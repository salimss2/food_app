/* ================================================
 * revenue.js — Page-specific logic
 * ================================================ */

// Dummy Data - Revenue Transactions
        let txns = [
            { id: "TXN-88029", type: "Inbound", entity: "Customer Order #5012", amount: 45.50, date: "April 06, 2026", status: "Settled" },
            { id: "TXN-88028", type: "Outbound", entity: "Payout - Pizza Hut", amount: 1500.00, date: "April 05, 2026", status: "Settled" },
            { id: "TXN-88027", type: "Inbound", entity: "Customer Order #5011", amount: 12.00, date: "April 05, 2026", status: "Settled" },
            { id: "TXN-88026", type: "Outbound", entity: "Payout - Ahmed Yasin", amount: 240.00, date: "April 03, 2026", status: "Failed" },
            { id: "TXN-88025", type: "Inbound", entity: "Customer Order #5010", amount: 80.00, date: "April 02, 2026", status: "Settled" }
        ];
// Search logic
        document.getElementById('revSearch').addEventListener('input', renderTable);

        // Toast Helper
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('hidden-el');
            setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
        }

        function fixCurrency(num) {
            return '$' + parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function forceRetry(id) {
            showToast(`Initiating manual retry for ${id}...`);
            setTimeout(() => {
                const target = txns.find(t => t.id === id);
                if(target) { target.status = "Settled"; renderTable(); showToast('Retry successful.'); }
            }, 1000);
        }

        function renderTable() {
            const filter = document.getElementById('typeFilter').value;
            const searchObj = document.getElementById('revSearch').value.toLowerCase();
            const tbody = document.getElementById('revTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            let display = txns;
            
            if(filter !== 'All') { display = display.filter(w => w.type === filter); }
            if(searchObj) {
                display = display.filter(w => w.id.toLowerCase().includes(searchObj) || w.entity.toLowerCase().includes(searchObj));
            }

            if(display.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            display.forEach(t => {
                const isInc = t.type === 'Inbound';
                let amountHTML = isInc 
                    ? `<span class="text-green-600 font-bold">+${fixCurrency(t.amount)}</span>`
                    : `<span class="text-gray-900 font-bold">-${fixCurrency(t.amount)}</span>`;
                
                let typeHTML = isInc
                    ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-50 text-green-700 border border-green-100">Inbound</span>`
                    : `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">Outbound</span>`;

                if(t.status === 'Failed') {
                    typeHTML = `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-700 border border-red-100">Failed Payout</span>`;
                }

                let actionsHTML = t.status === 'Failed' 
                    ? `<button onclick="forceRetry('${t.id}')" class="text-red-600 hover:text-red-900 font-bold text-sm">Force Retry</button>`
                    : `<span class="text-gray-400 text-xs italic">Settled</span>`;

                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4 font-bold text-indigo-600">${t.id}</td>
                    <td class="px-6 py-4">${typeHTML}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">${t.entity}</td>
                    <td class="px-6 py-4 text-right">${amountHTML}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${t.date}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        ${actionsHTML}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Init
        renderTable();
