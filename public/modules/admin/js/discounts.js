/* ================================================
 * discounts.js — Page-specific logic
 * ================================================ */

// Dummy Data - Discounts
        let discounts = [
            { id: "DSC-001", code: "WELCOME20", type: "Percentage", value: 20, expiry: "2026-12-31", minOrder: 15, limit: 1000, usages: 342 },
            { id: "DSC-002", code: "FREELUNCH", type: "Fixed", value: 10, expiry: "2026-05-01", minOrder: 30, limit: 50, usages: 49 },
            { id: "DSC-003", code: "HOLIDAY50", type: "Percentage", value: 50, expiry: "2026-01-01", minOrder: 50, limit: 100, usages: 100 }
        ];
// Search Element
        document.getElementById('discountSearch').addEventListener('input', renderTable);

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
            document.getElementById('discountForm').reset();
            document.getElementById('discountId').value = "";
            document.getElementById('modalTitle').innerText = "Create Discount Code";

            if (id) {
                const d = discounts.find(o => o.id === id);
                if (d) {
                    document.getElementById('modalTitle').innerText = "Edit Discount Code";
                    document.getElementById('discountId').value = d.id;
                    document.getElementById('codeTitleInput').value = d.code;
                    document.getElementById('typeInput').value = d.type;
                    document.getElementById('valueInput').value = d.value;
                    document.getElementById('expiryInput').value = d.expiry;
                    document.getElementById('minOrderInput').value = d.minOrder;
                    document.getElementById('limitInput').value = d.limit;
                }
            }
            document.getElementById('discountModal').classList.remove('hidden-el');
        }

        // Render Table
        function renderTable() {
            const filter = document.getElementById('discountFilter').value;
            const searchObj = document.getElementById('discountSearch').value.toLowerCase();
            const tbody = document.getElementById('discountsTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            let displayData = discounts;
            
            if(filter !== 'All') { displayData = displayData.filter(d => d.type === filter); }
            if(searchObj) {
                displayData = displayData.filter(d => d.code.toLowerCase().includes(searchObj));
            }

            if(displayData.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            displayData.forEach(d => {
                const usageRatio = Math.min(100, Math.round((d.usages / d.limit) * 100));
                let usageColor = 'bg-indigo-500';
                if(usageRatio >= 100) usageColor = 'bg-red-500';
                else if(usageRatio > 80) usageColor = 'bg-yellow-500';

                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <span class="font-bold text-gray-900 border border-gray-300 bg-gray-100 rounded px-2 py-1 uppercase tracking-widest">${d.code}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-gray-900">${d.type === 'Percentage' ? d.value + '%' : '$' + d.value}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        Min. Order: $${d.minOrder.toFixed(2)}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center text-xs text-gray-500 mb-1">
                            ${d.usages} / ${d.limit}
                        </div>
                        <div class="w-24 bg-gray-200 rounded-full h-1.5 shadow-inner">
                            <div class="${usageColor} h-1.5 rounded-full" style="width: ${usageRatio}%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">${d.expiry}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <button onclick="openModal('${d.id}')" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm mr-4">Edit</button>
                        <button onclick="deleteDiscount('${d.id}')" class="text-red-500 hover:text-red-700 font-medium text-sm">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function handleDiscountSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('discountId').value;
            const code = document.getElementById('codeTitleInput').value.toUpperCase();
            const type = document.getElementById('typeInput').value;
            const value = parseFloat(document.getElementById('valueInput').value);
            const expiry = document.getElementById('expiryInput').value;
            const minOrder = parseFloat(document.getElementById('minOrderInput').value);
            const limit = parseInt(document.getElementById('limitInput').value);

            if (id) {
                // Edit
                const index = discounts.findIndex(d => d.id === id);
                if (index > -1) {
                    discounts[index] = { ...discounts[index], code, type, value, expiry, minOrder, limit };
                    showToast('Discount updated successfully!');
                }
            } else {
                // Create
                const newCode = {
                    id: "DSC-" + Math.floor(Math.random() * 1000),
                    code, type, value, expiry, minOrder, limit, usages: 0
                };
                discounts.unshift(newCode);
                showToast('Discount generated successfully!');
            }

            closeModal('discountModal');
            renderTable();
        }

        function deleteDiscount(id) {
            if(confirm("Are you sure you want to delete this discount code?")) {
                discounts = discounts.filter(d => d.id !== id);
                renderTable();
                showToast('Discount code deleted.');
            }
        }

        // Init
        renderTable();
