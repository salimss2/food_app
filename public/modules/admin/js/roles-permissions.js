/* ================================================
 * roles-permissions.js — Page-specific logic
 * ================================================ */

// Dummy Data - Roles
        let roles = [
            { id: "R-01", name: "Super Admin", desc: "Full system access.", usersCount: 2, isSystem: true },
            { id: "R-02", name: "Content Editor", desc: "Can manage restaurants and offers.", usersCount: 5, isSystem: false },
            { id: "R-03", name: "Support Agent", desc: "Can view orders and handle complaints.", usersCount: 12, isSystem: false },
            { id: "R-04", name: "Finance Manager", desc: "Can manage payments and settlements.", usersCount: 1, isSystem: false }
        ];

        let activeRoleId = "R-01";

        // Dummy Data - Permissions Matrix Config per module
        const modules = [
            { id: "m_users", name: "Users Management" },
            { id: "m_orders", name: "Orders Flow" },
            { id: "m_restaurants", name: "Restaurants & Menus" },
            { id: "m_drivers", name: "Drivers & Fleet" },
            { id: "m_payments", name: "Payments & Revenue" },
            { id: "m_marketing", name: "Offers & Discounts" },
            { id: "m_support", name: "Complaints & Feedback" },
            { id: "m_settings", name: "System Config" }
        ];

        // Permissions map: dict of roleId -> module_id -> {v, c, e, d}
        const initialPermissions = {
            "R-01": {}, // Populated dynamically to all true
            "R-02": { "m_restaurants": {v:1,c:1,e:1,d:0}, "m_marketing": {v:1,c:1,e:1,d:0} },
            "R-03": { "m_users": {v:1,c:0,e:0,d:0}, "m_orders": {v:1,c:0,e:1,d:0}, "m_support": {v:1,c:1,e:1,d:0} },
            "R-04": { "m_payments": {v:1,c:0,e:1,d:0}, "m_orders": {v:1,c:0,e:0,d:0} }
        };
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

        function openRoleModal(id = null) {
            document.getElementById('roleForm').reset();
            document.getElementById('roleId').value = "";
            document.getElementById('roleModalTitle').innerText = "Create Global Role";

            if (id) {
                const r = roles.find(o => o.id === id);
                if (r) {
                    document.getElementById('roleModalTitle').innerText = "Edit Role";
                    document.getElementById('roleId').value = r.id;
                    document.getElementById('roleNameInput').value = r.name;
                    document.getElementById('roleDescInput').value = r.desc;
                }
            }
            document.getElementById('roleModal').classList.remove('hidden-el');
        }

        function handleRoleSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('roleId').value;
            const name = document.getElementById('roleNameInput').value;
            const desc = document.getElementById('roleDescInput').value;

            if (id) {
                const r = roles.find(o => o.id === id);
                if(r.isSystem) { showToast("Cannot edit system roles.", true); return; }
                r.name = name; r.desc = desc;
                showToast('Role updated successfully.');
            } else {
                const newId = "R-0" + (roles.length + 1);
                roles.push({ id: newId, name, desc, usersCount: 0, isSystem: false });
                initialPermissions[newId] = {};
                showToast('New role created.');
            }

            closeModal('roleModal');
            renderRoles();
        }

        function renderRoles() {
            const list = document.getElementById('rolesList');
            list.innerHTML = "";

            roles.forEach(r => {
                const isActive = r.id === activeRoleId;
                const activeClass = isActive ? "bg-indigo-50 border-l-4 border-primary" : "hover:bg-gray-50 border-l-4 border-transparent";
                
                const deleteBtn = r.isSystem ? "" : `<button onclick="event.stopPropagation(); deleteRole('${r.id}')" class="text-red-400 hover:text-red-600 focus:outline-none"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>`;

                const li = document.createElement('li');
                li.className = `p-4 cursor-pointer transition-colors flex justify-between items-start ${activeClass}`;
                li.onclick = () => selectRole(r.id);
                li.innerHTML = `
                    <div>
                        <div class="flex items-center space-x-2">
                            <h4 class="text-sm font-bold text-gray-900">${r.name}</h4>
                            ${r.isSystem ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-200 text-gray-600">SYS</span>' : ''}
                        </div>
                        <p class="text-xs text-gray-500 mt-1">${r.usersCount} Assigned Users</p>
                    </div>
                    ${deleteBtn}
                `;
                list.appendChild(li);
            });
        }

        function deleteRole(id) {
            const r = roles.find(o => o.id === id);
            if(r.isSystem) return;
            if(confirm(`Are you sure you want to delete the ${r.name} role?`)) {
                roles = roles.filter(o => o.id !== id);
                delete initialPermissions[id];
                if(activeRoleId === id) selectRole("R-01");
                else renderRoles();
                showToast("Role removed.", true);
            }
        }

        function selectRole(id) {
            activeRoleId = id;
            renderRoles();
            
            const r = roles.find(o => o.id === id);
            document.getElementById('matrixTitle').innerText = `Permissions for ${r.name}`;
            document.getElementById('matrixSubtitle').innerText = r.desc;
            
            renderPermissions();
        }

        function renderPermissions() {
            const container = document.getElementById('permissionsContainer');
            container.innerHTML = "";

            const isSuper = activeRoleId === "R-01";
            const rolePerms = initialPermissions[activeRoleId] || {};

            modules.forEach(m => {
                // Determine values
                const p = rolePerms[m.id] || {v:0, c:0, e:0, d:0};
                const v_checked = isSuper || p.v ? "checked" : "";
                const c_checked = isSuper || p.c ? "checked" : "";
                const e_checked = isSuper || p.e ? "checked" : "";
                const d_checked = isSuper || p.d ? "checked" : "";
                const disabled = isSuper ? "disabled" : "";
                const opacity = isSuper ? "opacity-50" : "";

                // Generate toggle helper
                const makeToggle = (type, actionName, isChecked) => `
                    <div class="flex items-center justify-between sm:justify-center mt-2 sm:mt-0">
                        <span class="sm:hidden text-xs text-gray-500 w-16">${actionName}</span>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in ${opacity}">
                            <input type="checkbox" name="toggle" id="toggle_${m.id}_${type}" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer" ${isChecked} ${disabled}/>
                            <label for="toggle_${m.id}_${type}" class="toggle-label block overflow-hidden h-5 rounded-full cursor-pointer"></label>
                        </div>
                    </div>
                `;

                const row = document.createElement('div');
                row.className = "flex flex-col sm:grid sm:grid-cols-5 gap-4 py-3 sm:py-4 border-b border-gray-100 sm:items-center hover:bg-gray-50 -mx-4 px-4 sm:mx-0 sm:px-0 transition-colors rounded";
                row.innerHTML = `
                    <div class="col-span-1 mb-2 sm:mb-0">
                        <span class="text-sm font-semibold text-gray-800 break-words">${m.name}</span>
                    </div>
                    ${makeToggle('v', 'View', v_checked)}
                    ${makeToggle('c', 'Create', c_checked)}
                    ${makeToggle('e', 'Edit', e_checked)}
                    ${makeToggle('d', 'Delete', d_checked)}
                `;
                container.appendChild(row);
            });
        }

        function savePermissions() {
            if(activeRoleId === "R-01") {
                showToast("Super admin permissions cannot be changed.", true);
                return;
            }
            
            // In a real app we'd scrape the toggles and dispatch JSON via axios
            showToast("Role access policy successfully synchronized.");
        }

        // Init
        renderRoles();
        selectRole("R-01");
