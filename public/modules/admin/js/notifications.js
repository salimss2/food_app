/* ================================================
 * notifications.js — Page-specific logic
 * ================================================ */

// Dummy Data - ACTIVE Broadcasts (Recently sent)
        let sentNotifications = [
            { id: "NOT-001", title: "System Maintenance", message: "Servers will be down for 10 minutes at 3 AM.", target: "All", type: "Push", date: "Today, 08:00 AM" },
            { id: "NOT-002", title: "New Driver Bonus", message: "Complete 10 deliveries today to get a $20 bonus!", target: "Drivers", type: "Push", date: "Yesterday, 07:30 AM" },
            { id: "NOT-003", title: "Monthly Newsletter", message: "Check out the top performing restaurants of the month...", target: "Restaurants", type: "Email", date: "April 01, 2026, 12:00 PM" }
        ];
// Search listener
        document.getElementById('notifSearch').addEventListener('input', renderNotifications);

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('hidden-el');
            setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
        }

        // Render Table
        function renderNotifications() {
            const filter = document.getElementById('notifFilter').value;
            const searchObj = document.getElementById('notifSearch').value.toLowerCase();
            const tbody = document.getElementById('notifTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            let displayNotifs = sentNotifications;
            
            if(filter !== 'All') {
                displayNotifs = displayNotifs.filter(n => n.target === filter);
            }
            if(searchObj) {
                displayNotifs = displayNotifs.filter(n => n.title.toLowerCase().includes(searchObj) || n.message.toLowerCase().includes(searchObj));
            }

            if(displayNotifs.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            displayNotifs.forEach(n => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">${n.title}</div>
                        <div class="text-xs text-gray-500">${n.date}</div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-500 truncate max-w-xs">${n.message}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${n.target}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-semibold text-gray-600 uppercase">${n.type}</span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <button onclick="resendNotif('${n.id}')" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Resend</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function handleSendNotification(e) {
            e.preventDefault();
            const title = document.getElementById('notifTitle').value;
            const target = document.getElementById('notifTarget').value;
            const message = document.getElementById('notifMessage').value;
            const type = document.querySelector('input[name="notifType"]:checked').value;

            const newNotif = {
                id: "NOT-" + Math.floor(Math.random() * 1000),
                title: title,
                message: message,
                target: target,
                type: type,
                date: "Just now"
            };

            sentNotifications.unshift(newNotif);
            document.getElementById('sendNotifModal').classList.add('hidden-el');
            document.getElementById('notifForm').reset();
            renderNotifications();
            showToast('Notification broadcasted successfully.');
        }

        function resendNotif(id) {
            const target = sentNotifications.find(n => n.id === id);
            if(target && confirm("Do you want to re-broadcast this notification?")) {
                showToast('Notification re-sent successfully.');
            }
        }

        // Init
        renderNotifications();
