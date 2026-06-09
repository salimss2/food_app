/* ================================================
 * commissions-driver.js — Page-specific logic
 * ================================================ */

// Global Data
let commData = [];
let archiveData = [];
let activeTab = 'wallets'; // 'wallets' or 'archive'

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    fetchDriverWallets();
    const searchInput = document.getElementById('driverSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            if (activeTab === 'wallets') {
                renderTable();
            } else {
                renderArchiveTable();
            }
        });
    }
});

/**
 * Switch between Active Wallets and Settlement Archive tabs
 */
function switchTab(tab) {
    activeTab = tab;
    const tabWallets = document.getElementById('tabActiveWallets');
    const tabArchive = document.getElementById('tabSettlementArchive');
    const walletsContainer = document.getElementById('walletsContainer');
    const archiveContainer = document.getElementById('archiveContainer');
    const searchWrapper = document.getElementById('searchWrapper');

    if (tab === 'wallets') {
        // Classes update
        tabWallets.className = "px-4 py-2 text-sm font-bold border-b-2 border-primary text-primary transition-all focus:outline-none";
        tabArchive.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all focus:outline-none";

        // Show/Hide
        walletsContainer.classList.remove('hidden-el');
        archiveContainer.classList.add('hidden-el');
        if (searchWrapper) searchWrapper.classList.remove('hidden-el');

        fetchDriverWallets();
    } else {
        // Classes update
        tabWallets.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all focus:outline-none";
        tabArchive.className = "px-4 py-2 text-sm font-bold border-b-2 border-primary text-primary transition-all focus:outline-none";

        // Show/Hide
        walletsContainer.classList.add('hidden-el');
        archiveContainer.classList.remove('hidden-el');
        if (searchWrapper) searchWrapper.classList.add('hidden-el'); // hide search during archive

        fetchSettlementsArchive();
    }
}

/**
 * Fetch and aggregate active driver wallets
 */
async function fetchDriverWallets() {
    const tbody = document.getElementById('driverTableBody');
    const emptyState = document.getElementById('emptyState');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">جاري جلب البيانات... (Fetching Data)</td></tr>`;

    try {
        const response = await fetch('/admin/api/driver-wallets', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const result = await response.json();

        if (result.status && Array.isArray(result.data)) {
            commData = result.data;
            renderTable();
        } else {
            throw new Error('Data format is invalid');
        }

    } catch (error) {
        console.error('Fetch Error:', error);
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-red-600 font-bold">⚠️ خطأ في الاتصال: تعذر جلب بيانات السائقين (Fetch Failed)</td></tr>`;
        if (emptyState) emptyState.classList.add('hidden-el');
    }
}

/**
 * Fetch and render Settlements Archive receipts
 */
async function fetchSettlementsArchive() {
    const tbody = document.getElementById('archiveTableBody');
    const emptyState = document.getElementById('archiveEmptyState');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-10 text-center text-gray-500 italic">جاري جلب أرشيف التسويات... (Fetching Settlements Archive)</td></tr>`;

    try {
        const response = await fetch('/admin/api/settlements', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to fetch settlements');

        const result = await response.json();

        if (result.status && Array.isArray(result.data)) {
            archiveData = result.data;
            renderArchiveTable();
        } else {
            throw new Error('Data format is invalid');
        }

    } catch (error) {
        console.error('Archive Fetch Error:', error);
        tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-10 text-center text-red-600 font-bold">⚠️ خطأ: تعذر تحميل أرشيف التسويات</td></tr>`;
        if (emptyState) emptyState.classList.add('hidden-el');
    }
}

/**
 * Format currency helper
 */
function fixCurrency(num) {
    const val = parseFloat(num);
    const formatted = Math.abs(val).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    if (val < 0) {
        return `(${formatted} YER)`;
    }
    return `${formatted} YER`;
}

/**
 * View active unsettled deliveries for a specific driver in breakdown modal
 */
async function viewBreakdown(index) {
    const driver = commData[index];
    const breakdownTitleEl = document.getElementById('breakdownTitle');
    if (breakdownTitleEl) {
        breakdownTitleEl.innerText = `${driver.name} - Breakdown`;
    }

    const tbody = document.querySelector('#breakdownModal tbody');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-400 italic">جاري جلب تفاصيل التوصيل... (Fetching delivery breakdown)</td></tr>';

    const modal = document.getElementById('breakdownModal');
    if (modal) {
        modal.classList.remove('hidden-el');
    }

    try {
        const response = await fetch(`/admin/api/driver-wallets/${driver.id}/deliveries`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to fetch deliveries');

        const result = await response.json();

        if (result.status && Array.isArray(result.data)) {
            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-400 italic">لا توجد توصيلات غير مسواة حالياً (No unsettled deliveries found)</td></tr>';
            } else {
                tbody.innerHTML = '';
                result.data.forEach(order => {
                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-gray-50 border-b";

                    const orderDate = new Date(order.created_at).toLocaleString('ar-EG', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const distanceVal = order.distance || order.delivery_distance || 0;

                    tr.innerHTML = `
                        <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">#${order.id}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">${orderDate}</td>
                        <td class="px-4 py-3 text-center text-sm font-medium text-gray-900">${order.payment_method.toUpperCase()}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">${parseFloat(distanceVal).toFixed(2)} km</td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-600">${parseFloat(order.delivery_fee).toFixed(2)} YER</td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-red-600">${parseFloat(order.platform_commission).toFixed(2)} YER</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-green-600">${parseFloat(order.driver_commission).toFixed(2)} YER</td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        } else {
            throw new Error('Data format invalid');
        }
    } catch (error) {
        console.error('Fetch Error:', error);
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-red-600 font-semibold">⚠️ خطأ: تعذر تحميل تفاصيل التوصيلات</td></tr>';
    }
}

/**
 * Settle active driver balance (audit-ready settlement receipt created)
 */
async function settleAccount(index) {
    const driver = commData[index];
    const confirmMessage = `هل أنت متأكد من تسوية حساب السائق ${driver.name}؟\nالرصيد الصافي: ${fixCurrency(driver.netBalance)}`;
    if (!confirm(confirmMessage)) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        const response = await fetch(`/admin/api/driver-wallets/${driver.id}/settle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to settle driver balance');

        const result = await response.json();

        if (result.status && result.success) {
            alert(`تمت تسوية حساب السائق ${driver.name} بنجاح!\nرقم إيصال التسوية: ${result.settlement.settlement_number}`);

            // Close modal if open
            const modal = document.getElementById('breakdownModal');
            if (modal) {
                modal.classList.add('hidden-el');
            }

            // Refresh table data
            fetchDriverWallets();
        } else {
            throw new Error(result.message || 'Settlement failed');
        }
    } catch (error) {
        console.error('Settlement Error:', error);
        alert('⚠️ خطأ: حدثت مشكلة أثناء محاولة تسوية الحساب. يرجى المحاولة مرة أخرى.');
    }
}

/**
 * View detailed orders linked to a past settlement receipt
 */
async function viewReceipt(settlementId, settlementNumber) {
    const titleEl = document.getElementById('receiptTitle');
    const subEl = document.getElementById('receiptSub');
    const tbody = document.querySelector('#receiptModal tbody');
    const btnExport = document.getElementById('btnExportReceiptCSV');

    if (titleEl) titleEl.innerText = `Receipt Details: ${settlementNumber}`;
    if (subEl) subEl.innerText = `Detailed orders linked to settlement receipt ${settlementNumber}`;
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-400 italic">جاري تحميل تفاصيل الإيصال... (Loading Receipt Details)</td></tr>';

    const modal = document.getElementById('receiptModal');
    if (modal) {
        modal.classList.remove('hidden-el');
    }

    try {
        const response = await fetch(`/admin/api/settlements/${settlementId}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Failed to fetch settlement details');

        const result = await response.json();

        if (result.status && Array.isArray(result.data)) {
            tbody.innerHTML = '';
            result.data.forEach(order => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-gray-50 border-b";

                const orderDate = new Date(order.created_at).toLocaleString('ar-EG', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const distanceVal = order.distance || order.delivery_distance || 0;

                tr.innerHTML = `
                    <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">#${order.id}</td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">${orderDate}</td>
                    <td class="px-4 py-3 text-center text-sm font-medium text-gray-900">${order.payment_method.toUpperCase()}</td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">${parseFloat(distanceVal).toFixed(2)} km</td>
                    <td class="px-4 py-3 text-right text-sm font-medium text-gray-600">${parseFloat(order.delivery_fee).toFixed(2)} YER</td>
                    <td class="px-4 py-3 text-right text-sm font-medium text-red-600">${parseFloat(order.platform_commission).toFixed(2)} YER</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-green-600">${parseFloat(order.driver_commission).toFixed(2)} YER</td>
                `;
                tbody.appendChild(tr);
            });

            // Update export handler for this dynamic receipt
            if (btnExport) {
                btnExport.onclick = () => exportReceiptToCSV(settlementNumber);
            }

        } else {
            throw new Error('Data format invalid');
        }
    } catch (error) {
        console.error('Fetch Details Error:', error);
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-red-600 font-semibold">⚠️ خطأ: تعذر تحميل تفاصيل الإيصال</td></tr>';
    }
}

/**
 * Render active drivers wallets table
 */
function renderTable() {
    const search = document.getElementById('driverSearch')?.value.toLowerCase() || '';
    const tbody = document.getElementById('driverTableBody');
    const emptyState = document.getElementById('emptyState');

    if (!tbody) return;

    tbody.innerHTML = "";

    let display = commData;
    if (search) {
        display = display.filter(d => d.name.toLowerCase().includes(search));
    }

    if (display.length === 0) {
        tbody.parentElement.classList.add('hidden-el');
        if (emptyState) emptyState.classList.remove('hidden-el');
        return;
    } else {
        tbody.parentElement.classList.remove('hidden-el');
        if (emptyState) emptyState.classList.add('hidden-el');
    }

    display.forEach((d, index) => {
        const balanceColor = d.netBalance >= 0 ? "text-green-600" : "text-red-600";

        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-50 transition-colors border-b";
        tr.innerHTML = `
            <td class="px-6 py-4 font-bold text-gray-900 flex items-center">
                <img class="w-8 h-8 rounded-full border border-gray-200 mr-3" src="${d.avatar}">
                ${d.name}
            </td>
            <td class="px-6 py-4 text-center font-medium text-gray-600">${d.deliveries}</td>
            <td class="px-6 py-4 text-right font-medium text-gray-900">${d.driverEarnings.toFixed(2)} YER</td>
            <td class="px-6 py-4 text-right font-medium text-orange-600">${d.cashInHand.toFixed(2)} YER</td>
            <td class="px-6 py-4 text-right font-bold ${balanceColor}">${fixCurrency(d.netBalance)}</td>
            <td class="px-6 py-4 text-right whitespace-nowrap">
                <div class="flex flex-col space-y-1 items-end">
                    <button onclick="viewBreakdown(${index})" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs">View Deliveries</button>
                    <button onclick="settleAccount(${index})" class="text-purple-600 hover:text-purple-900 font-bold text-xs uppercase tracking-tight">Settle Account</button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

/**
 * Render archive settlements receipts table
 */
function renderArchiveTable() {
    const tbody = document.getElementById('archiveTableBody');
    const emptyState = document.getElementById('archiveEmptyState');

    if (!tbody) return;

    tbody.innerHTML = "";

    if (archiveData.length === 0) {
        tbody.parentElement.classList.add('hidden-el');
        if (emptyState) emptyState.classList.remove('hidden-el');
        return;
    } else {
        tbody.parentElement.classList.remove('hidden-el');
        if (emptyState) emptyState.classList.add('hidden-el');
    }

    archiveData.forEach((s) => {
        const netColor = s.net_amount >= 0 ? "text-green-600" : "text-red-600";
        const dateStr = new Date(s.date).toLocaleDateString('ar-EG', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });

        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-50 transition-colors border-b";
        tr.innerHTML = `
            <td class="px-6 py-4 font-bold text-gray-900">${s.settlement_number}</td>
            <td class="px-6 py-4 font-medium text-gray-600">${dateStr}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${s.driver_name}</td>
            <td class="px-6 py-4 text-right font-medium text-gray-600">${s.total_earnings.toFixed(2)} YER</td>
            <td class="px-6 py-4 text-right font-medium text-orange-600">${s.total_cash.toFixed(2)} YER</td>
            <td class="px-6 py-4 text-right font-bold ${netColor}">${fixCurrency(s.net_amount)}</td>
            <td class="px-6 py-4 text-gray-700 font-semibold">${s.admin_name}</td>
            <td class="px-6 py-4 text-right whitespace-nowrap">
                <button onclick="viewReceipt(${s.id}, '${s.settlement_number}')" class="text-purple-600 hover:text-purple-900 font-bold text-xs uppercase tracking-tight">View Receipt</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

/**
 * Client-side CSV export of currently active breakdown list
 */
function exportBreakdownToCSV() {
    const title = document.getElementById('breakdownTitle')?.innerText || '';
    const driverName = title.split(' - ')[0].trim() || 'Driver';

    const table = document.querySelector('#breakdownModal table');
    if (!table) return;

    exportTableToCSV(table, `${driverName}_Deliveries_Breakdown.csv`);
}

/**
 * Client-side CSV export of past settlement receipt list
 */
function exportReceiptToCSV(settlementNumber) {
    const table = document.querySelector('#receiptModal table');
    if (!table) return;

    exportTableToCSV(table, `${settlementNumber}_Receipt_Details.csv`);
}

/**
 * Global helper to extract table contents and trigger CSV download
 */
function exportTableToCSV(table, filename) {
    const headers = [];
    const headerCols = table.querySelectorAll('thead th');
    headerCols.forEach(th => {
        headers.push(`"${th.innerText.trim().replace(/"/g, '""')}"`);
    });

    const rows = [];
    rows.push(headers.join(','));

    const bodyRows = table.querySelectorAll('tbody tr');
    if (bodyRows.length === 0 || (bodyRows.length === 1 && bodyRows[0].cells.length === 1)) {
        alert('لا توجد بيانات لتصديرها (No data available to export)');
        return;
    }

    bodyRows.forEach(tr => {
        const rowData = [];
        const cols = tr.querySelectorAll('td');
        cols.forEach(td => {
            rowData.push(`"${td.innerText.trim().replace(/"/g, '""')}"`);
        });
        rows.push(rowData.join(','));
    });

    const csvContent = "\uFEFF" + rows.join("\n"); // Unicode BOM
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
