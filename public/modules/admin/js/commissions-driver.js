/* ================================================
 * commissions-driver.js — Page-specific logic
 * ================================================ */

// Dummy Data - Driver Comm
        let commData = [
            { id: "D01", name: "Ahmed Yasin", deliveries: 120, revenue: 840.00, commPercent: 10 },
            { id: "D02", name: "John Doe", deliveries: 85, revenue: 610.50, commPercent: 10 },
            { id: "D03", name: "Omar Ali", deliveries: 45, revenue: 320.00, commPercent: 10 },
            { id: "D04", name: "Mike Smith", deliveries: 210, revenue: 1540.00, commPercent: 8 } // Elite driver rate
        ];
// Search logic
        document.getElementById('driverSearch').addEventListener('input', renderTable);

        function fixCurrency(num) {
            return '$' + parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function viewBreakdown(name) {
            document.getElementById('breakdownTitle').innerText = `${name} - Delivery Breakdown`;
            document.getElementById('breakdownModal').classList.remove('hidden-el');
        }

        function renderTable() {
            const search = document.getElementById('driverSearch').value.toLowerCase();
            const tbody = document.getElementById('driverTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            let display = commData;
            if(search) {
                display = display.filter(d => d.name.toLowerCase().includes(search));
            }

            if(display.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            display.forEach(d => {
                const commAmount = d.revenue * (d.commPercent / 100);
                const netAmount = d.revenue - commAmount;

                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4 font-bold text-gray-900 flex items-center">
                        <img class="w-8 h-8 rounded-full border border-gray-200 mr-3" src="https://ui-avatars.com/api/?name=${d.name.replace(' ', '+')}&color=fff&background=4f46e5">
                        ${d.name}
                    </td>
                    <td class="px-6 py-4 text-center font-medium text-gray-600">${d.deliveries}</td>
                    <td class="px-6 py-4 text-right font-medium text-gray-900">${fixCurrency(d.revenue)}</td>
                    <td class="px-6 py-4 text-center font-bold bg-gray-50 border-x border-gray-100">${d.commPercent}%</td>
                    <td class="px-6 py-4 text-right font-bold text-red-600 bg-red-50/20">-${fixCurrency(commAmount)}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600 bg-green-50/20">${fixCurrency(netAmount)}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <button onclick="viewBreakdown('${d.name}')" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">View Deliveries</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        renderTable();
