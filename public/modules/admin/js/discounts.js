/* ================================================
 * discounts.js — Enhanced Discount Codes & Coupons
 * ================================================ */

document.addEventListener('DOMContentLoaded', () => {
    // Initializer
    toggleDiscountTypeFields();
});

// Toast Alert Helper
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
 * One-Click Copy Discount Code
 */
function copyDiscountCode(codeText) {
    if (!codeText) return;
    navigator.clipboard.writeText(codeText).then(() => {
        showToast(`تم نسخ الكود (${codeText}) إلى الحافظة بنجاح!`);
    }).catch(err => {
        console.error('Copy failed:', err);
        showToast(`كود الخصم: ${codeText}`);
    });
}

/**
 * Auto Promo Code Generator
 */
function generateRandomPromoCode() {
    const prefixes = ['OFF-', 'DEAL-', 'SAVE-', 'PROMO-', 'FOOD-', 'YEMEN-'];
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Clean alphanumeric excluding ambiguous characters
    const randomPrefix = prefixes[Math.floor(Math.random() * prefixes.length)];
    
    let randomSuffix = '';
    for (let i = 0; i < 4; i++) {
        randomSuffix += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    const code = randomPrefix + randomSuffix;
    const input = document.getElementById('codeTitleInput') || document.getElementById('discountCodeInput');
    
    if (input) {
        input.value = code;
        
        // Subtle glow / highlight animation
        input.classList.add('ring-2', 'ring-indigo-500', 'bg-indigo-50/50');
        setTimeout(() => {
            input.classList.remove('ring-2', 'ring-indigo-500', 'bg-indigo-50/50');
        }, 600);
    }
    
    return code;
}

/**
 * Table Search & Type Filter
 */
function filterDiscountsTable() {
    const typeVal = document.getElementById('discountFilterType')?.value || 'ALL';
    const rows = document.querySelectorAll('.discount-row');

    rows.forEach(row => {
        const rowType = row.getAttribute('data-type');
        if (typeVal === 'ALL' || rowType === typeVal) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * AJAX Toggle Discount Active Status
 */
async function toggleDiscountStatus(discountId, toggleEl) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch(`/admin/discounts/${discountId}/toggle`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (response.ok && result.status) {
            showToast(result.message || 'تم تغيير حالة كود الخصم بنجاح.');
            if (toggleEl) {
                const row = toggleEl.closest('.discount-row');
                if (row) row.setAttribute('data-status', result.is_active ? 'active' : 'inactive');
            }
        } else {
            showToast(result.message || 'فشل تغيير حالة كود الخصم.', true);
            if (toggleEl) toggleEl.checked = !toggleEl.checked;
        }
    } catch (err) {
        console.error('Toggle Status Error:', err);
        showToast('حدث خطأ أثناء تغيير الحالة.', true);
        if (toggleEl) toggleEl.checked = !toggleEl.checked;
    }
}

/**
 * Delete Discount Code
 */
async function deleteDiscount(discountId) {
    if (!discountId) return;

    if (!confirm('هل أنت متأكد من حذف كود الخصم هذا؟')) {
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch(`/admin/discounts/${discountId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (response.ok && result.status) {
            showToast('تم حذف كود الخصم بنجاح.');
            window.location.reload();
        } else {
            showToast(result.message || 'حدث خطأ أثناء حذف الكود.', true);
        }
    } catch (err) {
        console.error('Delete Discount Error:', err);
        window.location.reload();
    }
}

/**
 * Open Create Modal
 */
function openCreateDiscountModal() {
    const form = document.getElementById('discountForm');
    if (form) {
        form.reset();
        form.action = '/admin/discounts';
    }

    const methodInput = document.getElementById('discountFormMethod');
    if (methodInput) methodInput.value = 'POST';

    const modalTitle = document.getElementById('discountModalTitle');
    if (modalTitle) modalTitle.innerText = "إنشاء كود خصم جديد";

    const btnSave = document.getElementById('btnSaveDiscount');
    if (btnSave) btnSave.innerText = "حفظ كود الخصم";

    toggleDiscountTypeFields();

    // Auto-generate promo code if input is empty
    const codeInput = document.getElementById('codeTitleInput') || document.getElementById('discountCodeInput');
    if (codeInput && !codeInput.value) {
        generateRandomPromoCode();
    }

    const modal = document.getElementById('discountModal');
    if (modal) modal.classList.remove('hidden-el');
}

/**
 * Open Edit Modal Pre-filled
 */
function editDiscount(codeData) {
    if (!codeData) return;

    const form = document.getElementById('discountForm');
    if (form) {
        form.action = `/admin/discounts/${codeData.id}`;
    }

    const methodInput = document.getElementById('discountFormMethod');
    if (methodInput) methodInput.value = 'PUT';

    const modalTitle = document.getElementById('discountModalTitle');
    if (modalTitle) modalTitle.innerText = "تعديل كود الخصم";

    const btnSave = document.getElementById('btnSaveDiscount');
    if (btnSave) btnSave.innerText = "تحديث كود الخصم";

    // Fill form inputs
    document.getElementById('codeTitleInput').value = codeData.code || '';
    document.getElementById('typeInput').value = codeData.discount_type || 'percentage';
    document.getElementById('valueInput').value = codeData.discount_value || '';
    document.getElementById('maxDiscountInput').value = codeData.max_discount_amount || '';
    document.getElementById('minOrderInput').value = codeData.min_order_amount || '';
    document.getElementById('limitInput').value = codeData.max_usages || '';
    document.getElementById('perUserLimitInput').value = codeData.per_user_limit || 1;

    if (codeData.expiry_date) {
        document.getElementById('expiryInput').value = codeData.expiry_date.split('T')[0];
    }

    const scopeSelect = document.getElementById('restaurantScopeInput');
    if (scopeSelect) {
        scopeSelect.value = codeData.restaurant_id || 'all';
    }

    const activeCheck = document.getElementById('discountActiveCheck');
    if (activeCheck) {
        activeCheck.checked = !!codeData.is_active;
    }

    toggleDiscountTypeFields();

    const modal = document.getElementById('discountModal');
    if (modal) modal.classList.remove('hidden-el');
}

function closeDiscountModal() {
    const modal = document.getElementById('discountModal');
    if (modal) modal.classList.add('hidden-el');
}

/**
 * Toggle Max Discount Cap Field visibility based on Discount Type
 */
function toggleDiscountTypeFields() {
    const typeSelect = document.getElementById('typeInput');
    const capContainer = document.getElementById('maxDiscountCapContainer');
    if (!typeSelect || !capContainer) return;

    if (typeSelect.value === 'percentage') {
        capContainer.classList.remove('hidden-el');
    } else {
        capContainer.classList.add('hidden-el');
    }
}
