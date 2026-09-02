/* ================================================
 * tickets.js — Support & Tickets Management System
 * ================================================ */

let currentPage = 1;
let debounceTimer = null;
let currentTicketsData = [];

document.addEventListener('DOMContentLoaded', () => {
    fetchTickets(1);

    const searchInput = document.getElementById('ticketSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchTickets(1);
            }, 300);
        });
    }

    const typeFilter = document.getElementById('typeFilter');
    if (typeFilter) {
        typeFilter.addEventListener('change', () => {
            fetchTickets(1);
        });
    }

    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            fetchTickets(1);
        });
    }
});

/**
 * Fetch KPI Stats and Paginated Support Tickets
 */
async function fetchTickets(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('ticketsTableBody');
    const emptyState = document.getElementById('ticketsEmptyState');
    const paginationWrapper = document.getElementById('ticketsPaginationWrapper');

    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500 italic">جاري تحميل تذاكر الدعم...</td></tr>`;

    const type = document.getElementById('typeFilter')?.value || 'all';
    const status = document.getElementById('statusFilter')?.value || 'all';
    const search = document.getElementById('ticketSearch')?.value || '';

    const url = new URL('/admin/api/support/tickets', window.location.origin);
    url.searchParams.set('page', page);
    url.searchParams.set('type', type);
    url.searchParams.set('status', status);
    if (search) url.searchParams.set('search', search);

    try {
        const response = await fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to fetch tickets');

        const result = await response.json();

        if (result.status) {
            renderKpi(result.kpi);
            currentTicketsData = result.data || [];
            renderTable(currentTicketsData);
            renderPagination(result.pagination);
        } else {
            throw new Error('Invalid response format');
        }
    } catch (err) {
        console.error('Fetch Tickets Error:', err);
        tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-8 text-center text-red-600 font-semibold">⚠️ تعذر تحميل بيانات التذاكر.</td></tr>`;
        if (emptyState) emptyState.classList.add('hidden-el');
        if (paginationWrapper) paginationWrapper.classList.add('hidden-el');
    }
}

/**
 * Render KPI Metric Summary Cards
 */
function renderKpi(kpi) {
    if (!kpi) return;

    const totalEl = document.getElementById('totalTicketsMetric');
    const complaintsEl = document.getElementById('pendingComplaintsMetric');
    const inquiriesEl = document.getElementById('openInquiriesMetric');
    const avgTimeEl = document.getElementById('avgResponseTimeMetric');

    if (totalEl) totalEl.innerText = Number(kpi.total_tickets).toLocaleString('ar-EG');
    if (complaintsEl) complaintsEl.innerText = Number(kpi.pending_complaints).toLocaleString('ar-EG');
    if (inquiriesEl) inquiriesEl.innerText = Number(kpi.open_inquiries).toLocaleString('ar-EG');
    if (avgTimeEl) avgTimeEl.innerText = kpi.avg_resolution_time;
}

/**
 * Render Tickets Data Table
 */
function renderTable(tickets) {
    const tbody = document.getElementById('ticketsTableBody');
    const emptyState = document.getElementById('ticketsEmptyState');
    const paginationWrapper = document.getElementById('ticketsPaginationWrapper');

    tbody.innerHTML = '';

    if (tickets.length === 0) {
        tbody.parentElement.classList.add('hidden-el');
        if (emptyState) emptyState.classList.remove('hidden-el');
        if (paginationWrapper) paginationWrapper.classList.add('hidden-el');
        return;
    }

    tbody.parentElement.classList.remove('hidden-el');
    if (emptyState) emptyState.classList.add('hidden-el');
    if (paginationWrapper) paginationWrapper.classList.remove('hidden-el');

    const badgeColors = [
        'bg-indigo-600 text-white',
        'bg-emerald-600 text-white',
        'bg-amber-600 text-white',
        'bg-purple-600 text-white',
        'bg-blue-600 text-white',
    ];

    tickets.forEach((t, idx) => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 transition-colors border-b';

        const user = t.customer_user || { name: 'عميل', phone: 'غير محدد', initials: 'ع', avatar: '' };
        const colorClass = badgeColors[idx % badgeColors.length];

        const avatarMarkup = user.avatar && !user.avatar.includes('ui-avatars')
            ? `<img class="w-9 h-9 rounded-full border border-gray-200 ml-3 object-cover" src="${user.avatar}" alt="${user.name}">`
            : `<div class="w-9 h-9 rounded-full ${colorClass} font-bold text-xs flex items-center justify-center ml-3 shadow-sm">${user.initials}</div>`;

        // Type Pill
        const isComplaint = t.type === 'complaint';
        const typeLabel = isComplaint ? 'شكوى' : 'استفسار';
        const typeClass = isComplaint
            ? 'bg-red-100 text-red-700 border-red-200'
            : 'bg-blue-100 text-blue-700 border-blue-200';

        // Status Badge Pill
        let statusLabel = 'قيد الانتظار';
        let statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';

        switch (t.status) {
            case 'in_progress':
                statusLabel = 'قيد المعالجة';
                statusClass = 'bg-blue-100 text-blue-800 border-blue-200';
                break;
            case 'resolved':
                statusLabel = 'تم الحل / الرد';
                statusClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                break;
            case 'rejected':
                statusLabel = 'مرفوضة';
                statusClass = 'bg-red-100 text-red-800 border-red-200';
                break;
            case 'closed':
                statusLabel = 'مغلقة';
                statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                break;
        }

        // State-Aware Action Button
        const isPendingOrInProgress = t.status === 'pending' || t.status === 'in_progress';
        const actionBtnMarkup = isPendingOrInProgress
            ? `<button type="button" onclick="openTicketModal(${t.id})" class="px-3.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-bold border border-indigo-200/80 transition-colors shadow-2xs">مراجعة ورد ←</button>`
            : `<button type="button" onclick="openTicketModal(${t.id})" class="px-3.5 py-1.5 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 text-xs font-bold border border-slate-200 transition-colors shadow-2xs">عرض التفاصيل</button>`;

        tr.innerHTML = `
            <td class="px-6 py-4 text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 tracking-wider">
                    #${t.ticket_code}
                </span>
            </td>
            <td class="px-6 py-4 text-right">
                <div class="flex items-center">
                    ${avatarMarkup}
                    <div>
                        <div class="font-bold text-gray-900">${user.name}</div>
                        <div class="text-xs text-gray-500 font-medium" dir="ltr">${user.phone}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-right">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border ${typeClass}">
                    ${typeLabel}
                </span>
            </td>
            <td class="px-6 py-4 text-right">
                <div class="font-bold text-gray-900 text-sm mb-0.5">${t.subject}</div>
                <div class="text-xs text-gray-500 truncate max-w-xs" title="${t.message}">${t.message}</div>
            </td>
            <td class="px-6 py-4 text-right text-xs text-gray-600 font-medium whitespace-nowrap">
                ${t.created_at}
            </td>
            <td class="px-6 py-4 text-right">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border ${statusClass}">
                    ${statusLabel}
                </span>
            </td>
            <td class="px-6 py-4 text-left">
                <div class="flex items-center justify-end gap-2">
                    ${actionBtnMarkup}
                    <button type="button" onclick="deleteTicket(${t.id})" class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-1.5 rounded-lg border border-transparent hover:border-rose-100 transition-colors delete-ticket-btn" title="حذف التذكرة">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </td>
        `;

        tbody.appendChild(tr);
    });
}

/**
 * Render Pagination Controls
 */
function renderPagination(pagination) {
    const info = document.getElementById('ticketsPaginationInfo');
    const nav = document.getElementById('ticketsPaginationNav');

    if (!info || !nav || !pagination) return;

    info.innerText = `عرض الصفحة ${pagination.current_page} من ${pagination.last_page} (${pagination.total} إجمالي العناصر)`;

    nav.innerHTML = '';

    if (pagination.last_page <= 1) return;

    // Previous Button
    const prevBtn = document.createElement('button');
    prevBtn.className = `px-3 py-1 text-xs rounded-lg border ${pagination.current_page > 1 ? 'bg-white text-gray-700 hover:bg-gray-100' : 'bg-gray-100 text-gray-400 cursor-not-allowed'}`;
    prevBtn.innerText = 'السابق';
    prevBtn.disabled = pagination.current_page <= 1;
    prevBtn.onclick = () => fetchTickets(pagination.current_page - 1);
    nav.appendChild(prevBtn);

    // Page Buttons
    for (let p = 1; p <= pagination.last_page; p++) {
        if (p === 1 || p === pagination.last_page || Math.abs(p - pagination.current_page) <= 2) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `px-3 py-1 text-xs rounded-lg border font-semibold ${p === pagination.current_page ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 hover:bg-gray-100'}`;
            pageBtn.innerText = p;
            pageBtn.onclick = () => fetchTickets(p);
            nav.appendChild(pageBtn);
        }
    }

    // Next Button
    const nextBtn = document.createElement('button');
    nextBtn.className = `px-3 py-1 text-xs rounded-lg border ${pagination.current_page < pagination.last_page ? 'bg-white text-gray-700 hover:bg-gray-100' : 'bg-gray-100 text-gray-400 cursor-not-allowed'}`;
    nextBtn.innerText = 'التالي';
    nextBtn.disabled = pagination.current_page >= pagination.last_page;
    nextBtn.onclick = () => fetchTickets(pagination.current_page + 1);
    nav.appendChild(nextBtn);
}

/**
 * Open Ticket Review & Response Modal
 */
function openTicketModal(ticketId) {
    const ticket = currentTicketsData.find(t => t.id === ticketId);
    if (!ticket) return;

    document.getElementById('modalTicketId').value = ticket.id;
    document.getElementById('modalTicketCode').innerText = `#${ticket.ticket_code}`;

    const isComplaint = ticket.type === 'complaint';
    const typeBadge = document.getElementById('modalTicketTypeBadge');
    if (typeBadge) {
        typeBadge.innerHTML = isComplaint
            ? `<span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span><span>شكوى</span>`
            : `<span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span><span>استفسار</span>`;
        typeBadge.className = isComplaint
            ? 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200/80 shadow-2xs'
            : 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs';
    }

    const user = ticket.customer_user || { name: 'عميل', phone: 'غير محدد', initials: 'ع', avatar: '' };
    document.getElementById('modalCustomerName').innerText = user.name;
    document.getElementById('modalCustomerPhone').innerText = user.phone;

    const avatarEl = document.getElementById('modalCustomerAvatar');
    if (avatarEl) {
        if (user.avatar && !user.avatar.includes('ui-avatars')) {
            avatarEl.innerHTML = `<img src="${user.avatar}" class="w-full h-full object-cover rounded-full" alt="${user.name}">`;
        } else {
            avatarEl.innerText = user.initials;
        }
    }

    document.getElementById('modalCategory').innerText = ticket.category || 'عام';
    document.getElementById('modalRelatedId').innerText = ticket.related_id || 'لا يوجد';
    document.getElementById('modalCreatedAt').innerText = ticket.created_at || '';
    document.getElementById('modalSubject').innerText = ticket.subject;
    document.getElementById('modalUserMessage').innerText = ticket.message;

    // Status Select
    const statusSelect = document.getElementById('modalStatusSelect');
    if (statusSelect) {
        if (ticket.status === 'pending' || ticket.type === 'inquiry') {
            statusSelect.value = 'resolved';
        } else {
            statusSelect.value = ticket.status;
        }
    }

    // Admin Response
    const responseInput = document.getElementById('modalAdminResponse');
    if (responseInput) {
        responseInput.value = ticket.admin_response || '';
    }

    // History Box
    const historyBox = document.getElementById('modalHistoryBox');
    if (ticket.admin_response && historyBox) {
        historyBox.classList.remove('hidden-el');
        document.getElementById('modalAdminName').innerText = ticket.admin_name || 'الإدارة';
        document.getElementById('modalRespondedAt').innerText = ticket.responded_at || '';
    } else if (historyBox) {
        historyBox.classList.add('hidden-el');
    }

    // Show Modal
    const modal = document.getElementById('ticketModal');
    if (modal) {
        modal.classList.remove('hidden-el');
    }
}

/**
 * Close Ticket Modal
 */
function closeTicketModal() {
    const modal = document.getElementById('ticketModal');
    if (modal) {
        modal.classList.add('hidden-el');
    }
}

/**
 * Submit Ticket Response & Status Update
 */
async function submitTicketResponse(event) {
    event.preventDefault();

    const ticketId = document.getElementById('modalTicketId').value;
    const status = document.getElementById('modalStatusSelect').value;
    const adminResponse = document.getElementById('modalAdminResponse').value;
    const btnSubmit = document.getElementById('btnSubmitResponse');

    if (!ticketId || !adminResponse) return;

    if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `<span>جاري التحديث...</span>`;
    }

    try {
        const response = await fetch(`/admin/api/support/tickets/${ticketId}/respond`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                status: status,
                admin_response: adminResponse
            })
        });

        const result = await response.json();

        if (response.ok && result.status) {
            closeTicketModal();
            fetchTickets(currentPage);
        } else {
            alert(result.message || 'حدث خطأ أثناء تحديث التذكرة.');
        }
    } catch (err) {
        console.error('Submit Response Error:', err);
        alert('تعذر الاتصال بالخادم، يرجى المحاولة لاحقاً.');
    } finally {
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = `<span>تحديث التذكرة وإرسال الرد</span>`;
        }
    }
}

/**
 * Delete Support Ticket (Soft Delete)
 */
async function deleteTicket(ticketId) {
    if (!ticketId) return;

    if (!confirm('هل أنت متأكد من حذف هذه التذكرة؟')) {
        return;
    }

    try {
        const response = await fetch(`/admin/api/support/tickets/${ticketId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const result = await response.json();

        if (response.ok && result.status) {
            fetchTickets(currentPage);
        } else {
            alert(result.message || 'حدث خطأ أثناء حذف التذكرة.');
        }
    } catch (err) {
        console.error('Delete Ticket Error:', err);
        alert('تعذر الاتصال بالخادم، يرجى المحاولة لاحقاً.');
    }
}
