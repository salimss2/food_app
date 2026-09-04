/* ================================================
 * offers.js — Enhanced Promotional Offers Logic
 * ================================================ */

document.addEventListener('DOMContentLoaded', () => {
    // Toast Alert Initializer if message stored in session
});

// Toast Helper
function showToast(msg, isError = false) {
    const toast = document.getElementById('toast');
    if (!toast) return;

    document.getElementById('toastMessage').innerText = msg;

    if (isError) {
        toast.className = 'fixed bottom-5 left-5 z-50 rounded-2xl bg-rose-600 text-white p-4 shadow-xl border border-rose-500 transition-all duration-300 flex items-center gap-3';
    } else {
        toast.className = 'fixed bottom-5 left-5 z-50 rounded-2xl bg-emerald-600 text-white p-4 shadow-xl border border-emerald-500 transition-all duration-300 flex items-center gap-3';
    }

    toast.classList.remove('hidden-el');
    setTimeout(() => { toast.classList.add('hidden-el'); }, 3500);
}

/**
 * Table Search & Status Filter
 */
function filterOffersTable() {
    const statusVal = document.getElementById('offerFilterStatus')?.value || 'ALL';
    const typeVal = document.getElementById('offerFilterType')?.value || 'ALL';

    const rows = document.querySelectorAll('.offer-row');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const rowType = row.getAttribute('data-type');

        const matchStatus = (statusVal === 'ALL' || rowStatus === statusVal);
        const matchType = (typeVal === 'ALL' || rowType === typeVal);

        if (matchStatus && matchType) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * Image Preview Popup Modal
 */
function previewBannerImage(url, title) {
    if (!url) return;
    const modal = document.getElementById('imagePreviewModal');
    const fullImg = document.getElementById('modalFullImage');
    const titleEl = document.getElementById('previewModalTitle');

    if (fullImg) fullImg.src = url;
    if (titleEl) titleEl.innerText = title || 'معاينة الصورة الترويجية';
    if (modal) modal.classList.remove('hidden-el');
}

function closeImagePreviewModal() {
    const modal = document.getElementById('imagePreviewModal');
    if (modal) modal.classList.add('hidden-el');
}

/**
 * AJAX Toggle Offer Live Status
 */
async function toggleOfferStatus(offerId, toggleEl) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch(`/admin/offers/${offerId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (response.ok && result.status) {
            showToast(result.message || 'تم تغيير حالة العرض بنجاح.');
            if (toggleEl) {
                const row = toggleEl.closest('.offer-row');
                if (row) row.setAttribute('data-status', result.new_status);
            }
        } else {
            showToast(result.message || 'فشل تغيير حالة العرض.', true);
            if (toggleEl) toggleEl.checked = !toggleEl.checked;
        }
    } catch (err) {
        console.error('Toggle Offer Status Error:', err);
        showToast('حدث خطأ أثناء تغيير الحالة.', true);
        if (toggleEl) toggleEl.checked = !toggleEl.checked;
    }
}

/**
 * AJAX Toggle Restaurant Combo Status
 */
async function toggleComboStatus(comboId, toggleEl) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch(`/admin/offers/combos/${comboId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (response.ok && result.status) {
            showToast(result.message || 'تم تغيير حالة الوجبة بنجاح.');
        } else {
            showToast(result.message || 'فشل تغيير حالة الوجبة.', true);
            if (toggleEl) toggleEl.checked = !toggleEl.checked;
        }
    } catch (err) {
        console.error('Toggle Combo Status Error:', err);
        showToast('حدث خطأ أثناء تغيير الحالة.', true);
        if (toggleEl) toggleEl.checked = !toggleEl.checked;
    }
}

/**
 * Delete Offer Action
 */
async function deleteOffer(offerId) {
    if (!offerId) return;

    if (!confirm('هل أنت متأكد من حذف هذا العرض الترويجي؟')) {
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch(`/admin/offers/${offerId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (response.ok && result.status) {
            showToast('تم حذف العرض الترويجي بنجاح.');
            window.location.reload();
        } else {
            showToast(result.message || 'حدث خطأ أثناء حذف العرض.', true);
        }
    } catch (err) {
        console.error('Delete Offer Error:', err);
        // Fallback form submission
        window.location.reload();
    }
}

/**
 * Delete Combo Action
 */
async function deleteCombo(comboId) {
    if (!comboId) return;

    if (!confirm('هل أنت متأكد من حذف هذه الوجبة المجمعة؟')) {
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch(`/admin/offers/combos/${comboId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (response.ok && result.status) {
            showToast('تم حذف الوجبة المجمعة بنجاح.');
            window.location.reload();
        } else {
            showToast(result.message || 'حدث خطأ أثناء الحذف.', true);
        }
    } catch (err) {
        console.error('Delete Combo Error:', err);
        window.location.reload();
    }
}

/**
 * Open Create Offer Modal
 */
function openCreateOfferModal() {
    const form = document.getElementById('offerForm');
    if (form) {
        form.reset();
        form.action = '/admin/offers';
    }

    const methodInput = document.getElementById('offerFormMethod');
    if (methodInput) methodInput.value = 'POST';

    const modalTitle = document.getElementById('offerModalTitle');
    if (modalTitle) modalTitle.innerText = "إنشاء عرض ترويجي جديد";

    const btnSave = document.getElementById('btnSaveOffer');
    if (btnSave) btnSave.innerText = "حفظ العرض";

    // Reset Image Preview Box
    const imgPreview = document.getElementById('bannerImagePreview');
    const imgPlaceholder = document.getElementById('bannerImagePlaceholder');
    if (imgPreview) {
        imgPreview.src = '';
        imgPreview.classList.add('hidden-el');
    }
    if (imgPlaceholder) imgPlaceholder.classList.remove('hidden-el');

    toggleOfferTypeFields('banner');

    const modal = document.getElementById('offerModal');
    if (modal) modal.classList.remove('hidden-el');
}

/**
 * Open Edit Offer Modal Pre-filled
 */
function editOffer(offer) {
    if (!offer) return;

    const form = document.getElementById('offerForm');
    if (form) {
        form.action = `/admin/offers/${offer.id}`;
    }

    const methodInput = document.getElementById('offerFormMethod');
    if (methodInput) methodInput.value = 'PUT';

    const modalTitle = document.getElementById('offerModalTitle');
    if (modalTitle) modalTitle.innerText = "تعديل العرض الترويجي";

    const btnSave = document.getElementById('btnSaveOffer');
    if (btnSave) btnSave.innerText = "تحديث العرض";

    // Fill form fields
    document.getElementById('offerTitleInput').value = offer.title || '';
    document.getElementById('offerDescInput').value = offer.description || '';
    document.getElementById('offerClickActionInput').value = offer.click_action || 'restaurant';
    document.getElementById('offerDiscountInput').value = offer.discount_percentage || '';
    document.getElementById('offerOrigPriceInput').value = offer.original_price || '';
    document.getElementById('offerPriceInput').value = offer.offer_price || '';

    if (offer.start_date) {
        document.getElementById('offerStartDateInput').value = offer.start_date.split('T')[0];
    }
    if (offer.expiry_date) {
        document.getElementById('offerExpiryInput').value = offer.expiry_date.split('T')[0];
    }

    const restaurantSelect = document.getElementById('offerRestaurantInput');
    if (restaurantSelect) {
        restaurantSelect.value = offer.restaurant_id || 'all';
    }

    const activeCheck = document.getElementById('offerActiveCheck');
    if (activeCheck) {
        activeCheck.checked = (offer.status === 'active');
    }

    // Handle Type radios
    const typeRadios = document.getElementsByName('type');
    const offerType = offer.type || 'banner';
    typeRadios.forEach(r => {
        r.checked = (r.value === offerType);
    });

    toggleOfferTypeFields(offerType);

    if (offer.meal_id) {
        const mealSelect = document.getElementById('offerMealInput');
        if (mealSelect) mealSelect.value = offer.meal_id;
    }

    // Handle image preview
    if (offer.image_url) {
        const imgPreview = document.getElementById('bannerImagePreview');
        const imgPlaceholder = document.getElementById('bannerImagePlaceholder');
        if (imgPreview) {
            imgPreview.src = offer.image_url;
            imgPreview.classList.remove('hidden-el');
        }
        if (imgPlaceholder) imgPlaceholder.classList.add('hidden-el');
    }

    const modal = document.getElementById('offerModal');
    if (modal) modal.classList.remove('hidden-el');
}

function closeOfferModal() {
    const modal = document.getElementById('offerModal');
    if (modal) modal.classList.add('hidden-el');
}

/**
 * Toggle Form Fields based on Offer Type (banner vs direct_cart)
 */
function toggleOfferTypeFields(type) {
    const mealSelectContainer = document.getElementById('mealSelectContainer');
    if (!mealSelectContainer) return;

    if (type === 'direct_cart') {
        mealSelectContainer.classList.remove('hidden-el');
        const clickActionSelect = document.getElementById('offerClickActionInput');
        if (clickActionSelect) clickActionSelect.value = 'cart';
    } else {
        mealSelectContainer.classList.add('hidden-el');
    }

    filterMealOptionsByRestaurant();
}

/**
 * Filter Meal options inside select based on chosen restaurant
 */
function filterMealOptionsByRestaurant() {
    const restaurantVal = document.getElementById('offerRestaurantInput')?.value;
    const mealSelect = document.getElementById('offerMealInput');
    if (!mealSelect) return;

    const options = mealSelect.querySelectorAll('option');
    options.forEach(opt => {
        const mealRestId = opt.getAttribute('data-restaurant');
        if (!opt.value) return; // Keep "اختر وجبة..."

        if (restaurantVal === 'all' || !mealRestId || mealRestId === restaurantVal) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });
}

/**
 * Auto-fill original price & parent restaurant when a meal is selected
 */
function autoFillMealPrice() {
    const mealSelect = document.getElementById('offerMealInput');
    if (!mealSelect) return;

    const selectedOpt = mealSelect.options[mealSelect.selectedIndex];
    if (selectedOpt && selectedOpt.value) {
        if (selectedOpt.hasAttribute('data-price')) {
            const price = selectedOpt.getAttribute('data-price');
            const origPriceInput = document.getElementById('offerOrigPriceInput');
            if (origPriceInput) {
                origPriceInput.value = Math.round(price);
            }
        }

        if (selectedOpt.hasAttribute('data-restaurant')) {
            const restId = selectedOpt.getAttribute('data-restaurant');
            const restSelect = document.getElementById('offerRestaurantInput');
            if (restSelect && restId) {
                restSelect.value = restId;
            }
        }
    }
}

/**
 * Live Image Upload Preview Box
 */
function handleImagePreview(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const imgPreview = document.getElementById('bannerImagePreview');
            const imgPlaceholder = document.getElementById('bannerImagePlaceholder');
            if (imgPreview) {
                imgPreview.src = e.target.result;
                imgPreview.classList.remove('hidden-el');
            }
            if (imgPlaceholder) imgPlaceholder.classList.add('hidden-el');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
