<!-- Firebase Realtime Dashboard Integration -->
<style>
    @keyframes slideInFade {
        0% { transform: translateX(-30px); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    .slide-in-row {
        animation: slideInFade 0.8s ease-out forwards;
    }
</style>
<!-- Audio element for notifications -->
<audio id="order-notification-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"
    preload="auto"></audio>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3"></div>

<!-- Firebase Modular SDK -->
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
    import { getDatabase, ref, onValue, onChildAdded, onChildChanged } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-database.js";

    // TODO: User needs to replace these with actual credentials
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    const firebaseConfig = {
        apiKey: "AIzaSyBBgcCbxRmxVE3GapweLIfiBT9zo5LPcyU",
        authDomain: "rdms-mukalla.firebaseapp.com",
        projectId: "rdms-mukalla",
        storageBucket: "rdms-mukalla.firebasestorage.app",
        messagingSenderId: "780792946688",
        appId: "1:780792946688:web:4b0edc60090e724d30f8e5",
        measurementId: "G-0YZCZTN0T3",
        databaseURL: "https://rdms-mukalla-default-rtdb.firebaseio.com/",
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const database = getDatabase(app);
    const ordersRef = ref(database, 'orders');

    // UI Elements
    const activeOrdersEl = document.getElementById('fb-active-orders');
    const pendingPaymentsEl = document.getElementById('fb-pending-payments');
    const ordersTableBody = document.getElementById('fb-orders-table-body');
    const notificationSound = document.getElementById('order-notification-sound');

    // Track initialization to avoid playing sound on initial load
    let isInitialLoad = true;

    // Toast Notification Function
    window.showToast = function (title, message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `transform transition-all duration-300 translate-x-full opacity-0 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden`;

        let iconHtml = '';
        if (type === 'success' || type === 'new') {
            iconHtml = `<svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else {
            iconHtml = `<svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        }

        toast.innerHTML = `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">${iconHtml}</div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">${title}</p>
                        <p class="mt-1 text-sm text-gray-500">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="this.closest('.max-w-sm').remove()">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('toast-container').appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    };

    // Helper to format status beautifully
    const getStatusBadge = (status) => {
        const statusMap = {
            'pending': { color: 'bg-yellow-100 text-yellow-800', label: 'Pending' },
            'pending_driver_acceptance': { color: 'bg-purple-100 text-purple-800', label: 'Awaiting Driver' },
            'driver_assigned': { color: 'bg-indigo-100 text-indigo-800', label: 'Driver Assigned' },
            'preparing': { color: 'bg-orange-100 text-orange-800', label: 'Preparing' },
            'picked_up': { color: 'bg-blue-100 text-blue-800', label: 'Picked Up' },
            'delivered': { color: 'bg-green-100 text-green-800', label: 'Delivered' },
            'canceled': { color: 'bg-red-100 text-red-800', label: 'Canceled' },
        };
        const mapped = statusMap[status] || { color: 'bg-gray-100 text-gray-800', label: status };
        return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${mapped.color}">${mapped.label}</span>`;
    };

    // Listen to all orders for aggregated stats
    onValue(ordersRef, (snapshot) => {
        let activeOrdersCount = 0;
        let pendingPaymentsTotal = 0;

        const orders = [];

        snapshot.forEach((childSnapshot) => {
            const order = childSnapshot.val();
            orders.push(order);

            // Active Orders logic (excluding delivered and canceled)
            if (!['delivered', 'canceled'].includes(order.status)) {
                activeOrdersCount++;
            }

            // Pending Payments logic
            if (['pending_verification', 'pending'].includes(order.payment_status)) {
                pendingPaymentsTotal += parseFloat(order.total || 0);
            }
        });

        // Update UI Stats - Removed to prevent overwriting MySQL counts
        // Stats are now handled via DashboardController and Blade variables.

        // Sort orders by timestamp descending for the table
        orders.sort((a, b) => (b.timestamp || 0) - (a.timestamp || 0));

        // Render/Sync Firebase orders (limit to latest 10)
        if (isInitialLoad && ordersTableBody) {
            orders.slice(0, 10).reverse().forEach(order => appendOrderRow(order, false));

            setTimeout(() => { isInitialLoad = false; }, 2000);
        }
    });

    // Listen for NEW orders
    onChildAdded(ordersRef, (data) => {
        if (!isInitialLoad) {
            const order = data.val();

            // Play sound
            notificationSound.play().catch(e => console.log('Audio play prevented', e));

            // Show Toast
            showToast('New Order Received!', `Order #${order.order_number} for $${order.total}`, 'new');

            // Add to table
            appendOrderRow(order, true);
        }
    });

    // Listen for CHANGED orders
    onChildChanged(ordersRef, (data) => {
        if (!isInitialLoad) {
            const order = data.val();

            showToast('Order Updated', `Order #${order.order_number} is now ${order.status}`, 'info');

            updateOrderRow(order);
        }
    });

    // Helper functions for UI
    function appendOrderRow(order, highlight = false) {
        if (!ordersTableBody) return;

        // Remove old row if exists to prevent duplicates
        const existingRow = document.getElementById(`fb-order-${order.id}`);
        if (existingRow) existingRow.remove();

        const row = document.createElement('tr');
        row.id = `fb-order-${order.id}`;
        
        if (highlight) {
            row.className = "bg-green-50 transition-colors duration-1000 slide-in-row";
        }

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#${order.order_number}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${order.restaurant_name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${order.user_name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$${parseFloat(order.total).toFixed(2)}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${getStatusBadge(order.status)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${order.created_at}</td>
        `;

        ordersTableBody.insertBefore(row, ordersTableBody.firstChild);

        // Remove highlight after transition
        if (highlight) {
            setTimeout(() => {
                row.classList.remove('bg-green-50', 'slide-in-row');
            }, 3000);
        }

        // Keep table to max 10 rows
        while (ordersTableBody.children.length > 10) {
            ordersTableBody.removeChild(ordersTableBody.lastChild);
        }
    }

    function updateOrderRow(order) {
        if (!ordersTableBody) return;

        const row = document.getElementById(`fb-order-${order.id}`);
        if (row) {
            // Update cells
            row.cells[1].textContent = order.restaurant_name;
            row.cells[2].textContent = order.user_name;
            row.cells[3].textContent = `$${parseFloat(order.total).toFixed(2)}`;
            row.cells[4].innerHTML = getStatusBadge(order.status);

            // Highlight change
            row.classList.add('bg-blue-50');
            setTimeout(() => {
                row.classList.remove('bg-blue-50');
                row.classList.add('transition-colors', 'duration-1000');
            }, 3000);
        } else {
            // If row wasn't visible, prepend it
            appendOrderRow(order, true);
        }
    }
</script>