/* ================================================
 * driver-details.js — Page-specific logic
 * ================================================ */

// Dummy Data Arrays
const dummyOrders = [
    { id: "ORD-1002", date: "Oct 24, 2023 - 14:30", rest: "Burger King", cust: "Ali S.", amt: "$24.50", status: "Delivered" },
    { id: "ORD-1001", date: "Oct 24, 2023 - 12:15", rest: "KFC", cust: "Mona R.", amt: "$18.00", status: "Delivered" },
    { id: "ORD-1000", date: "Oct 23, 2023 - 20:45", rest: "Pizza Hut", cust: "Omar K.", amt: "$42.00", status: "Cancelled" },
    { id: "ORD-0999", date: "Oct 23, 2023 - 19:10", rest: "Subway", cust: "Layla M.", amt: "$12.50", status: "Delivered" },
    { id: "ORD-0998", date: "Oct 23, 2023 - 13:05", rest: "Shawarma House", cust: "Tariq Z.", amt: "$8.00", status: "Delivered" },
];

const dummyTransactions = [
    { date: "Oct 24, 2023", id: "ORD-1002", amt: 24.50, comm: 2.45 },
    { date: "Oct 24, 2023", id: "ORD-1001", amt: 18.00, comm: 1.80 },
    { date: "Oct 23, 2023", id: "ORD-0999", amt: 12.50, comm: 1.25 },
    { date: "Oct 23, 2023", id: "ORD-0998", amt: 8.00, comm: 0.80 },
];

const dummyReviews = [
    { name: "Ali S.", rating: 5, date: "2 days ago", comment: "Very fast and polite driver. Arrived earlier than expected!" },
    { name: "Mona R.", rating: 4, date: "3 days ago", comment: "Food was warm and handled well. Good service." },
    { name: "Omar K.", rating: 5, date: "1 week ago", comment: "Excellent experience. Knows the location well." },
];

// Shared Logic
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const sidebar = document.getElementById('sidebar');
const sidebarBackdrop = document.getElementById('sidebarBackdrop');

mobileMenuBtn.addEventListener('click', () => {
    sidebar.classList.remove('-translate-x-full');
    sidebarBackdrop.classList.remove('hidden-el');
});

sidebarBackdrop.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    sidebarBackdrop.classList.add('hidden-el');
});

// Tabs Logic
function switchTab(tabId) {
    // Remove active from all tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('text-gray-500', 'border-transparent');
    });
    // Hide all content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden-el');
    });

    // Activate chosen tab
    const targetBtn = document.getElementById(`tab-${tabId}`);
    targetBtn.classList.add('active');
    targetBtn.classList.remove('text-gray-500', 'border-transparent');
    // Show content
    document.getElementById(`content-${tabId}`).classList.remove('hidden-el');
}

// Modals
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden-el');
    if (modalId === 'notificationModal') {
        document.getElementById('notificationForm').reset();
    }
}

function openImageModal(src, title) {
    document.getElementById('imageModalSrc').src = src;
    document.getElementById('imageModalTitle').innerText = title;
    openModal('imageModal');
}

function openTimeline(orderId) {
    document.getElementById('timelineOrderId').innerText = orderId;
    openModal('timelineModal');
}

// Toasts
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type === 'success' ? 'border-green-500' : 'border-red-500'}`;

    const iconHTML = type === 'success'
        ? `<svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`
        : `<svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;

    toast.innerHTML = `
                ${iconHTML}
                <div class="text-sm font-medium text-gray-800">${message}</div>
            `;
    container.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 3000);
}

// Actions
function sendNotification(e) {
    e.preventDefault();
    closeModal('notificationModal');
    showToast("Notification sent to driver successfully!");
}

function toggleStatus() {
    const textEl = document.getElementById('statusActionText');
    const btnEl = document.getElementById('statusToggleBtn');
    const circle = btnEl.querySelector('div');

    if (textEl.innerText === 'Block Driver') {
        textEl.innerText = 'Unblock Driver';
        textEl.classList.add('text-red-600');
        btnEl.classList.replace('bg-green-400', 'bg-red-400');
        circle.style.transform = 'translateX(-16px)';
        showToast("Driver account blocked.", "error");
    } else {
        textEl.innerText = 'Block Driver';
        textEl.classList.remove('text-red-600');
        btnEl.classList.replace('bg-red-400', 'bg-green-400');
        circle.style.transform = 'translateX(0)';
        showToast("Driver account activated.");
    }
}

function passwordReset() {
    if (confirm('Are you sure you want to reset password for this driver?')) {
        showToast("Password reset link sent to driver's email/phone.");
    }
}

function processPayout() {
    showToast("Payout of $1,320.00 processed successfully!");
}

// Initial Data Rendezvous
function initData() {
    // Populate Orders Table
    const ordersTbody = document.getElementById('ordersTableBody');
    if (ordersTbody) {
        ordersTbody.innerHTML = ''; // Let Blade do the initial rendering or handle fallback gracefully
    }

    // Populate Financial Table
    const finTbody = document.getElementById('financialTableBody');
    if (finTbody) {
        dummyTransactions.forEach(t => {
            const net = (t.amt - t.comm).toFixed(2);
            finTbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">${t.date}</td>
                            <td class="px-6 py-4 font-medium">${t.id}</td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">$${t.amt.toFixed(2)}</td>
                            <td class="px-6 py-4 text-right text-red-600">-$${t.comm.toFixed(2)}</td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">$${net}</td>
                        </tr>
                    `;
        });
    }

    // Populate Reviews
    const rContainer = document.getElementById('reviewsContainer');
    if (rContainer) {
        dummyReviews.forEach(r => {
            let stars = '';
            for (let i = 0; i < 5; i++) {
                if (i < r.rating) {
                    stars += `<svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>`;
                } else {
                    stars += `<svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>`;
                }
            }

            rContainer.innerHTML += `
                        <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 mr-3">${r.name.charAt(0)}</div>
                                    <div>
                                        <h5 class="text-sm font-semibold text-gray-900">${r.name}</h5>
                                        <div class="flex">${stars}</div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">${r.date}</span>
                            </div>
                            <p class="text-sm text-gray-600 ml-11 italic">"${r.comment}"</p>
                        </div>
                    `;
        });
    }
}

initData();
