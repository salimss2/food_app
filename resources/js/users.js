/* ================================================
 * public/js/users.js — Page-specific UI logic
 * ================================================ */

let currentTab = "All";

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    
    // Setup for Add User
    if (modalId === 'userModal') {
        document.getElementById('modal-title').innerText = "Add User";
        document.getElementById('userForm').reset();
        
        let methodInput = document.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        
        // Form route should point to store action (e.g., /users)
        document.getElementById('userForm').action = "/users"; 
    }
}

function openEditModal(id, name, email, phone, role, status) {
    document.getElementById('modal-title').innerText = "Edit User";
    document.getElementById('userName').value = name;
    document.getElementById('userEmail').value = email;
    document.getElementById('userPhone').value = phone;
    
    // Select role if present
    const roleSelect = document.getElementById('userRole');
    for (let i = 0; i < roleSelect.options.length; i++) {
        if (roleSelect.options[i].value === role || roleSelect.options[i].text === role) {
            roleSelect.selectedIndex = i;
            break;
        }
    }
    
    document.getElementById('userStatus').value = status;

    const form = document.getElementById('userForm');
    form.action = `/users/${id}`; // Points to update route
    
    let methodInput = document.querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
    }

    document.getElementById('userModal').classList.remove('hidden');
}

function openDeleteModal(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/users/${id}`; // Points to destroy route
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Client-side Tab & DOM Search Logic
function filterAndCalculateUsers() {
    const query = document.getElementById('userSearch') ? document.getElementById('userSearch').value.toLowerCase() : "";
    const rows = document.querySelectorAll('#usersTableBody tr:not(.empty-row)');
    
    let counts = { All: 0, Active: 0, Blocked: 0, Archived: 0 };
    let visibleCount = 0;

    rows.forEach(row => {
        const name = row.querySelector('.user-name') ? row.querySelector('.user-name').innerText.toLowerCase() : "";
        const email = row.querySelector('.user-email') ? row.querySelector('.user-email').innerText.toLowerCase() : "";
        const status = row.getAttribute('data-status');
        
        // Accumulate status counts unconditionally (ignore search box filter for tabs totals)
        counts.All++;
        if (counts[status] !== undefined) counts[status]++;
        
        // Determine Visibility
        const matchesTab = currentTab === "All" || status === currentTab;
        const matchesSearch = name.includes(query) || email.includes(query);
        
        if (matchesTab && matchesSearch) {
            row.classList.remove('hidden');
            visibleCount++;
        } else {
            row.classList.add('hidden');
        }
    });

    // Update Tab UI Count Badges
    if (document.getElementById('countAll')) document.getElementById('countAll').innerText = counts.All;
    if (document.getElementById('countActive')) document.getElementById('countActive').innerText = counts.Active;
    if (document.getElementById('countBlocked')) document.getElementById('countBlocked').innerText = counts.Blocked;
    if (document.getElementById('countArchived')) document.getElementById('countArchived').innerText = counts.Archived;
    
    // Update summary table result count
    if (document.getElementById('totalUsersCount')) document.getElementById('totalUsersCount').innerText = visibleCount;
}

// Tab Configuration Listeners
function initTabs() {
    const tabs = document.querySelectorAll('#userTabs button');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            currentTab = tab.getAttribute('data-tab');
            
            // Toggle Visual Styles for tabs
            tabs.forEach(t => {
                t.classList.remove('border-primary', 'text-primary');
                t.classList.add('border-transparent', 'text-gray-500');
                const badge = t.querySelector('span:last-child');
                if (badge) {
                    badge.classList.remove('bg-indigo-100', 'text-primary');
                    badge.classList.add('bg-gray-100', 'text-gray-600');
                }
            });

            tab.classList.remove('border-transparent', 'text-gray-500');
            tab.classList.add('border-primary', 'text-primary');
            
            const activeBadge = tab.querySelector('span:last-child');
            if (activeBadge) {
                activeBadge.classList.remove('bg-gray-100', 'text-gray-600');
                activeBadge.classList.add('bg-indigo-100', 'text-primary');
            }

            filterAndCalculateUsers();
        });
    });
}

// Initialization and Event Setup
document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    filterAndCalculateUsers();
    
    const userSearch = document.getElementById('userSearch');
    if (userSearch) {
        userSearch.addEventListener('input', filterAndCalculateUsers);
    }
});
