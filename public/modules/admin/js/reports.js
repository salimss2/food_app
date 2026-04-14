/* ================================================
 * reports.js — Page-specific logic
 * ================================================ */

// Dummy table data
        let reportsData = [
            { id: "REP-9921", date: "June 2026", count: 1840, vol: 120500.50 },
            { id: "REP-9920", date: "May 2026", count: 1650, vol: 95420.00 },
            { id: "REP-9919", date: "April 2026", count: 1420, vol: 75200.75 },
            { id: "REP-9918", date: "March 2026", count: 910, vol: 51000.00 },
        ];

        let sortAsc = true;
// Search logic
        document.getElementById('reportSearch').addEventListener('input', renderTable);

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

        function sortData() {
            sortAsc = !sortAsc;
            reportsData.sort((a,b) => sortAsc ? a.id.localeCompare(b.id) : b.id.localeCompare(a.id));
            renderTable();
        }

        function renderTable() {
            const search = document.getElementById('reportSearch').value.toLowerCase();
            const tbody = document.getElementById('reportsTable');
            tbody.innerHTML = "";

            let display = reportsData;
            if(search) {
                display = display.filter(w => w.id.toLowerCase().includes(search));
            }

            display.forEach(r => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4 font-bold text-indigo-600">${r.id}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">${r.date}</td>
                    <td class="px-6 py-4 text-right text-gray-500">${r.count.toLocaleString()}</td>
                    <td class="px-6 py-4 text-right font-bold text-gray-900">${fixCurrency(r.vol)}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <button onclick="showToast('Downloading Ledger for ${r.id}...')" class="text-gray-500 hover:text-indigo-600 font-medium text-sm flex items-center justify-end w-full">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Download
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        renderTable();
