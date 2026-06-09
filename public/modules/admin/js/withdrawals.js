/* ================================================
 * withdrawals.js — Page-specific logic
 * ================================================ */

// Dummy Data - Withdrawals
        let wRequests = [
            { id: "WD-001", name: "Pizza Hut", type: "Restaurant", date: "2026-04-05", amount: 1205.50, status: "Pending" },
            { id: "WD-002", name: "Ahmed Yasin", type: "Driver", date: "2026-04-05", amount: 840.00, status: "Pending" },
            { id: "WD-003", name: "Burger King", type: "Restaurant", date: "2026-04-03", amount: 4500.00, status: "Approved" },
            { id: "WD-004", name: "John Doe", type: "Driver", date: "2026-04-01", amount: 120.00, status: "Rejected" }
        ];
// Search logic
        document.getElementById('wdSearch').addEventListener('input', renderTable);

        // Toast Helper
        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            
            if(isError) {
                toast.classList.replace('bg-green-50', 'bg-red-50');
                toast.classList.replace('border-green-200', 'border-red-200');
                document.getElementById('toastMessage').classList.replace('text-green-800', 'text-red-800');
            } else {
                toast.classList.replace('bg-red-50', 'bg-green-50');
                toast.classList.replace('border-red-200', 'border-green-200');
                document.getElementById('toastMessage').classList.replace('text-red-800', 'text-green-800');
            }

            toast.classList.remove('hidden-el');
            setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
        }

        function fixCurrency(num) {
            return '$' + parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function handleAction(id, actionStr) {
            if(confirm(`Are you sure you want to ${actionStr} this payout request?`)) {
                const target = wRequests.find(w => w.id === id);
                if(target) {
                    target.status = actionStr === 'Approve' ? 'Approved' : 'Rejected';
                    showToast(`Request ${target.status}!`, actionStr !== 'Approve');
                    renderTable();
                }
            }
        }

        function renderTable() {
            const filter = document.getElementById('wdFilter').value;
            const searchObj = document.getElementById('wdSearch').value.toLowerCase();
            const tbody = document.getElementById('wdTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            let display = wRequests;
            
            if(filter !== 'All') { display = display.filter(w => w.status === filter); }
            if(searchObj) {
                display = display.filter(w => w.name.toLowerCase().includes(searchObj));
            }

            if(display.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            display.forEach(w => {
                let badge = "";
                if(w.status === 'Pending') badge = '<span class="inline-flex flex-shrink-0 items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>';
                else if(w.status === 'Approved') badge = '<span class="inline-flex flex-shrink-0 items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Approved</span>';
                else badge = '<span class="inline-flex flex-shrink-0 items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Rejected</span>';

                let actionsHTML = "";
                if(w.status === 'Pending') {
                    actionsHTML = `
                        <button onclick="handleAction('${w.id}', 'Approve')" class="text-green-600 hover:text-green-900 font-medium text-sm mr-3 bg-green-50 px-2 py-1 rounded">Approve</button>
                        <button onclick="handleAction('${w.id}', 'Reject')" class="text-red-600 hover:text-red-900 font-medium text-sm bg-red-50 px-2 py-1 rounded">Reject</button>
                    `;
                } else {
                    actionsHTML = `<span class="text-gray-400 text-xs italic">Processed</span>`;
                }

                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4 font-bold text-gray-900">${w.name}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase">${w.type}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">${w.date}</td>
                    <td class="px-6 py-4 text-right font-bold text-gray-900">
                        ${fixCurrency(w.amount)}
                    </td>
                    <td class="px-6 py-4">${badge}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        ${actionsHTML}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Init
        renderTable();
