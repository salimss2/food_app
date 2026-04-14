/* ================================================
 * orders.js — Page-specific logic
 * ================================================ */

// Dummy Data - ACTIVE ORDERS only
        let activeOrders = [
            { id: "ORD-501", customerName: "John Doe", customerPhone: "+1 234 567 8900", address: "123 Main St, Apt 4B, Springfield", restaurant: "Burger King", driver: "Ahmed Fathi", total: 24.50, status: "Pending", time: "10:30 AM", items: [{name: "Whopper Meal", qty: 2, price: 10.00}, {name: "Onion Rings", qty: 1, price: 4.50}] },
            { id: "ORD-502", customerName: "Sarah Smith", customerPhone: "+1 987 654 3210", address: "405 Tech Campus, Building C", restaurant: "Pizza Hut", driver: "Mohammed Ali", total: 45.00, status: "Preparing", time: "10:45 AM", items: [{name: "Large Pepperoni", qty: 1, price: 20.00}, {name: "Garlic Bread", qty: 2, price: 12.50}, {name: "Coke 1L", qty: 1, price: 0}] },
            { id: "ORD-504", customerName: "Michael Chang", customerPhone: "+1 333 444 5555", address: "Downtown Residences, Floor 12", restaurant: "Sushi Master", driver: "Sara AlSaud", total: 85.00, status: "On Delivery", time: "11:10 AM", items: [{name: "Dragon Roll", qty: 3, price: 60.00}, {name: "Miso Soup", qty: 2, price: 15.00}, {name: "Edamame", qty: 1, price: 10.00}] }
        ];

        let selectedOrderId = null;
        // Helpers
        function getStatusBadge(status) {
            switch(status) {
                case 'Pending': return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>';
                case 'Preparing': return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Preparing</span>';
                case 'On Delivery': return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">On Delivery</span>';
                case 'Completed': return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>';
                case 'Cancelled': return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>';
                default: return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${status}</span>`;
            }
        }

        // Render Table
        function renderTable() {
            const filter = document.getElementById('statusFilter').value;
            const tbody = document.getElementById('ordersTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            let displayOrders = filter === 'All' ? activeOrders : activeOrders.filter(o => o.status === filter);

            if(displayOrders.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            displayOrders.forEach(o => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">${o.id}</div>
                        <div class="text-xs text-gray-500">${o.time}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">${o.customerName}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${o.restaurant}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${o.driver}</td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-gray-900">$${o.total.toFixed(2)}</span>
                    </td>
                    <td class="px-6 py-4">
                        ${getStatusBadge(o.status)}
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <button onclick="viewOrder('${o.id}')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-1.5 px-3 rounded shadow-sm text-xs transition-colors">View / Edit Status</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function viewOrder(id) {
            const order = activeOrders.find(o => o.id === id);
            if(!order) return;
            selectedOrderId = id;

            document.getElementById('modalOrderId').innerHTML = `Order Details <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded ml-2">${order.id}</span>`;
            document.getElementById('detailCustomerName').innerText = order.customerName;
            document.getElementById('detailCustomerPhone').innerText = order.customerPhone;
            document.getElementById('detailCustomerInitials').innerText = order.customerName.charAt(0);
            document.getElementById('detailAddress').innerText = order.address;
            document.getElementById('detailRestaurant').innerText = order.restaurant;
            document.getElementById('detailDriver').innerText = order.driver;
            
            document.getElementById('detailCurrentBadge').innerHTML = getStatusBadge(order.status);
            document.getElementById('modalStatusSelect').value = order.status;

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

        function updateOrderStatusFromModal(newStatus) {
            if(!selectedOrderId) return;
            const order = activeOrders.find(o => o.id === selectedOrderId);
            if(order) {
                order.status = newStatus;
                
                // If it's completed or cancelled, we remove it from active list
                if(newStatus === 'Completed' || newStatus === 'Cancelled') {
                    activeOrders = activeOrders.filter(o => o.id !== selectedOrderId);
                    closeModal('orderDetailsModal');
                } else {
                    document.getElementById('detailCurrentBadge').innerHTML = getStatusBadge(order.status);
                }
                renderTable();
            }
        }

        // Init
        renderTable();
