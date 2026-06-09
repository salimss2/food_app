/* ================================================
 * notification-history.js — Page-specific logic
 * ================================================ */

// Dummy Data - HISTORY Broadcasts
        let histNotifications = [
            { id: "HNOT-301", title: "Welcome to New Version", target: "All", type: "Push", date: "March 15, 2026, 08:00", status: "Delivered", deliveries: "15,203" },
            { id: "HNOT-302", title: "App Blackout Fix Updates", target: "Restaurants", type: "Email", date: "March 20, 2026, 12:30", status: "Delivered", deliveries: "120" },
            { id: "HNOT-303", title: "Severe Weather Warning", target: "Drivers", type: "Push", date: "March 25, 2026, 17:00", status: "Delivered", deliveries: "305" },
            { id: "HNOT-304", title: "Flash Sale Server Load test", target: "Users", type: "Push", date: "April 01, 2026, 09:00", status: "Failed", deliveries: "0" }
        ];
// Search listener
        document.getElementById('histSearch').addEventListener('input', renderTable);

        // Render Table
        function renderTable() {
            const searchObj = document.getElementById('histSearch').value.toLowerCase();
            const tbody = document.getElementById('histTableBody');
            tbody.innerHTML = "";

            let displayNotifs = histNotifications;
            
            if(searchObj) {
                displayNotifs = displayNotifs.filter(n => n.title.toLowerCase().includes(searchObj));
            }

            document.getElementById('histCount').innerText = displayNotifs.length;

            displayNotifs.forEach(n => {
                const badge = n.status === 'Delivered' 
                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Delivered</span>'
                    : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>';

                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">${n.date}</div>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900">${n.title}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-medium text-gray-500 uppercase">${n.target}</span>
                    </td>
                    <td class="px-6 py-4">
                        ${badge}
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-bold text-gray-600">
                        ${n.deliveries}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Init
        renderTable();
