/* ================================================
 * scheduled-notifications.js — Page-specific logic
 * ================================================ */

// Dummy Data - SCHEDULED Broadcasts
        let schedNotifications = [
            { id: "SNOT-201", title: "Upcoming Holiday Promo", target: "All", type: "Push", date: "April 15, 2026", time: "10:00 AM" },
            { id: "SNOT-202", title: "Quarterly Performance Report", target: "Restaurants", type: "Email", date: "April 30, 2026", time: "09:00 AM" }
        ];
// Render Table
        function renderTable() {
            const tbody = document.getElementById('schedTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            if(schedNotifications.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            schedNotifications.forEach(n => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-1 rounded inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            ${n.date} - ${n.time}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-900">${n.title}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${n.target}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-semibold text-gray-600 uppercase">${n.type}</span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <button onclick="cancelSched('${n.id}')" class="text-red-500 hover:text-red-700 font-medium text-sm">Cancel</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function cancelSched(id) {
            if(confirm("Cancel this scheduled notification?")) {
                schedNotifications = schedNotifications.filter(n => n.id !== id);
                renderTable();
            }
        }

        // Init
        renderTable();
