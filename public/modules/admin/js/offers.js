/* ================================================
 * offers.js — Page-specific logic
 * ================================================ */

// Dummy Data - Offers
        let offers = [
            { id: "OFR-001", title: "Summer Splash 20%", discount: 20, expiry: "2026-08-30", restaurant: "All Restaurants", status: "Active" },
            { id: "OFR-002", title: "Free Garlic Bread", discount: 100, expiry: "2026-04-15", restaurant: "Pizza Hut", status: "Active" },
            { id: "OFR-003", title: "Midnight Cravings", discount: 15, expiry: "2026-03-01", restaurant: "Burger King", status: "Expired" }
        ];
// Search Element
        document.getElementById('offerSearch').addEventListener('input', renderTable);

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

        function openModal(id = null) {
            document.getElementById('offerForm').reset();
            document.getElementById('offerId').value = "";
            document.getElementById('modalTitle').innerText = "Create Offer";

            if (id) {
                const offer = offers.find(o => o.id === id);
                if (offer) {
                    document.getElementById('modalTitle').innerText = "Edit Offer";
                    document.getElementById('offerId').value = offer.id;
                    document.getElementById('offerTitleInput').value = offer.title;
                    document.getElementById('offerDiscountInput').value = offer.discount;
                    document.getElementById('offerExpiryInput').value = offer.expiry;
                    document.getElementById('offerRestaurantInput').value = offer.restaurant;
                    document.getElementById('offerStatusInput').value = offer.status;
                }
            }
            document.getElementById('offerModal').classList.remove('hidden-el');
        }

        // Render Table
        function renderTable() {
            const filter = document.getElementById('offerFilter').value;
            const searchObj = document.getElementById('offerSearch').value.toLowerCase();
            const tbody = document.getElementById('offersTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            let displayData = offers;
            
            if(filter !== 'All') { displayData = displayData.filter(o => o.status === filter); }
            if(searchObj) {
                displayData = displayData.filter(o => o.title.toLowerCase().includes(searchObj) || o.restaurant.toLowerCase().includes(searchObj));
            }

            if(displayData.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            displayData.forEach(o => {
                const badge = o.status === 'Active' 
                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">Active</span>'
                    : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">Expired</span>';

                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4 font-semibold text-sm text-gray-900">${o.title}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${o.restaurant}</td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">${o.discount}%</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">${o.expiry}</td>
                    <td class="px-6 py-4">${badge}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <button onclick="openModal('${o.id}')" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm mr-4">Edit</button>
                        <button onclick="deleteOffer('${o.id}')" class="text-red-500 hover:text-red-700 font-medium text-sm">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function handleOfferSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('offerId').value;
            const title = document.getElementById('offerTitleInput').value;
            const discount = document.getElementById('offerDiscountInput').value;
            const expiry = document.getElementById('offerExpiryInput').value;
            const restaurant = document.getElementById('offerRestaurantInput').value;
            const status = document.getElementById('offerStatusInput').value;

            if (id) {
                // Edit
                const index = offers.findIndex(o => o.id === id);
                if (index > -1) {
                    offers[index] = { id, title, discount, expiry, restaurant, status };
                    showToast('Offer updated successfully!');
                }
            } else {
                // Create
                const newOffer = {
                    id: "OFR-" + Math.floor(Math.random() * 1000),
                    title, discount, expiry, restaurant, status
                };
                offers.unshift(newOffer);
                showToast('Offer created successfully!');
            }

            closeModal('offerModal');
            renderTable();
        }

        function deleteOffer(id) {
            if(confirm("Are you sure you want to delete this offer?")) {
                offers = offers.filter(o => o.id !== id);
                renderTable();
                showToast('Offer removed.');
            }
        }

        // Init
        renderTable();
