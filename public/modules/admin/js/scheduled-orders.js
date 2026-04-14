/* ================================================
 * scheduled-orders.js — Page-specific logic
 * ================================================ */

// Scheduled Orders Dummy Data
        let scheduledOrders = [
            { id: "ORD-SC-01", date: "April 10, 2026", time: "18:00 (06:00 PM)", customerName: "Elena Rodriguez", customerPhone: "+1 555 123 4567", address: "789 Pine Ave, Suite 3B", restaurant: "Mama's Italian", driver: "Not assigned yet", total: 65.50, items: [{name: "Spaghetti Bolognese", qty: 2, price: 20.00}, {name: "Tiramisu", qty: 1, price: 8.50}, {name: "Garlic Bread", qty: 1, price: 17.00}] },
            { id: "ORD-SC-02", date: "April 10, 2026", time: "19:30 (07:30 PM)", customerName: "James Wilson", customerPhone: "+1 555 987 6543", address: "404 Ocean Drive", restaurant: "Seafood Paradise", driver: "Not assigned yet", total: 110.00, items: [{name: "Grilled Salmon", qty: 2, price: 40.00}, {name: "Crab Cakes", qty: 1, price: 30.00}] },
            { id: "ORD-SC-03", date: "April 11, 2026", time: "12:00 (12:00 PM)", customerName: "TechCorp Office", customerPhone: "+1 555 444 3333", address: "100 Silicon Blvd, Floor 5", restaurant: "Salad Station", driver: "Not assigned yet", total: 145.00, items: [{name: "Caesar Salad Combo", qty: 10, price: 10.00}, {name: "Fresh Juice Assorted", qty: 10, price: 4.50}] }
        ];

        let selectedOrderId = null;
        // Processing Data
        function renderGroups() {
            const container = document.getElementById('scheduledGroupsContainer');
            container.innerHTML = "";
            
            // Group by date
            const grouped = scheduledOrders.reduce((acc, order) => {
                if(!acc[order.date]) acc[order.date] = [];
                acc[order.date].push(order);
                return acc;
            }, {});

            if(Object.keys(grouped).length === 0) {
                container.innerHTML = `<div class="p-12 text-center text-gray-500">No scheduled orders found.</div>`;
                return;
            }

            for(const date in grouped) {
                const groupDiv = document.createElement('div');
                
                let orderRows = "";
                grouped[date].forEach(order => {
                    orderRows += `
                        <tr class="hover:bg-gray-50 border-b border-gray-100 last:border-none">
                            <td class="px-6 py-4">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-1 rounded inline-flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    ${order.time}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-sm text-gray-900">${order.id}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">${order.customerName}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">${order.restaurant}</td>
                            <td class="px-6 py-4 font-bold text-gray-900">$${order.total.toFixed(2)}</td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="viewOrder('${order.id}')" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm mr-4">Details</button>
                                <button onclick="cancelSchedule('${order.id}')" class="text-red-500 hover:text-red-700 font-semibold text-sm">Cancel</button>
                            </td>
                        </tr>
                    `;
                });

                groupDiv.innerHTML = `
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-800 border-l-4 border-primary pl-3">${date} <span class="text-sm font-normal text-gray-500 ml-2">(${grouped[date].length} orders)</span></h3>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                        <div class="overflow-x-auto w-full">
                            <table class="w-full whitespace-nowrap text-left text-sm text-gray-500">
                                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 w-40">Time</th>
                                        <th class="px-6 py-3">Order ID</th>
                                        <th class="px-6 py-3">Customer</th>
                                        <th class="px-6 py-3">Restaurant</th>
                                        <th class="px-6 py-3">Total</th>
                                        <th class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${orderRows}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                container.appendChild(groupDiv);
            }
        }

        function viewOrder(id) {
            const order = scheduledOrders.find(o => o.id === id);
            if(!order) return;
            selectedOrderId = id;

            document.getElementById('modalOrderId').innerHTML = `Order Details <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded ml-2">${order.id}</span>`;
            document.getElementById('detailCustomerName').innerText = order.customerName;
            document.getElementById('detailCustomerPhone').innerText = order.customerPhone;
            document.getElementById('detailCustomerInitials').innerText = order.customerName.charAt(0);
            document.getElementById('detailAddress').innerText = order.address;
            document.getElementById('detailRestaurant').innerText = order.restaurant;
            document.getElementById('detailDriver').innerText = order.driver;
            
            document.getElementById('detailScheduleBadge').innerHTML = `${order.date} <br> <span class="text-sm font-normal">${order.time}</span>`;

            const ul = document.getElementById('detailItemsList');
            ul.innerHTML = "";
            order.items.forEach(item => {
                ul.innerHTML += `
                    <li class="px-4 py-3 flex justify-between">
                        <div class="text-sm">
                            <span class="font-medium text-gray-900">${item.qty}x</span> ${item.name}
                        </div>
                        <div class="text-sm font-medium text-gray-900">
                            $${(item.qty * item.price).toFixed(2)}
                        </div>
                    </li>
                `;
            });
            document.getElementById('detailTotal').innerText = `$${order.total.toFixed(2)}`;

            document.getElementById('orderDetailsModal').classList.remove('hidden-el');
        }

        function cancelSchedule(id) {
            if(confirm("Are you sure you want to cancel this scheduled order?")) {
                scheduledOrders = scheduledOrders.filter(o => o.id !== id);
                renderGroups();
            }
        }

        renderGroups();
