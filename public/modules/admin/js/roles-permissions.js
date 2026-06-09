/* ================================================
 * roles-permissions.js — Dynamic Backend Integration
 * ================================================ */

let roles = [];
let allPermissions = [];
let activeRoleId = null;

// The requested Mapping Strategy
const mappingArray = [
    {
        id: 'users',
        label: 'Users & Drivers',
        permissions: [
            { name: 'view_users', col: 'view', label: 'Users' },
            { name: 'create_users', col: 'create', label: 'Users' },
            { name: 'edit_users', col: 'edit', label: 'Users' },
            { name: 'delete_users', col: 'delete', label: 'Users' },
            { name: 'view_drivers', col: 'view', label: 'Drivers' },
            { name: 'edit_drivers', col: 'edit', label: 'Drivers' }
        ]
    },
    {
        id: 'restaurants',
        label: 'Restaurants',
        permissions: [
            { name: 'view_restaurants', col: 'view', label: 'All' },
            { name: 'create_restaurants', col: 'create', label: 'All' },
            { name: 'edit_restaurants', col: 'edit', label: 'All' },
            { name: 'delete_restaurants', col: 'delete', label: 'All' }
        ]
    },
    {
        id: 'orders',
        label: 'Orders',
        permissions: [
            { name: 'view_orders', col: 'view', label: 'All' },
            { name: 'edit_orders', col: 'edit', label: 'All' },
            { name: 'manage_order_status', col: 'manage', label: 'Status' }
        ]
    },
    {
        id: 'financials',
        label: 'Financials',
        permissions: [
            { name: 'view_financials', col: 'view', label: 'All' },
            { name: 'manage_payments', col: 'manage', label: 'Payments' },
            { name: 'manage_commissions', col: 'manage', label: 'Commissions' }
        ]
    },
    {
        id: 'complaints',
        label: 'Complaints',
        permissions: [
            { name: 'view_complaints', col: 'view', label: 'All' },
            { name: 'respond_complaints', col: 'respond', label: 'All' }
        ]
    },
    {
        id: 'settings',
        label: 'Settings',
        permissions: [
            { name: 'manage_settings', col: 'manage', label: 'General' },
            { name: 'manage_roles', col: 'manage', label: 'Roles' }
        ]
    }
];

const COLUMNS = ['view', 'create', 'edit', 'delete', 'manage', 'respond'];

// Toast Helper
function showToast(msg, isError = false) {
    const toast = document.getElementById('toast');
    document.getElementById('toastMessage').innerText = msg;

    if (isError) {
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

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

// Data Fetching
async function fetchRolesData() {
    try {
        const res = await fetch('/admin/api/roles', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.success) {
            roles = data.roles;
            allPermissions = data.all_permissions;

            if (roles.length > 0 && !activeRoleId) {
                activeRoleId = roles[0].id;
            }

            renderRoles();
            if (activeRoleId) selectRole(activeRoleId);
        }
    } catch (e) {
        showToast("Failed to fetch roles data.", true);
        console.error(e);
    }
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

function closeModal(id) {
    document.getElementById(id).classList.add('hidden-el');
}

async function handleRoleSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('roleId').value;
    const name = document.getElementById('roleNameInput').value;

    try {
        let res;
        if (id) {
            res = await fetch(`/admin/api/roles/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify({ name })
            });
        } else {
            res = await fetch(`/admin/api/roles`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify({ name })
            });
        }

        const data = await res.json();
        if (data.success) {
            showToast(data.message);
            closeModal('roleModal');
            fetchRolesData(); // Refresh list
        } else {
            showToast(data.message || "Error saving role.", true);
        }
    } catch (err) {
        showToast("Network error.", true);
    }
}

function renderRoles() {
    const list = document.getElementById('rolesList');
    list.innerHTML = "";

    roles.forEach(r => {
        const isActive = r.id === activeRoleId;
        const activeClass = isActive ? "bg-indigo-50 border-l-4 border-primary" : "hover:bg-gray-50 border-l-4 border-transparent";

        const deleteBtn = r.isSystem ? "" : `<button onclick="event.stopPropagation(); deleteRole(${r.id})" class="text-red-400 hover:text-red-600 focus:outline-none"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>`;

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

async function deleteRole(id) {
    const r = roles.find(o => o.id === id);
    if (r.isSystem) return;
    if (confirm(`Are you sure you want to delete the ${r.name} role?`)) {
        try {
            const res = await fetch(`/admin/api/roles/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken() }
            });
            const data = await res.json();
            if (data.success) {
                showToast("Role removed.");
                activeRoleId = null;
                fetchRolesData();
            } else {
                showToast(data.message, true);
            }
        } catch (e) {
            showToast("Network error.", true);
        }
    }
}

function selectRole(id) {
    activeRoleId = id;
    renderRoles();

    const r = roles.find(o => o.id === id);
    document.getElementById('matrixTitle').innerText = `Permissions for ${r.name}`;
    document.getElementById('matrixSubtitle').innerText = r.desc;

    // Reset global select alls
    COLUMNS.forEach(c => {
        const el = document.getElementById(`selectAll_${c}`);
        if (el) el.checked = false;
    });

    renderPermissions();
}

function renderPermissions() {
    const container = document.getElementById('permissionsContainer');
    if (!container) return;

    container.innerHTML = "";

    const r = roles.find(o => o.id === activeRoleId);
    if (!r) return;

    const isSystem = r.isSystem;

    mappingArray.forEach(module => {
        const row = document.createElement('div');
        // Changed to items-start for better vertical alignment when columns have multiple items
        row.className = "flex flex-col sm:grid sm:grid-cols-7 gap-4 py-4 border-b border-gray-100 items-start hover:bg-gray-50 -mx-4 px-4 sm:mx-0 sm:px-0 transition-colors rounded";

        let rowHtml = `
            <div class="col-span-1 mb-2 sm:mb-0 flex items-center h-full pt-1">
                <input type="checkbox" data-action="select-row" data-row="${module.id}" class="w-4 h-4 mr-2 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer" ${isSystem ? 'disabled' : ''}>
                <span class="text-sm font-semibold text-gray-800 break-words">${module.label}</span>
            </div>
        `;

        COLUMNS.forEach(col => {
            // Find all permissions mapping to this column for this module
            const permsInCol = module.permissions.filter(p => p.col === col);

            // Flex column with gap-3 for proper vertical stacking and neat spacing
            let colHtml = `<div class="flex flex-col gap-3 items-center justify-start w-full">`;
            if (permsInCol.length === 0) {
                colHtml += `<span class="text-gray-300 pt-1">-</span>`;
            } else {
                permsInCol.forEach(p => {
                    const disabledAttr = isSystem ? 'disabled' : '';
                    const opacity = isSystem ? 'opacity-50' : '';

                    // Do not render empty span if label is 'All' to avoid spacing issues
                    const labelHtml = p.label !== 'All' ? `<span class="text-[10px] font-medium text-gray-500 mb-1 leading-none">${p.label}</span>` : '';

                    colHtml += `
                        <div class="flex flex-col items-center justify-center">
                            ${labelHtml}
                            <label class="relative inline-flex items-center cursor-pointer ${opacity}">
                                <input type="checkbox" name="permission_toggle" value="${p.name}" data-col="${col}" data-row="${module.id}" id="toggle_${p.name}" class="sr-only peer" ${disabledAttr}>
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    `;
                });
            }
            colHtml += `</div>`;
            rowHtml += colHtml;
        });

        row.innerHTML = rowHtml;
        container.appendChild(row);
    });

    // Async Hydration: Wait for the DOM to fully inject and paint before hydrating states
    setTimeout(() => hydrateCheckboxes(r), 0);
}

function hydrateCheckboxes(role) {
    if (role.isSystem) {
        document.querySelectorAll('input[name="permission_toggle"]').forEach(cb => {
            cb.checked = true;
        });
    } else {
        const rolePerms = role.permissions || [];
        rolePerms.forEach(permName => {
            // ID Matching explicitly maps to the generated HTML pattern
            const cb = document.getElementById(`toggle_${permName}`);
            if (cb) {
                cb.checked = true;
            }
        });
    }
}

function toggleSelectAllColumn(colName, isChecked) {
    const checkboxes = document.querySelectorAll(`input[name="permission_toggle"][data-col="${colName}"]:not(:disabled)`);
    checkboxes.forEach(cb => cb.checked = isChecked);
}

function toggleSelectAllRow(rowId, isChecked) {
    const checkboxes = document.querySelectorAll(`input[name="permission_toggle"][data-row="${rowId}"]:not(:disabled)`);
    checkboxes.forEach(cb => cb.checked = isChecked);
}

async function savePermissions() {
    const r = roles.find(o => o.id === activeRoleId);
    if (!r) return;

    if (r.isSystem) {
        showToast("System role permissions cannot be changed.", true);
        return;
    }

    const checkboxes = document.querySelectorAll('input[name="permission_toggle"]:checked');
    const selectedPermissions = Array.from(checkboxes).map(cb => cb.value);

    try {
        const res = await fetch(`/admin/api/roles/${activeRoleId}/sync`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ permissions: selectedPermissions })
        });
        const data = await res.json();

        if (data.success) {
            showToast("Role access policy successfully synchronized.");

            // Explicitly wait for fresh data and re-sync local state
            await fetchRolesData();

            // Re-find the role to ensure we have the latest permissions from the server
            const updatedRole = roles.find(o => o.id === activeRoleId);
            if (updatedRole) {
                renderPermissions();
            }
        } else {
            showToast(data.message || "Failed to sync permissions.", true);
        }
    } catch (err) {
        showToast("Network error during sync.", true);
        console.error("Save error:", err);
    }
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    fetchRolesData();

    const container = document.getElementById('permissionsContainer');
    if (container) {
        // Event Delegation only for Select-All Row (Native toggle handled by browser label/input)
        container.addEventListener('change', (e) => {
            if (e.target && e.target.dataset.action === 'select-row') {
                toggleSelectAllRow(e.target.dataset.row, e.target.checked);
            }
        });
    }
});
