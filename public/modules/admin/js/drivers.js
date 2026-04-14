/* ================================================
 * drivers.js — Backend-integrated logic
 * ────────────────────────────────────────────────
 * Data passing strategy: all driver data is passed
 * as base64(JSON) via data attributes, then decoded
 * here — 100% safe against any special characters.
 * ================================================ */

/* ─── Base64 JSON Decoders (called from onclick) ─── */

/**
 * Decodes a base64-encoded JSON string and opens the Edit modal.
 * Called via: onclick="openEditB64(this.dataset.driver)"
 */
function openEditB64(b64) {
    try {
        var data = JSON.parse(atob(b64));
        openEditModal(data);
    } catch (e) {
        console.error('[drivers.js] openEditB64 decode error:', e);
    }
}

/**
 * Decodes a base64-encoded JSON string and opens the Quick-View modal.
 * Called via: onclick="openDetailsB64(this.dataset.qv)"
 */
function openDetailsB64(b64) {
    try {
        var data = JSON.parse(atob(b64));
        openDetailsFromServer(data);
    } catch (e) {
        console.error('[drivers.js] openDetailsB64 decode error:', e);
    }
}

/* ─── Modal Helpers ─── */

function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');

    if (modalId === 'driverModal') {
        var form = document.getElementById('driverForm');
        // Reset to "Add" mode
        form.action = form.dataset.storeUrl;
        document.getElementById('driverMethodField').innerHTML = '';
        document.getElementById('driver-modal-title').innerText = 'Add Driver';
        // Password required for new driver
        var pwStar = document.getElementById('passwordRequiredStar');
        if (pwStar) pwStar.style.display = 'inline';
        document.getElementById('driverPassword').setAttribute('required', 'required');
        // Clear all fields
        form.reset();
        // Clear any lingering validation errors
        clearValidationErrors();
    }
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
}

/* ─── Edit Driver Modal ─── */

function openEditModal(data) {
    var form = document.getElementById('driverForm');
    if (!form) { console.error('[drivers.js] #driverForm not found'); return; }

    // Switch to PUT mode (Laravel method spoofing)
    var updateUrl = DRIVER_UPDATE_URL_TEMPLATE + '/' + data.id;
    form.action = updateUrl;
    document.getElementById('driverMethodField').innerHTML =
        '<input type="hidden" name="_method" value="PUT">';

    // Modal UI state
    document.getElementById('driver-modal-title').innerText = 'Edit Driver';
    var pwStar = document.getElementById('passwordRequiredStar');
    if (pwStar) pwStar.style.display = 'none';
    document.getElementById('driverPassword').removeAttribute('required');
    document.getElementById('driverPassword').value = '';

    // Populate User fields
    document.getElementById('driverName').value = data.name || '';
    document.getElementById('driverEmail').value = data.email || '';
    document.getElementById('driverPhone').value = data.phone || '';
    document.getElementById('driverStatus').value = data.status || 'Active';

    // Populate Profile fields
    document.getElementById('driverIdNumber').value = data.idNumber || '';
    document.getElementById('driverAddress').value = data.address || '';
    document.getElementById('vehicleModel').value = data.vehicleModel || '';
    document.getElementById('vehiclePlate').value = data.vehiclePlate || '';
    document.getElementById('vehicleVin').value = data.vehicleVin || '';

    // Clear old validation errors
    clearValidationErrors();

    // Show modal
    document.getElementById('driverModal').classList.remove('hidden');
}

/* ─── Delete Driver ─── */

function openDeleteModal(driverId) {
    var deleteUrl = DRIVER_DELETE_URL_TEMPLATE + '/' + driverId;
    document.getElementById('deleteDriverForm').action = deleteUrl;
    document.getElementById('deleteModal').classList.remove('hidden');
}

/* ─── Quick View (Details) Modal ─── */

function openDetailsFromServer(data) {
    var avatarUrl = data.avatar
        || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(data.name) + '&background=4f46e5&color=fff&size=128');

    var set = function (id, val) {
        var el = document.getElementById(id);
        if (el) el.innerText = val || '—';
    };

    var setImg = function (id, src) {
        var el = document.getElementById(id);
        if (el) el.src = src;
    };

    setImg('detailAvatar', avatarUrl);
    set('detailName', data.name);
    set('detailPhone', data.phone);
    set('detailEmail', data.email || 'N/A');
    set('detailIdNumber', data.idNumber);
    set('detailAddress', data.address || 'N/A');
    set('detailVehicleModel', data.vModel);
    set('detailPlate', data.pNumber);
    set('detailVin', data.vin);

    var statusEl = document.getElementById('detailStatus');
    if (statusEl) {
        statusEl.innerText = data.status || '—';
        var isActive = data.status && data.status.toLowerCase() === 'active';
        statusEl.className = isActive
            ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1 bg-green-100 text-green-800'
            : 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1 bg-red-100 text-red-800';
    }

    document.getElementById('detailsModal').classList.remove('hidden');
}

/* ─── Validation Error Helpers ─── */

function clearValidationErrors() {
    var errorIds = ['err-name', 'err-phone', 'err-email', 'err-password'];
    errorIds.forEach(function (id) {
        var el = document.getElementById(id);
        if (el) {
            el.innerText = '';
            el.classList.add('hidden');
        }
    });
    // Remove red border from inputs
    ['driverName', 'driverPhone', 'driverEmail', 'driverPassword'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.classList.remove('border-red-500');
    });
}

function showValidationErrors(errors) {
    /* errors: { field_name: ['error message', ...] }
       e.g. { name: ['The name field is required.'], email: ['...'] } */
    var fieldMap = {
        'name': { err: 'err-name', input: 'driverName' },
        'phone': { err: 'err-phone', input: 'driverPhone' },
        'email': { err: 'err-email', input: 'driverEmail' },
        'password': { err: 'err-password', input: 'driverPassword' },
    };
    Object.keys(errors).forEach(function (field) {
        if (fieldMap[field]) {
            var errEl = document.getElementById(fieldMap[field].err);
            var inputEl = document.getElementById(fieldMap[field].input);
            if (errEl) {
                errEl.innerText = errors[field][0];
                errEl.classList.remove('hidden');
            }
            if (inputEl) inputEl.classList.add('border-red-500');
        }
    });
}

/* ─── Toggle Availability ─── */

function toggleAvailability(driverId, btnEl) {
    if (btnEl.disabled) return;
    btnEl.disabled = true;

    var originalClass = btnEl.className;
    var originalHtml = btnEl.innerHTML;

    btnEl.classList.add('opacity-50', 'cursor-wait');

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                  || document.querySelector('input[name="_token"]')?.value 
                  || window.Laravel?.csrfToken;

    if (!token) { 
        console.error("CSRF token not found!"); 
        alert("Security token missing. Please refresh the page."); 
        btnEl.classList.remove('opacity-50', 'cursor-wait');
        btnEl.disabled = false;
        return; 
    }

    var url = '/admin/drivers/toggle-availability/' + driverId;
    
    console.log('Sending request to:', url);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        }
    })
    .then(async (response) => {
        const text = await response.text();
        console.log('Raw Response:', text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('[drivers.js] JSON Parse Error:', e);
            if (response.ok) {
                // If DB was updated (200 OK) but JSON failed, we can deduce some state 
                // or just alert the error. Let's alert to be safe but informative.
                alert("Server updated the database but returned an invalid format. Please refresh the page to see the final status.");
            } else {
                alert("Server Error (Non-JSON):\n" + text.substring(0, 500));
            }
            throw new Error("Invalid response format");
        }

        if (!response.ok) {
            throw new Error(data.message || 'Server Error');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            // Update UI components (Dot and Text)
            const isOnline = data.is_online;
            const dot = btnEl.querySelector('.availability-dot');
            const text = btnEl.querySelector('.availability-text');
            
            if (isOnline) {
                btnEl.className = "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border focus:outline-none transition-all duration-200 border-green-200 bg-green-50 text-green-800 hover:bg-green-100";
                if (dot) dot.className = "availability-dot w-2 h-2 rounded-full bg-green-500 animate-pulse";
                if (text) text.innerText = "Online";
            } else {
                btnEl.className = "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border focus:outline-none transition-all duration-200 border-red-200 bg-red-50 text-red-800 hover:bg-red-100";
                if (dot) dot.className = "availability-dot w-2 h-2 rounded-full bg-red-500";
                if (text) text.innerText = "Offline";
            }
        } else {
            alert(data.message || 'Error occurred while updating availability.');
            btnEl.className = originalClass;
            btnEl.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('[drivers.js] toggleAvailability error:', error);
        if (error.message !== "Invalid response format") {
            alert('Error: ' + error.message);
        }
        btnEl.className = originalClass;
        btnEl.innerHTML = originalHtml;
    })
    .finally(() => {
        btnEl.classList.remove('opacity-50', 'cursor-wait');
        btnEl.disabled = false;
    });
}


/* ─── Client-side Search / Filter ─── */

document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('driverSearch');
    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        var query = this.value.toLowerCase().trim();
        var tbody = document.getElementById('driversTableBody');
        if (!tbody) return;

        var rows = tbody.querySelectorAll('tr:not(.no-results-row):not(#emptyStateRow)');
        var visibleCount = 0;

        rows.forEach(function (row) {
            var text = row.innerText.toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Handle "no results" feedback
        var noRow = tbody.querySelector('.no-results-row');
        if (visibleCount === 0 && rows.length > 0) {
            if (!noRow) {
                noRow = document.createElement('tr');
                noRow.className = 'no-results-row';
                noRow.innerHTML = '<td colspan="6" class="px-6 py-12 text-center">'
                    + '<div class="flex flex-col items-center gap-2">'
                    + '<svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>'
                    + '<p class="text-sm font-medium text-gray-500">No drivers match <span class="font-bold">&ldquo;' + query.replace(/</g, '&lt;') + '&rdquo;</span></p>'
                    + '<p class="text-xs text-gray-400">Try a different name or phone number.</p>'
                    + '</div></td>';
                tbody.appendChild(noRow);
            } else {
                // Update the search term in the message
                noRow.querySelector('p span') && (noRow.querySelector('p span').innerText = '"' + query + '"');
            }
            noRow.style.display = '';
        } else if (noRow) {
            noRow.style.display = 'none';
        }
    });
});

/* ─── Close modals on overlay click ─── */

document.addEventListener('DOMContentLoaded', function () {
    ['driverModal', 'detailsModal', 'deleteModal'].forEach(function (id) {
        var modal = document.getElementById(id);
        if (!modal) return;
        var overlay = modal.querySelector('.modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', function () {
                closeModal(id);
            });
        }
    });
});

/* ─── ESC key closes the topmost open modal ─── */

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        ['driverModal', 'detailsModal', 'deleteModal'].forEach(function (id) {
            var modal = document.getElementById(id);
            if (modal && !modal.classList.contains('hidden')) {
                closeModal(id);
            }
        });
    }
});
