/* ================================================
 * order-history.js — Page-specific logic
 * ================================================ */

// Dummy Data - HISTORY orders
        let historyOrders = [
            { id: "ORD-101", customerName: "Alice Wonderland", customerPhone: "+1 234 500 0001", address: "123 Story Blvd", restaurant: "Pizza Hut", driver: "Hassan B", total: 32.00, status: "Completed", date: "April 02, 2026, 14:00", items: [{name: "Medium Cheese", qty: 2, price: 15.00}, {name: "Cola", qty: 1, price: 2.00}] },
            { id: "ORD-102", customerName: "Bob Builder", customerPhone: "+1 234 500 0002", address: "405 Construction Ave", restaurant: "Burger King", driver: "Ahmed Fathi", total: 18.50, status: "Completed", date: "April 02, 2026, 15:30", items: [{name: "Whopper", qty: 1, price: 10.00}, {name: "Fries", qty: 2, price: 4.25}] },
            { id: "ORD-103", customerName: "Charlie Brown", customerPhone: "+1 234 500 0003", address: "789 Peanut St", restaurant: "Mama's Italian", driver: "None", total: 55.00, status: "Cancelled", date: "April 03, 2026, 12:15", items: [{name: "Lasagna", qty: 2, price: 25.00}, {name: "Garlic Bread", qty: 1, price: 5.00}] },
            { id: "ORD-104", customerName: "Diana Prince", customerPhone: "+1 234 500 0004", address: "1 Themyscira Island", restaurant: "Salad Station", driver: "Mohammed Ali", total: 22.00, status: "Completed", date: "April 04, 2026, 13:00", items: [{name: "Greek Salad", qty: 2, price: 11.00}] }
        ];

        let selectedOrderId = null;
        // Helpers
        function getStatusBadge(status) {
            if(status === 'Completed') return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">Completed</span>';
            if(status === 'Cancelled') return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">Cancelled</span>';
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${status}</span>`;
        }

        // Render Table
        function renderHistoryTable() {
            const filter = document.getElementById('historyFilter').value;
            const searchObj = document.getElementById('historySearch').value.toLowerCase();
            const tbody = document.getElementById('historyTableBody');
            const emptyState = document.getElementById('historyEmptyState');
            tbody.innerHTML = "";

            let displayOrders = historyOrders;
            
            if(filter !== 'All') {
                displayOrders = displayOrders.filter(o => o.status === filter);
            }
            if(searchObj) {
                displayOrders = displayOrders.filter(o => o.id.toLowerCase().includes(searchObj) || o.customerName.toLowerCase().includes(searchObj));
            }

            document.getElementById('historyCount').innerText = displayOrders.length;

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
                        <div class="text-xs text-gray-500">${o.date}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">${o.customerName}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${o.restaurant}</td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-gray-900">$${o.total.toFixed(2)}</span>
                    </td>
                    <td class="px-6 py-4">
                        ${getStatusBadge(o.status)}
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <button onclick="viewHistoryOrder('${o.id}')" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm flex items-center justify-end w-full">
                            <span class="mr-1">Details</span> 
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function viewHistoryOrder(id) {
            const order = historyOrders.find(o => o.id === id);
            if(!order) return;

            document.getElementById('modalOrderId').innerHTML = `Order Reference <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded ml-2">${order.id}</span>`;
            document.getElementById('detailCustomerName').innerText = order.customerName;
            document.getElementById('detailCustomerPhone').innerText = order.customerPhone;
            document.getElementById('detailCustomerInitials').innerText = order.customerName.charAt(0);
            document.getElementById('detailAddress').innerText = order.address;
            document.getElementById('detailRestaurant').innerText = order.restaurant;
            document.getElementById('detailDriver').innerText = order.driver;
            document.getElementById('detailTime').innerText = `Date: ${order.date}`;
            
            document.getElementById('detailCurrentBadge').innerHTML = getStatusBadge(order.status);

            const ul = document.getElementById('detailItemsList');
            ul.innerHTML = "";
            order.items.forEach(item => {
                ul.innerHTML += `
                    <li class="px-4 py-3 flex justify-between">
                        <div class="text-sm">
                            <span class="font-medium text-gray-900">${item.qty}x</span> <span class="text-gray-600">${item.name}</span>
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

        // Init
        renderHistoryTable();
