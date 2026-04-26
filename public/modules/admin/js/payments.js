/* ================================================
 * payments.js — Page-specific logic
 * ================================================ */

'use strict';

// ── State ──────────────────────────────────────────────────────────────────
let actionTarget = { action: null, id: null };
let debounceTimer = null;

// ── Image Preview ───────────────────────────────────────────────────────────
function openImagePreview(src, orderId) {
    document.getElementById('fullImage').src = src;
    document.getElementById('previewTitle').innerText = `Payment Proof — Order #${orderId}`;
    document.getElementById('imageModal').classList.remove('hidden-el');
}

// ── Tab Switching ───────────────────────────────────────────────────────────
function switchTab(tab) {
    // Update tab button styles
    ['all', 'pending_refund'].forEach(t => {
        const el = document.getElementById(`tab-${t}`);
        if (!el) return;
        const isActive = t === tab;
        el.className = el.className
            .replace(/border-primary|border-transparent|text-primary|text-gray-500/g, '')
            .trim();
        if (isActive) {
            el.classList.add('border-primary', 'text-primary');
            el.classList.remove('border-transparent', 'text-gray-500');
        } else {
            el.classList.add('border-transparent', 'text-gray-500');
            el.classList.remove('border-primary', 'text-primary');
        }
    });

    // Set payment status filter to match the tab, then fetch
    const paymentStatusEl = document.getElementById('ajaxStatusFilter');
    if (paymentStatusEl) {
        paymentStatusEl.value = (tab === 'pending_refund') ? 'pending_refund' : 'all';
    }

    fetchFilteredPayments();
}

// ── Unified Filter Fetch ────────────────────────────────────────────────────
function fetchFilteredPayments() {
    const loading   = document.getElementById('loadingIndicator');
    const tableBody = document.getElementById('paymentsTableBody');

    const params = new URLSearchParams({
        payment_status: getValue('ajaxStatusFilter', 'all'),
        order_status:   getValue('filterOrderStatus', 'all'),
        from_date:      getValue('filterFromDate', ''),
        to_date:        getValue('filterToDate', ''),
        min_amount:     getValue('filterMinAmount', ''),
    });

    // Remove empty params
    for (const [key, val] of [...params.entries()]) {
        if (!val || val === 'all') params.delete(key);
    }

    if (loading)   loading.classList.remove('hidden');
    if (loading)   loading.classList.add('flex');
    if (tableBody) tableBody.style.opacity = '0.5';

    fetch(`/admin/payments/filter?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
    .then(res => res.ok ? res.text() : Promise.reject(res))
    .then(html => {
        if (tableBody) {
            tableBody.innerHTML = html;
            tableBody.style.opacity = '1';
        }
    })
    .catch(err => console.error('Filter fetch failed:', err))
    .finally(() => {
        if (loading) { loading.classList.add('hidden'); loading.classList.remove('flex'); }
        if (tableBody) tableBody.style.opacity = '1';
    });
}

function getValue(id, fallback = '') {
    const el = document.getElementById(id);
    return el ? el.value : fallback;
}

// ── Approve / Reject flow ───────────────────────────────────────────────────
function requestAction(action, id) {
    actionTarget = { action, id };
    const modalBody   = document.getElementById('confirmModalBody');
    const modalFooter = document.getElementById('confirmModalFooter');

    if (action === 'Approve') {
        modalBody.innerHTML = `
            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </div>
            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                <h3 class="text-lg font-semibold leading-6 text-gray-900">Approve Payment</h3>
                <p class="mt-2 text-sm text-gray-500">Approve order <strong>#${id}</strong>? This updates the status and notifies drivers.</p>
            </div>`;
        modalFooter.innerHTML = `
            <button type="button" onclick="processAction()" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto transition-colors">Approve</button>
            <button type="button" onclick="closeModal('confirmModal')" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Cancel</button>`;
    } else {
        modalBody.innerHTML = `
            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="mt-3 text-center w-full sm:ml-4 sm:mt-0 sm:text-left">
                <h3 class="text-lg font-semibold leading-6 text-gray-900">Reject Payment</h3>
                <p class="mt-2 text-sm text-gray-500">Reject payment for order <strong>#${id}</strong>?</p>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Rejection Reason <span class="text-red-500">*</span></label>
                    <textarea id="rejectionReasonInput" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm border p-2" placeholder="Provide a reason..."></textarea>
                    <p id="rejectionError" class="mt-1 text-sm text-red-600 hidden-el">Rejection reason is required.</p>
                </div>
            </div>`;
        modalFooter.innerHTML = `
            <button id="rejectSubmitBtn" type="button" onclick="processAction()" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">Reject</button>
            <button type="button" onclick="closeModal('confirmModal')" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Cancel</button>`;
    }

    document.getElementById('confirmModal').classList.remove('hidden-el');
}

async function processAction() {
    if (!actionTarget.id) return;
    const { id, action } = actionTarget;
    const isApprove = action === 'Approve';
    let bodyData = {};

    if (!isApprove) {
        const reasonInput = document.getElementById('rejectionReasonInput');
        const errorMsg    = document.getElementById('rejectionError');
        const reason      = reasonInput.value.trim();
        if (!reason) {
            reasonInput.classList.add('border-red-500');
            errorMsg.classList.remove('hidden-el');
            return;
        }
        bodyData.rejection_reason = reason;
    }

    await sendPatchRequest(`/admin/payments/${id}/${isApprove ? 'approve' : 'reject'}`, bodyData, 'confirmModal');
}

// ── Cancel Order flow ───────────────────────────────────────────────────────
function requestCancel(id) {
    document.getElementById('cancelOrderId').value = id;
    document.getElementById('cancellationReasonInput').value = '';
    document.getElementById('cancellationReasonInput').classList.remove('border-red-500');
    document.getElementById('cancellationReasonError').classList.add('hidden-el');
    document.getElementById('cancelOrderModal').classList.remove('hidden-el');
}

async function submitCancelOrder() {
    const id     = document.getElementById('cancelOrderId').value;
    const input  = document.getElementById('cancellationReasonInput');
    const reason = input.value.trim();

    if (!reason) {
        input.classList.add('border-red-500');
        document.getElementById('cancellationReasonError').classList.remove('hidden-el');
        return;
    }

    await sendPatchRequest(`/admin/payments/${id}/cancel`, { cancellation_reason: reason }, 'cancelOrderModal');
}

// ── Refund flow ─────────────────────────────────────────────────────────────
async function requestRefund(id) {
    if (!confirm('Mark this order as refunded?')) return;
    await sendPatchRequest(`/admin/payments/${id}/refund`, {});
}

// ── Shared PATCH helper ─────────────────────────────────────────────────────
async function sendPatchRequest(endpoint, bodyData, modalIdToClose = null) {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    try {
        const res  = await fetch(endpoint, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify(bodyData),
        });
        const data = await res.json();

        if (res.ok && data.success) {
            if (modalIdToClose) closeModal(modalIdToClose);
            fetchFilteredPayments();
        } else {
            alert(data.message || 'An error occurred. Please try again.');
        }
    } catch (e) {
        console.error('Request failed:', e);
        alert('A network error occurred. Please try again.');
    }
}

// ── Event listeners (DOMContentLoaded) ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

    // Dropdown filters — instant onChange
    ['ajaxStatusFilter', 'filterOrderStatus'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', fetchFilteredPayments);
    });

    // Date inputs — instant onChange
    ['filterFromDate', 'filterToDate'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', fetchFilteredPayments);
    });

    // Amount input — debounced (500 ms)
    const amountEl = document.getElementById('filterMinAmount');
    if (amountEl) {
        amountEl.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchFilteredPayments, 500);
        });
    }
});
