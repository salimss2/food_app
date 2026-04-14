/* ================================================
 * payments.js — Page-specific logic
 * ================================================ */

// Dummy Data
        let pendingPayments = [
            { id: "ORD-9821", name: "Ahmed Fathi", amount: 150.00, date: "2026-04-05", proofImg: "https://placehold.co/600x800", status: "Pending" },
            { id: "ORD-9822", name: "Sara AlSaud", amount: 420.50, date: "2026-04-04", proofImg: "https://placehold.co/600x800", status: "Pending" },
            { id: "ORD-9824", name: "Mohammed Ali", amount: 85.00,  date: "2026-04-05", proofImg: "https://placehold.co/600x800", status: "Pending" }
        ];

        let actionTarget = { action: null, id: null }; // action: 'Approve' | 'Reject'
// Modals
        function openImagePreview(src, orderId) {
            document.getElementById('fullImage').src = src;
            document.getElementById('previewTitle').innerText = `Payment Proof - ${orderId}`;
            document.getElementById('imageModal').classList.remove('hidden-el');
        }

        // Render Table
        function renderTable(payments = pendingPayments) {
            const tbody = document.getElementById('paymentsTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";
            document.getElementById('sidebarBadge').innerText = pendingPayments.length;

            if(payments.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            payments.forEach(p => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-yellow-50 bg-white transition-colors border-l-4 border-yellow-400"; // Highlighted row
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">${p.id}</div>
                        <div class="text-xs text-gray-500">${p.date}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${p.name}</td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-green-600">$${p.amount.toFixed(2)}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <img src="${p.proofImg}" alt="Proof" onclick="openImagePreview('${p.proofImg}', '${p.id}')" class="h-10 w-10 object-cover rounded mx-auto cursor-pointer border border-gray-300 hover:opacity-80 transition-opacity ring-2 ring-transparent hover:ring-primary">
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            ${p.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <button onclick="requestAction('Approve', '${p.id}')" class="bg-green-100 hover:bg-green-200 text-green-800 font-bold py-1.5 px-3 rounded shadow-sm mr-2 text-xs transition-colors">Approve</button>
                        <button onclick="requestAction('Reject', '${p.id}')" class="bg-red-100 hover:bg-red-200 text-red-800 font-bold py-1.5 px-3 rounded shadow-sm text-xs transition-colors">Reject</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Action Handling
        function requestAction(action, id) {
            actionTarget = { action, id };
            const modalBody = document.getElementById('confirmModalBody');
            const modalFooter = document.getElementById('confirmModalFooter');
            
            if(action === 'Approve') {
                modalBody.innerHTML = `
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900">Approve Payment</h3>
                        <div class="mt-2 text-sm text-gray-500">Are you sure you want to approve order ${id}? This will update the user's wallet.</div>
                    </div>`;
                modalFooter.innerHTML = `
                    <button type="button" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto" onclick="processAction()">Approve</button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal('confirmModal')">Cancel</button>`;
            } else {
                modalBody.innerHTML = `
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900">Reject Payment</h3>
                        <div class="mt-2 text-sm text-gray-500">Are you sure you want to reject payment for ${id}? The customer will be notified.</div>
                    </div>`;
                modalFooter.innerHTML = `
                    <button type="button" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto" onclick="processAction()">Reject</button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal('confirmModal')">Cancel</button>`;
            }
            
            document.getElementById('confirmModal').classList.remove('hidden-el');
        }

        function processAction() {
            if(actionTarget.id) {
                // Remove from pending list
                pendingPayments = pendingPayments.filter(p => p.id !== actionTarget.id);
                applyFilters(); // Re-render with applied filters
            }
            closeModal('confirmModal');
        }

        function applyFilters() {
            const date = document.getElementById('filterDate').value;
            const amount = parseFloat(document.getElementById('filterAmount').value);
            
            let filtered = pendingPayments;
            
            if(date) {
                filtered = filtered.filter(p => p.date === date);
            }
            if(!isNaN(amount)) {
                filtered = filtered.filter(p => p.amount >= amount);
            }
            
            renderTable(filtered);
        }

        // Init
        renderTable();
