/* ================================================
 * complaints.js — Page-specific logic
 * ================================================ */

// Dummy Data - Complaints
        let comps = [
            { id: "CP-1052", user: "Miles Morales", type: "Driver", entity: "D-9901", status: "Pending", date: "Today, 10:00 AM", msg: "Driver was extremely rude and threw the package on my porch." },
            { id: "CP-1051", user: "Gwen Stacy", type: "Order", entity: "#ORD-5501", status: "In Progress", date: "Yesterday, 3:30 PM", msg: "Missing 2 fries from my meal! I called the restaurant but they refused to help." },
            { id: "CP-1050", user: "Peter Parker", type: "Payment", entity: "TXN-8810", status: "Resolved", date: "April 02, 2026", msg: "I was double charged for my pizza order. Please refund." },
            { id: "CP-1049", user: "Harry Osborn", type: "Order", entity: "#ORD-4011", status: "Rejected", date: "March 28, 2026", msg: "Food was cold and I didn't like the taste." }
        ];
// Search logic
        document.getElementById('compSearch').addEventListener('input', renderTable);

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

        function getBadge(status) {
            switch(status) {
                case 'Pending': return 'bg-yellow-100 text-yellow-800';
                case 'In Progress': return 'bg-blue-100 text-blue-800 border border-blue-200';
                case 'Resolved': return 'bg-green-100 text-green-800';
                case 'Rejected': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function viewDetails(id) {
            const c = comps.find(o => o.id === id);
            if(c) {
                document.getElementById('mId').innerText = `Ticket ${c.id}`;
                document.getElementById('mBadge').className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${getBadge(c.status)}`;
                document.getElementById('mBadge').innerText = c.status;
                
                document.getElementById('mUser').innerText = c.user;
                document.getElementById('mType').innerText = c.type;
                document.getElementById('mRelated').innerText = c.entity;
                document.getElementById('mBody').innerText = `"${c.msg}"`;
                
                document.getElementById('complaintIdHolder').value = c.id;
                document.getElementById('resStatus').value = c.status !== 'Pending' ? c.status : 'In Progress';
                document.getElementById('resMessage').value = ""; // clear form

                document.getElementById('reviewModal').classList.remove('hidden-el');
            }
        }

        function handleRespond(e) {
            e.preventDefault();
            const id = document.getElementById('complaintIdHolder').value;
            const newStatus = document.getElementById('resStatus').value;
            
            const target = comps.find(c => c.id === id);
            if(target) {
                target.status = newStatus;
                showToast(`Complaint updated to: ${newStatus}`);
                document.getElementById('reviewModal').classList.add('hidden-el');
                renderTable();
            }
        }

        function renderTable() {
            const filter = document.getElementById('compFilter').value;
            const searchObj = document.getElementById('compSearch').value.toLowerCase();
            const tbody = document.getElementById('compTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            let display = comps;
            
            if(filter !== 'All') { display = display.filter(w => w.status === filter); }
            if(searchObj) {
                display = display.filter(w => w.id.toLowerCase().includes(searchObj) || w.user.toLowerCase().includes(searchObj));
            }

            if(display.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            display.forEach(c => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors cursor-pointer";
                tr.onclick = () => viewDetails(c.id);
                
                tr.innerHTML = `
                    <td class="px-6 py-4 font-bold text-indigo-600">${c.id}</td>
                    <td class="px-6 py-4 font-semibold text-gray-900">${c.user}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${c.type}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${c.date}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold ${getBadge(c.status)}">${c.status}</span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <button class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Review Ticket &rarr;</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Init
        renderTable();
