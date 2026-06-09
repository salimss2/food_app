/* ================================================
 * feedback.js — Page-specific logic
 * ================================================ */

// Dummy Data - Feedback
        let feedback = [
            { id: "F-001", user: "Miles Morales", type: "Driver", entity: "Ahmed Yasin", stars: 5, date: "Today, 11:30 AM", msg: "Lightning fast delivery! Food was still piping hot." },
            { id: "F-002", user: "Gwen Stacy", type: "Restaurant", entity: "Pizza Hut", stars: 2, date: "Yesterday, 8:20 PM", msg: "Pizza crust was completely burnt on the edges." },
            { id: "F-003", user: "Peter Parker", type: "System", entity: "iOS App", stars: 4, date: "April 02, 2026", msg: "App is good but sometimes crashes during checkout." },
            { id: "F-004", user: "Harry Osborn", type: "Driver", entity: "John Doe", stars: 1, date: "March 29, 2026", msg: "Driver didn't follow delivery instructions at all." },
            { id: "F-005", user: "Mary Jane", type: "Restaurant", entity: "Sushi Master", stars: 5, date: "March 28, 2026", msg: "Best sushi in town! Great packaging." }
        ];
// Search logic
        document.getElementById('fbSearch').addEventListener('input', renderTable);

        function drawStars(num) {
            let sHTML = "";
            for(let i=1; i<=5; i++) {
                const cClass = i <= num ? "star-filled" : "star-empty";
                sHTML += `<svg class="w-4 h-4 ${cClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>`;
            }
            return `<div class="flex space-x-0.5">${sHTML}</div>`;
        }

        function renderTable() {
            const filter = document.getElementById('fbFilter').value;
            const searchObj = document.getElementById('fbSearch').value.toLowerCase();
            const tbody = document.getElementById('fbTableBody');
            const emptyState = document.getElementById('emptyState');
            tbody.innerHTML = "";

            let display = feedback;
            
            if(filter !== 'All') { display = display.filter(w => w.type === filter); }
            if(searchObj) {
                display = display.filter(w => w.user.toLowerCase().includes(searchObj) || w.msg.toLowerCase().includes(searchObj) || w.entity.toLowerCase().includes(searchObj));
            }

            if(display.length === 0) {
                tbody.parentElement.classList.add('hidden-el');
                emptyState.classList.remove('hidden-el');
                return;
            } else {
                tbody.parentElement.classList.remove('hidden-el');
                emptyState.classList.add('hidden-el');
            }

            display.forEach(f => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 transition-colors";
                
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <img class="w-8 h-8 rounded-full border border-gray-200 mr-3" src="https://ui-avatars.com/api/?name=${f.user.replace(' ', '+')}&color=fff&background=4f46e5">
                            <span class="font-bold text-gray-900">${f.user}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">${f.type}</span>
                        <span class="text-sm font-medium text-indigo-600">${f.entity}</span>
                    </td>
                    <td class="px-6 py-4">${drawStars(f.stars)}</td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600 truncate max-w-sm" title="${f.msg}">"${f.msg}"</p>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-gray-500">
                        ${f.date}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Init
        renderTable();
