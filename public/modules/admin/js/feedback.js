/* ================================================
 * feedback.js — User Real-Time Feedback Logic (Arabic RTL)
 * ================================================ */

let currentPage = 1;
let debounceTimer = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchAnalytics();
    fetchRatings(1);

    const searchInput = document.getElementById('fbSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchRatings(1);
            }, 300);
        });
    }

    const filterSelect = document.getElementById('fbFilter');
    if (filterSelect) {
        filterSelect.addEventListener('change', () => {
            fetchRatings(1);
        });
    }

    const scoreSelect = document.getElementById('fbScoreFilter');
    if (scoreSelect) {
        scoreSelect.addEventListener('change', () => {
            fetchRatings(1);
        });
    }
});

/**
 * Fetch analytics data (Global satisfaction & distributions)
 */
async function fetchAnalytics() {
    try {
        const response = await fetch('/admin/api/ratings/analytics', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to fetch analytics');

        const result = await response.json();
        if (result.status && result.data) {
            renderAnalytics(result.data);
        }
    } catch (err) {
        console.error('Analytics Fetch Error:', err);
    }
}

/**
 * Render analytics summary card & distribution bars
 */
function renderAnalytics(data) {
    const globalSatValue = document.getElementById('globalSatValue');
    const globalSatStars = document.getElementById('globalSatStars');
    const totalReviewsCount = document.getElementById('totalReviewsCount');

    if (globalSatValue) {
        globalSatValue.innerText = data.global_satisfaction.toFixed(1);
    }

    if (totalReviewsCount) {
        totalReviewsCount.innerText = Number(data.total_reviews).toLocaleString('ar-EG');
    }

    if (globalSatStars) {
        globalSatStars.innerHTML = drawStarsHTML(data.global_satisfaction, 'w-6 h-6');
    }

    if (data.distributions) {
        for (let i = 1; i <= 5; i++) {
            const item = data.distributions[i] || { count: 0, percentage: 0 };
            const barEl = document.getElementById(`dist${i}Bar`);
            const pctEl = document.getElementById(`dist${i}Pct`);

            if (barEl) {
                barEl.style.width = `${item.percentage}%`;
            }
            if (pctEl) {
                pctEl.innerText = `${item.percentage}%`;
            }
        }
    }
}

/**
 * Fetch paginated feedback table data
 */
async function fetchRatings(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('fbTableBody');
    const emptyState = document.getElementById('emptyState');
    const paginationWrapper = document.getElementById('paginationWrapper');

    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">جاري تحميل التقييمات...</td></tr>`;

    const entityType = document.getElementById('fbFilter')?.value || 'ALL';
    const ratingScore = document.getElementById('fbScoreFilter')?.value || '';
    const search = document.getElementById('fbSearch')?.value || '';

    const url = new URL('/admin/api/ratings', window.location.origin);
    url.searchParams.set('page', page);
    url.searchParams.set('entity_type', entityType);
    if (ratingScore) url.searchParams.set('rating_score', ratingScore);
    if (search) url.searchParams.set('search', search);

    try {
        const response = await fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to fetch ratings');

        const result = await response.json();

        if (result.status && Array.isArray(result.data)) {
            renderTable(result.data);
            renderPagination(result.pagination);
        } else {
            throw new Error('Invalid data format');
        }
    } catch (err) {
        console.error('Fetch Ratings Error:', err);
        tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-red-600 font-semibold">⚠️ تعذر تحميل بيانات التقييمات.</td></tr>`;
        if (emptyState) emptyState.classList.add('hidden-el');
        if (paginationWrapper) paginationWrapper.classList.add('hidden-el');
    }
}

/**
 * Draw star SVG icons string helper
 */
function drawStarsHTML(score, starSizeClass = 'w-4 h-4') {
    const num = Math.round(score);
    let sHTML = '';
    for (let i = 1; i <= 5; i++) {
        const cClass = i <= num ? 'star-filled' : 'star-empty';
        sHTML += `<svg class="${starSizeClass} ${cClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>`;
    }
    return `<div class="flex gap-0.5 justify-start">${sHTML}</div>`;
}

/**
 * Render table rows in Arabic RTL format
 */
function renderTable(items) {
    const tbody = document.getElementById('fbTableBody');
    const emptyState = document.getElementById('emptyState');
    const paginationWrapper = document.getElementById('paginationWrapper');

    tbody.innerHTML = '';

    if (items.length === 0) {
        tbody.parentElement.classList.add('hidden-el');
        if (emptyState) emptyState.classList.remove('hidden-el');
        if (paginationWrapper) paginationWrapper.classList.add('hidden-el');
        return;
    }

    tbody.parentElement.classList.remove('hidden-el');
    if (emptyState) emptyState.classList.add('hidden-el');
    if (paginationWrapper) paginationWrapper.classList.remove('hidden-el');

    const badgeColors = [
        'bg-indigo-600 text-white',
        'bg-emerald-600 text-white',
        'bg-amber-600 text-white',
        'bg-purple-600 text-white',
        'bg-blue-600 text-white',
    ];

    items.forEach((f, idx) => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 transition-colors border-b';

        const user = f.customer_user || { name: 'عميل', initials: 'ع', avatar: '' };
        const colorClass = badgeColors[idx % badgeColors.length];

        const avatarMarkup = user.avatar && !user.avatar.includes('ui-avatars')
            ? `<img class="w-9 h-9 rounded-full border border-gray-200 ml-3 object-cover" src="${user.avatar}" alt="${user.name}">`
            : `<div class="w-9 h-9 rounded-full ${colorClass} font-bold text-xs flex items-center justify-center ml-3 shadow-sm">${user.initials}</div>`;

        let entityLabel = 'مطعم';
        let entityBadgeClass = 'bg-indigo-100 text-indigo-700 border-indigo-200';

        if (f.entity_type === 'DRIVER') {
            entityLabel = 'سائق';
            entityBadgeClass = 'bg-purple-100 text-purple-700 border-purple-200';
        } else if (f.entity_type === 'MEAL') {
            entityLabel = 'وجبة / أطعمة';
            entityBadgeClass = 'bg-amber-100 text-amber-700 border-amber-200';
        }

        let commentText = f.comment_preview || 'لا توجد ملاحظات مدونة.';
        if (commentText === 'No comment provided.') {
            commentText = 'لا توجد ملاحظات مدونة.';
        }

        let dateFormatted = f.created_at || '';
        dateFormatted = dateFormatted
            .replace(/^Today,\s*/i, 'اليوم، ')
            .replace(/^Yesterday,\s*/i, 'أمس، ');

        tr.innerHTML = `
            <td class="px-6 py-4 text-right">
                <div class="flex items-center">
                    ${avatarMarkup}
                    <span class="font-bold text-gray-900">${user.name}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-right">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border ${entityBadgeClass} mb-1">
                    ${entityLabel}
                </span>
                <div class="text-sm font-semibold text-indigo-600">${f.entity_name}</div>
            </td>
            <td class="px-6 py-4 text-right">
                ${drawStarsHTML(f.rating)}
            </td>
            <td class="px-6 py-4 text-right">
                <p class="text-sm text-gray-600 truncate max-w-md" title="${commentText}">
                    "${commentText}"
                </p>
            </td>
            <td class="px-6 py-4 text-left whitespace-nowrap text-sm text-gray-500">
                ${dateFormatted}
            </td>
        `;
        tbody.appendChild(tr);
    });
}

/**
 * Render pagination controls
 */
function renderPagination(pagination) {
    const info = document.getElementById('paginationInfo');
    const nav = document.getElementById('paginationNav');

    if (!info || !nav || !pagination) return;

    info.innerText = `عرض الصفحة ${pagination.current_page} من ${pagination.last_page} (${pagination.total} إجمالي العناصر)`;

    nav.innerHTML = '';

    if (pagination.last_page <= 1) return;

    // Previous Button (السابق)
    const prevBtn = document.createElement('button');
    prevBtn.className = `px-3 py-1 text-xs rounded border ${pagination.current_page > 1 ? 'bg-white text-gray-700 hover:bg-gray-100' : 'bg-gray-100 text-gray-400 cursor-not-allowed'}`;
    prevBtn.innerText = 'السابق';
    prevBtn.disabled = pagination.current_page <= 1;
    prevBtn.onclick = () => fetchRatings(pagination.current_page - 1);
    nav.appendChild(prevBtn);

    // Page Buttons
    for (let p = 1; p <= pagination.last_page; p++) {
        if (p === 1 || p === pagination.last_page || Math.abs(p - pagination.current_page) <= 2) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `px-3 py-1 text-xs rounded border font-semibold ${p === pagination.current_page ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 hover:bg-gray-100'}`;
            pageBtn.innerText = p;
            pageBtn.onclick = () => fetchRatings(p);
            nav.appendChild(pageBtn);
        }
    }

    // Next Button (التالي)
    const nextBtn = document.createElement('button');
    nextBtn.className = `px-3 py-1 text-xs rounded border ${pagination.current_page < pagination.last_page ? 'bg-white text-gray-700 hover:bg-gray-100' : 'bg-gray-100 text-gray-400 cursor-not-allowed'}`;
    nextBtn.innerText = 'التالي';
    nextBtn.disabled = pagination.current_page >= pagination.last_page;
    nextBtn.onclick = () => fetchRatings(pagination.current_page + 1);
    nav.appendChild(nextBtn);
}
