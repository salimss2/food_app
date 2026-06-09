/* ================================================
 * users.js — Page-specific logic
 * ================================================ */

// ── Modal: View User (read-only quick preview) ────────────────
function openViewModal(user) {
    // Avatar from ui-avatars
    document.getElementById('view_avatar').src =
        `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=4f46e5&color=fff&size=200`;

    document.getElementById('view_name').innerText = user.name || '—';
    document.getElementById('view_email').innerText = user.email || '—';
    document.getElementById('view_id').innerText = '#' + user.id;
    document.getElementById('view_phone').innerText = user.phone || 'N/A';
    document.getElementById('view_status').innerText = user.status || '—';
    document.getElementById('view_role').innerText = user.role || 'Customer';
    document.getElementById('view_created_at').innerText = user.created_at || '—';

    // Role badge
    document.getElementById('view_role_badge').innerText = user.role || 'Customer';

    // Status badge — green for Active, red otherwise
    const statusBadge = document.getElementById('view_status_badge');
    statusBadge.innerText = user.status || '—';
    const isActive = (user.status || '').toLowerCase() === 'active';
    statusBadge.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
        }`;

    // "View Full Profile" link — builds /admin/users/{id}
    const storeUrl = document.getElementById('userForm')?.getAttribute('data-store-url') || '/admin/users';
    const usersBase = storeUrl.replace(/\/users$/, '/users');
    const profileLink = document.getElementById('view_profile_link');
    profileLink.href = usersBase + '/' + user.id;
    profileLink.textContent = 'View Full Profile →';

    document.getElementById('viewUserModal').classList.remove('hidden');
}

// ── Modal: Add User ────────────────────────────────────────────
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');

    if (modalId === 'userModal') {
        document.getElementById('modal-title').innerText = 'Add User';
        const form = document.getElementById('userForm');
        form.reset();

        // Clear PUT spoofing — this is a POST (create) action
        document.getElementById('methodContainer').innerHTML = '';

        // Set action back to the store URL
        const storeUrl = form.getAttribute('data-store-url');
        form.action = storeUrl;
    }
}

// ── Modal: Edit User ───────────────────────────────────────────
function openEditModal(id, name, email, phone, role, status) {
    document.getElementById('modal-title').innerText = 'Edit User';

    // Populate form fields
    document.getElementById('userName').value = name;
    document.getElementById('userEmail').value = email;
    document.getElementById('userPhone').value = phone !== 'null' ? (phone || '') : '';
    document.getElementById('userRole').value = role;
    const statusSelect = document.getElementById('userStatus');
    const targetStatus = (status || 'Active').trim().toLowerCase();
    Array.from(statusSelect.options).forEach(option => {
        if (option.value.toLowerCase() === targetStatus) {
            statusSelect.value = option.value;
        }
    });
    document.getElementById('userPassword').value = '';

    // Build update URL: /admin/users/{id}
    const form = document.getElementById('userForm');
    const storeUrl = form.getAttribute('data-store-url');
    // Replace last segment or just use base
    const baseUrl = storeUrl.replace(/\/users$/, '/users');
    form.action = `${baseUrl}/${id}`;

    // Inject Laravel PUT spoofing
    document.getElementById('methodContainer').innerHTML =
        '<input type="hidden" name="_method" value="PUT">';

    document.getElementById('userModal').classList.remove('hidden');
}

// ── Modal: Delete User ─────────────────────────────────────────
function openDeleteModal(id) {
    const form = document.getElementById('deleteForm');
    const storeUrl = document.getElementById('userForm').getAttribute('data-store-url');
    form.action = `${storeUrl}/${id}`;

    document.getElementById('deleteModal').classList.remove('hidden');
}

// ── Modal: Close ───────────────────────────────────────────────
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
}

// Close on backdrop click
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function () {
            const modal = this.closest('[role="dialog"]');
            if (modal) modal.classList.add('hidden');
        });
    });
});

// ── Sidebar & Dropdown Logic (handled globally in layouts/app.blade.php) ───────────────────

// ── Search: Client-side table filtering ───────────────────────
const searchInput = document.getElementById('userSearch');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#usersTableBody tr');
        rows.forEach(row => {
            if (row.children.length === 1) return; // skip empty-state row
            row.classList.toggle('hidden', !row.innerText.toLowerCase().includes(query));
        });
    });
}
