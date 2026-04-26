/* ================================================
 * restaurants.js — Backend-integrated logic
 * ────────────────────────────────────────────────
 * Surgical DOM updates instead of page refreshes.
 * ================================================ */

/**
 * Enhanced Fetch Wrapper for JSON & Error handling
 */
async function fetchAPI(url, options = {}) {
    const defaultHeaders = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrfToken()
    };

    const config = {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers
        }
    };

    try {
        const response = await fetch(url, config);
        const text = await response.text();

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('[restaurants.js] Invalid JSON response:', text);
            throw new Error('Server returned an invalid response. Please check logs.');
        }

        if (!response.ok) {
            return {
                success: false,
                status: response.status,
                message: data.message || 'An error occurred',
                errors: data.errors || null
            };
        }

        return data; 
    } catch (error) {
        console.error('[restaurants.js] fetchAPI error:', error);
        return { success: false, message: error.message };
    }
}

function getCsrfToken() {
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken && metaToken.content) return metaToken.content;
    const inputToken = document.querySelector('input[name="_token"]');
    if (inputToken && inputToken.value) return inputToken.value;
    return '';
}

/**
 * Decodes a base64-encoded JSON string and opens the Edit/Details modal.
 */
function openEditB64(b64) {
    try {
        var data = JSON.parse(atob(b64));
        openEditModal(data);
    } catch (e) {
        console.error('[restaurants.js] openEditB64 decode error:', e);
    }
}

function openDetailsB64(b64) {
    try {
        var data = JSON.parse(atob(b64));
        openDetailsModal(data);
    } catch (e) {
        console.error('[restaurants.js] openDetailsB64 decode error:', e);
    }
}

/* ─── Modal Helpers ─── */

function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden-el');

    if (modalId === 'restaurantModal') {
        var form = document.getElementById('restaurantForm');
        form.reset();
        document.getElementById('restaurantId').value = '';
        document.getElementById('restaurant-modal-title').innerText = 'Add Restaurant';
        document.getElementById('methodContainer').innerHTML = '';
        
        var pwField = document.getElementById('rPasswordField');
        if (pwField) pwField.classList.remove('hidden-el');
        document.getElementById('rPassword').setAttribute('required', 'required');
    }
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden-el');
}

/* ─── CRUD Operations ─── */

async function saveRestaurant() {
    const form = document.getElementById('restaurantForm');
    if (!form) return;

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const event = window.event;
    const submitBtn = (event && event.target) ? (event.target.tagName === 'BUTTON' ? event.target : event.target.closest('button')) : null;
    const originalText = submitBtn ? submitBtn.innerText : 'Save';
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = 'Saving...';
    }

    const formData = new FormData(form);
    const id = document.getElementById('restaurantId').value;
    const url = id ? `/admin/restaurants/${id}` : '/admin/restaurants';

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch(e) {
            console.error('[restaurants.js] Save Error JSON:', text);
            throw new Error("Invalid server response.");
        }

        if (result.success) {
            updateTableUI(result.data, id ? 'update' : 'create');
            closeModal('restaurantModal');
        } else {
            if (result.errors) {
                const firstError = Object.values(result.errors)[0][0];
                alert(firstError);
            } else {
                alert(result.message || 'Verification failed. Please check your data.');
            }
        }
    } catch (error) {
        console.error('[restaurants.js] save error:', error);
        alert('An error occurred: ' + error.message);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        }
    }
}

function updateTableUI(restaurant, mode) {
    const tbody = document.getElementById('restaurantsTableBody');
    if (!tbody || !restaurant) return;

    // Build B64 strictly from the object as Blade does
    const b64 = btoa(unescape(encodeURIComponent(JSON.stringify(restaurant))));

    const statusObj = getAccountStatusBadge(restaurant.account_status);
    const stateObj = getStateButton(restaurant.id, restaurant.status);
    const blockIcon = getBlockIcon(restaurant.account_status);
    const blockBtnColor = (restaurant.account_status === 'Blocked') ? 'text-green-500 hover:text-green-700' : 'text-red-500 hover:text-red-700';

    const state = (restaurant.status || 'closed').toLowerCase();
    const accStatus = (restaurant.account_status || 'active').toLowerCase();

    const logoSrc = restaurant.logo_url || (restaurant.logo ? (restaurant.logo.includes('/') ? `/storage/${restaurant.logo}` : `/storage/restaurants/logos/${restaurant.logo}`) : `https://ui-avatars.com/api/?name=${encodeURIComponent(restaurant.name)}&background=random`);

    const rowHTMLContent = `
        <td class="px-6 py-4">
            <div class="flex items-center">
                <img class="res-logo h-10 w-10 rounded-lg border border-gray-200 mr-3 object-cover" src="${logoSrc}" alt="">
                <div>
                    <div class="res-name-text text-sm font-medium text-gray-900">${restaurant.name}</div>
                    <div class="res-status-subtext text-xs text-gray-500">${restaurant.status === 'open' ? 'Open' : 'Closed'}</div>
                </div>
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="res-owner-name text-sm text-gray-900">${restaurant.owner ? restaurant.owner.name : 'No Manager'}</div>
            <div class="res-owner-phone text-xs text-gray-400">${restaurant.owner ? restaurant.owner.phone : ''}</div>
        </td>
        <td class="px-6 py-4">
            <span class="res-category-badge px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-semibold">${restaurant.category}</span>
        </td>
        <td class="px-6 py-4 text-center">
            <span class="res-account-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusObj.class}">
                ${restaurant.account_status}
            </span>
        </td>
        <td class="px-6 py-4 text-center flex justify-center">
            ${stateObj}
        </td>
        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
            <button onclick="openDetailsB64('${b64}')" class="text-indigo-600 hover:text-indigo-900" title="Quick View">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
            <button onclick="openEditB64('${b64}')" class="text-blue-600 hover:text-blue-900" title="Edit"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
            <button onclick="blockRestaurant(${restaurant.id}, this)" class="${blockBtnColor}" title="${restaurant.account_status === 'Blocked' ? 'Unblock' : 'Block'}">
                ${blockIcon}
            </button>
            <button onclick="openDeleteModal(${restaurant.id})" class="text-red-600 hover:text-red-900" title="Delete"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
        </td>
    `;

    if (mode === 'update') {
        const existingRow = document.getElementById(`restaurant-row-${restaurant.id}`);
        if (existingRow) {
            existingRow.innerHTML = rowHTMLContent;
            existingRow.setAttribute('data-state', state);
            existingRow.setAttribute('data-account-status', accStatus);
        }
    } else {
        const newRowHTML = `<tr id="restaurant-row-${restaurant.id}" data-state="${state}" data-account-status="${accStatus}" class="hover:bg-gray-50 transition-colors">${rowHTMLContent}</tr>`;
        tbody.insertAdjacentHTML('afterbegin', newRowHTML);

        const emptyRow = tbody.querySelector('tr td[colspan="6"]');
        if (emptyRow) emptyRow.closest('tr').remove();
    }
}

function getAccountStatusBadge(status) {
    switch (status) {
        case 'Active': return { class: 'bg-green-100 text-green-800' };
        case 'Blocked': return { class: 'bg-red-100 text-red-800' };
        default: return { class: 'bg-gray-100 text-gray-800' };
    }
}

function getStateButton(id, status) {
    const isOpen = (status === 'open');
    const colorClass = isOpen ? 'border-green-200 bg-green-50 text-green-800 hover:bg-green-100' : 'border-red-200 bg-red-50 text-red-800 hover:bg-red-100';
    const dotClass = isOpen ? 'bg-green-500 animate-pulse' : 'bg-red-500';
    const text = isOpen ? 'Open' : 'Closed';

    return `
        <button onclick="toggleState(${id}, this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border focus:outline-none transition-all duration-200 ${colorClass}">
            <span class="availability-dot w-2 h-2 rounded-full ${dotClass}"></span>
            <span class="availability-text font-medium text-xs">${text}</span>
        </button>
    `;
}

function getBlockIcon(status) {
    if (status === 'Blocked') {
        return `<svg class="w-5 h-5 inline text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
    } else {
        return `<svg class="w-5 h-5 inline text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>`;
    }
}

function openEditModal(data) {
    // 1. Open and Initialize FIRST
    openModal('restaurantModal');

    // 2. Populate data strictly using IDs AFTER reset
    document.getElementById('restaurant-modal-title').innerText = 'Edit Restaurant';
    document.getElementById('restaurantId').value = data.id || '';
    
    document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';

    document.getElementById('rName').value = data.name || '';
    document.getElementById('rCategory').value = data.category || 'Fast Food';
    document.getElementById('rAddress').value = data.location || '';
    document.getElementById('rStatus').value = data.status || 'open';
    
    document.getElementById('rOwner').value = (data.owner ? data.owner.name : '') || '';
    document.getElementById('rPhone').value = (data.owner ? data.owner.phone : '') || '';
    document.getElementById('rEmail').value = (data.owner ? data.owner.email : '') || '';
    
    var pwField = document.getElementById('rPasswordField');
    if (pwField) pwField.classList.add('hidden-el');
    document.getElementById('rPassword').removeAttribute('required');
    document.getElementById('rPassword').value = '';
}

function openDetailsModal(data) {
    document.getElementById('detailResName').innerText = data.name || '—';
    document.getElementById('detailResCategory').innerText = data.category || '—';
    document.getElementById('detailResOwner').innerText = (data.owner ? data.owner.name : '—');
    document.getElementById('detailResPhone').innerText = (data.owner ? data.owner.phone : '—');
    document.getElementById('detailResEmail').innerText = (data.owner ? data.owner.email : '—');
    document.getElementById('detailResAddress').innerText = data.location || '—';
    
    var logoEl = document.getElementById('detailResLogo');
    if (logoEl) {
        const logoSrc = data.logo_url || (data.logo ? (data.logo.includes('/') ? `/storage/${data.logo}` : `/storage/restaurants/logos/${data.logo}`) : `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name)}&background=random`);
        logoEl.src = logoSrc;
    }

    openModal('restaurantDetailsModal');
}

function openDeleteModal(id) {
    window.restaurantToDelete = id;
    openModal('deleteModal');
}

async function confirmDelete() {
    if (!window.restaurantToDelete) return;
    
    const id = window.restaurantToDelete;
    const url = `/admin/restaurants/${id}`;

    const result = await fetchAPI(url, { method: 'DELETE' });

    if (result.success) {
        const row = document.getElementById(`restaurant-row-${id}`);
        if (row) row.remove();
        closeModal('deleteModal');
    } else {
        alert(result.message || 'Delete failed.');
    }
}

/* ─── Status & State Toggles ─── */

async function toggleState(id, btnEl) {
    btnEl.classList.add('opacity-50', 'pointer-events-none');

    const result = await fetchAPI(`/admin/restaurants/${id}/toggle-state`, {
        method: 'POST'
    });

    if (result.success) {
        const row = document.getElementById(`restaurant-row-${id}`);
        if (row) {
            const newState = result.is_open ? 'open' : 'closed';
            row.setAttribute('data-state', newState);

            // Update the button HTML using the helper
            const container = btnEl.parentElement;
            container.innerHTML = getStateButton(id, newState);
            
            // Also update the subtext in the name cell
            const subtext = row.querySelector('.res-status-subtext');
            if (subtext) subtext.innerText = result.is_open ? 'Open' : 'Closed';
        }
    } else {
        alert(result.message || 'Update failed');
    }
    btnEl.classList.remove('opacity-50', 'pointer-events-none');
}

async function blockRestaurant(id, btnEl) {
    if (!confirm('Are you sure you want to toggle the block status?')) return;

    btnEl.disabled = true;

    const result = await fetchAPI(`/admin/restaurants/${id}/toggle-block`, {
        method: 'POST'
    });

    if (result.success) {
        const row = document.getElementById(`restaurant-row-${id}`);
        if (row) {
            const newAccStatus = result.new_status.toLowerCase();
            row.setAttribute('data-account-status', newAccStatus);

            // Update badge
            const badge = row.querySelector('.res-account-status-badge');
            if (badge) {
                badge.innerText = result.new_status;
                const statusObj = getAccountStatusBadge(result.new_status);
                badge.className = `res-account-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusObj.class}`;
            }
            // Swap SVG and Title
            btnEl.innerHTML = getBlockIcon(result.new_status);
            btnEl.setAttribute('title', result.new_status === 'Blocked' ? 'Unblock' : 'Block');
            
            // Toggle button color classes
            if (result.new_status === 'Blocked') {
                btnEl.classList.remove('text-red-500', 'hover:text-red-700');
                btnEl.classList.add('text-green-500', 'hover:text-green-700');
            } else {
                btnEl.classList.remove('text-green-500', 'hover:text-green-700');
                btnEl.classList.add('text-red-500', 'hover:text-red-700');
            }
        }
    } else {
        alert(result.message || 'Block failed');
    }
    btnEl.disabled = false;
}

/* ─── Filters & Helpers ─── */

function filterRestaurants(filterType, btnEl) {
    const tbody = document.getElementById('restaurantsTableBody');
    if (!tbody) return;

    const rows = tbody.querySelectorAll('tr[id^="restaurant-row-"]');
    
    // Update toolbar active classes
    const buttons = document.querySelectorAll('#filterToolbar .filter-btn');
    buttons.forEach(btn => {
        btn.classList.remove('bg-primary', 'text-white', 'active-filter');
        btn.classList.add('bg-white', 'text-gray-600');
    });
    
    if (btnEl) {
        btnEl.classList.remove('bg-white', 'text-gray-600');
        btnEl.classList.add('bg-primary', 'text-white', 'active-filter');
    }

    rows.forEach(row => {
        const state = row.getAttribute('data-state');
        const accStatus = row.getAttribute('data-account-status');

        let show = false;
        if (filterType === 'all') show = true;
        else if (filterType === 'open') show = (state === 'open');
        else if (filterType === 'closed') show = (state === 'closed');
        else if (filterType === 'active') show = (accStatus === 'active');
        else if (filterType === 'inactive') show = (accStatus === 'inactive');
        else if (filterType === 'blocked') show = (accStatus === 'blocked');

        row.style.display = show ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('restaurantSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var query = this.value.toLowerCase().trim();
            var tbody = document.getElementById('restaurantsTableBody');
            if (!tbody) return;

            var rows = tbody.querySelectorAll('tr');
            rows.forEach(function (row) {
                var text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Handle ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            ['restaurantModal', 'restaurantDetailsModal', 'deleteModal', 'mealModal'].forEach(closeModal);
        }
    });

    // Handle Overlay clicks
    ['restaurantModal', 'restaurantDetailsModal', 'deleteModal'].forEach(id => {
        const modal = document.getElementById(id);
        if (modal) {
            const overlay = modal.querySelector('.modal-overlay');
            if (overlay) {
                overlay.addEventListener('click', () => closeModal(id));
            }
        }
    });
});
