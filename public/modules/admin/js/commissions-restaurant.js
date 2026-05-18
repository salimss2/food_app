/* ========================================================
 * commissions-restaurant.js — Interactive Restaurant settlements
 * ======================================================== */

// Global state
let activeTab = 'wallets'; // 'wallets' or 'archive'
let walletsData = [];
let archiveData = [];
let activeBreakdownOrders = [];
let activeReceiptOrders = [];

// CSRF helper
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

// Currency formatting (SAR)
function formatCurrency(amount) {
    return parseFloat(amount).toFixed(2) + " SAR";
}

// Date formatting
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Switch between Active Balances and Settlement History
function switchTab(tab) {
    activeTab = tab;
    
    const tabActive = document.getElementById('tabActiveBalances');
    const tabHistory = document.getElementById('tabSettlementHistory');
    const walletsContainer = document.getElementById('walletsContainer');
    const archiveContainer = document.getElementById('archiveContainer');
    
    if (tab === 'wallets') {
        tabActive.className = "px-4 py-2 text-sm font-bold border-b-2 border-primary text-primary transition-all focus:outline-none";
        tabHistory.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all focus:outline-none";
        walletsContainer.classList.remove('hidden-el');
        archiveContainer.classList.add('hidden-el');
        fetchWallets();
    } else {
        tabActive.className = "px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all focus:outline-none";
        tabHistory.className = "px-4 py-2 text-sm font-bold border-b-2 border-primary text-primary transition-all focus:outline-none";
        walletsContainer.classList.add('hidden-el');
        archiveContainer.classList.remove('hidden-el');
        fetchArchive();
    }
}

// Search filtering trigger
document.getElementById('restaurantSearch').addEventListener('input', () => {
    if (activeTab === 'wallets') {
        renderWallets();
    } else {
        renderArchive();
    }
});

// 1. Fetch active restaurant wallets
async function fetchWallets() {
    try {
        const response = await fetch('/admin/api/restaurant-wallets');
        const result = await response.json();
        if (result.status) {
            walletsData = result.data;
            renderWallets();
        }
    } catch (error) {
        console.error('Error fetching restaurant wallets:', error);
    }
}

// Render active wallets table
function renderWallets() {
    const search = document.getElementById('restaurantSearch').value.toLowerCase();
    const tbody = document.getElementById('restTableBody');
    const emptyState = document.getElementById('walletsEmptyState');
    tbody.innerHTML = "";

    let displayData = walletsData;
    if (search) {
        displayData = displayData.filter(item => item.name.toLowerCase().includes(search));
    }

    if (displayData.length === 0) {
        tbody.parentElement.classList.add('hidden-el');
        emptyState.classList.remove('hidden-el');
        return;
    } else {
        tbody.parentElement.classList.remove('hidden-el');
        emptyState.classList.add('hidden-el');
    }

    displayData.forEach(item => {
        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-50 transition-colors";
        
        tr.innerHTML = `
            <td class="px-6 py-4 flex items-center space-x-3">
                <img src="${item.logo}" alt="${item.name}" class="w-10 h-10 rounded-full border shadow-sm object-cover">
                <span class="font-bold text-gray-900 mx-2">${item.name}</span>
            </td>
            <td class="px-6 py-4 text-center text-gray-600 font-semibold">${item.orders_count}</td>
            <td class="px-6 py-4 text-right font-medium text-gray-900">${formatCurrency(item.gross_revenue)}</td>
            <td class="px-6 py-4 text-center font-bold bg-gray-50/50">${item.commission_rate}%</td>
            <td class="px-6 py-4 text-right font-bold text-red-600 bg-red-50/10">-${formatCurrency(item.system_cut)}</td>
            <td class="px-6 py-4 text-right font-bold text-green-600 bg-green-50/10">${formatCurrency(item.net_payable)}</td>
            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                <button onclick="viewBreakdown(${item.id}, '${item.name}', ${item.commission_rate})" class="text-indigo-600 hover:text-indigo-900 font-bold text-sm bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                    View Details (التفاصيل)
                </button>
                ${item.orders_count > 0 ? `
                    <button onclick="settleAccount(${item.id}, '${item.name}', ${item.gross_revenue}, ${item.system_cut}, ${item.net_payable})" class="text-white hover:bg-success-dark font-bold text-sm bg-success px-3 py-1.5 rounded-lg transition-colors">
                        Settle Balance (تسوية الحساب)
                    </button>
                ` : `
                    <button disabled class="text-gray-400 font-bold text-sm bg-gray-100 px-3 py-1.5 rounded-lg cursor-not-allowed">
                        Settle Balance
                    </button>
                `}
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// 2. Fetch past settlement receipts (Archive)
async function fetchArchive() {
    try {
        const response = await fetch('/admin/api/restaurant-settlements');
        const result = await response.json();
        if (result.status) {
            archiveData = result.data;
            renderArchive();
        }
    } catch (error) {
        console.error('Error fetching settlement history:', error);
    }
}

// Render settlement history table
function renderArchive() {
    const search = document.getElementById('restaurantSearch').value.toLowerCase();
    const tbody = document.getElementById('archiveTableBody');
    const emptyState = document.getElementById('archiveEmptyState');
    tbody.innerHTML = "";

    let displayData = archiveData;
    if (search) {
        displayData = displayData.filter(item => item.restaurant_name.toLowerCase().includes(search));
    }

    if (displayData.length === 0) {
        tbody.parentElement.classList.add('hidden-el');
        emptyState.classList.remove('hidden-el');
        return;
    } else {
        tbody.parentElement.classList.remove('hidden-el');
        emptyState.classList.add('hidden-el');
    }

    displayData.forEach(item => {
        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-50 transition-colors";
        
        tr.innerHTML = `
            <td class="px-6 py-4 font-bold text-gray-900">${item.settlement_number}</td>
            <td class="px-6 py-4 text-gray-600 text-sm">${formatDate(item.date)}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${item.restaurant_name}</td>
            <td class="px-6 py-4 text-right font-medium text-gray-900">${formatCurrency(item.gross_revenue)}</td>
            <td class="px-6 py-4 text-right font-semibold text-red-600 bg-red-50/10">-${formatCurrency(item.system_cut)}</td>
            <td class="px-6 py-4 text-right font-bold text-green-600 bg-green-50/10">${formatCurrency(item.net_payable)}</td>
            <td class="px-6 py-4 text-center text-sm font-medium text-gray-600">${item.admin_name}</td>
            <td class="px-6 py-4 text-right whitespace-nowrap">
                <button onclick="viewReceipt(${item.id}, '${item.settlement_number}')" class="text-indigo-600 hover:text-indigo-900 font-bold text-sm bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg transition-colors">
                    View Receipt (عرض الإيصال)
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// 3. View unsettled orders breakdown list
async function viewBreakdown(restaurantId, restaurantName, commissionRate) {
    try {
        const response = await fetch(`/admin/api/restaurant-wallets/${restaurantId}/orders`);
        const result = await response.json();
        if (result.status) {
            activeBreakdownOrders = result.data;
            document.getElementById('breakdownTitle').innerHTML = `${restaurantName} — Unsettled Orders Breakdown`;
            
            const tbody = document.getElementById('breakdownTableBody');
            tbody.innerHTML = "";
            
            activeBreakdownOrders.forEach(order => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 py-3 font-semibold text-gray-900">${order.order_number}</td>
                    <td class="px-4 py-3 text-sm">${formatDate(order.created_at)}</td>
                    <td class="px-4 py-3 text-sm capitalize font-medium text-gray-600">${order.payment_method}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">${formatCurrency(order.total)}</td>
                    <td class="px-4 py-3 text-right text-red-600 bg-red-50/10">-${formatCurrency(order.system_cut)}</td>
                    <td class="px-4 py-3 text-right text-green-600 bg-green-50/10 font-bold">${formatCurrency(order.net_payable)}</td>
                `;
                tbody.appendChild(tr);
            });
            
            // Set CSV exporter
            document.getElementById('btnExportBreakdownCSV').onclick = () => exportToCSV(
                `${restaurantName}_unsettled_orders.csv`,
                ['Order ID', 'Date', 'Payment Method', 'Meals Total', 'System Cut', 'Vendor Net'],
                activeBreakdownOrders.map(o => [o.order_number, formatDate(o.created_at), o.payment_method, o.total, o.system_cut, o.net_payable])
            );

            document.getElementById('breakdownModal').classList.remove('hidden-el');
        }
    } catch (error) {
        console.error('Error fetching breakdown:', error);
    }
}

// 4. View historical settlement details (Receipt)
async function viewReceipt(settlementId, settlementNumber) {
    try {
        const response = await fetch(`/admin/api/restaurant-settlements/${settlementId}/details`);
        const result = await response.json();
        if (result.status) {
            activeReceiptOrders = result.data;
            document.getElementById('receiptTitle').innerHTML = `Receipt Details — ${settlementNumber}`;
            
            const tbody = document.getElementById('receiptTableBody');
            tbody.innerHTML = "";
            
            activeReceiptOrders.forEach(order => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 py-3 font-semibold text-gray-900">${order.order_number}</td>
                    <td class="px-4 py-3 text-sm">${formatDate(order.created_at)}</td>
                    <td class="px-4 py-3 text-sm capitalize font-medium text-gray-600">${order.payment_method}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900">${formatCurrency(order.total)}</td>
                    <td class="px-4 py-3 text-right text-red-600 bg-red-50/10">-${formatCurrency(order.system_cut)}</td>
                    <td class="px-4 py-3 text-right text-green-600 bg-green-50/10 font-bold">${formatCurrency(order.net_payable)}</td>
                `;
                tbody.appendChild(tr);
            });
            
            // Set CSV exporter
            document.getElementById('btnExportReceiptCSV').onclick = () => exportToCSV(
                `receipt_${settlementNumber}.csv`,
                ['Order ID', 'Date', 'Payment Method', 'Meals Total', 'System Cut', 'Vendor Net'],
                activeReceiptOrders.map(o => [o.order_number, formatDate(o.created_at), o.payment_method, o.total, o.system_cut, o.net_payable])
            );

            document.getElementById('receiptModal').classList.remove('hidden-el');
        }
    } catch (error) {
        console.error('Error fetching receipt details:', error);
    }
}

// 5. Execute account settlement payout (Transaction)
function settleAccount(restaurantId, restaurantName, grossRevenue, systemCut, netPayable) {
    const isArabic = document.documentElement.getAttribute('dir') === 'rtl';

    const title = isArabic ? 'هل أنت متأكد؟' : 'Are you sure?';
    const text = isArabic
        ? `سيتم تسوية حساب مطعم (${restaurantName}) بقيمة صافية مستحقة قدرها ${formatCurrency(netPayable)}. لا يمكن التراجع عن هذا الإجراء!`
        : `You are about to settle the account for ${restaurantName} with a Net Payable of ${formatCurrency(netPayable)}. This will archive all active orders.`;
    const confirmBtn = isArabic ? 'نعم، قم بالتسوية!' : 'Yes, settle it!';
    const cancelBtn = isArabic ? 'إلغاء' : 'Cancel';

    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: confirmBtn,
        cancelButtonText: cancelBtn
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: isArabic ? 'جاري المعالجة...' : 'Processing settlement...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(`/admin/api/restaurant-wallets/${restaurantId}/settle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                });
                
                const data = await response.json();
                
                if (data.status) {
                    Swal.fire({
                        title: isArabic ? 'تمت التسوية بنجاح!' : 'Settled Successfully!',
                        text: isArabic 
                            ? `تم إنشاء إيصال تسوية رقم ${data.settlement.settlement_number} بنجاح.`
                            : `Settlement receipt ${data.settlement.settlement_number} has been generated.`,
                        icon: 'success'
                    });
                    fetchWallets(); // Refresh wallet table
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Settlement failed.',
                        icon: 'error'
                    });
                }
            } catch (error) {
                console.error('Settlement error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An unexpected connection error occurred.',
                    icon: 'error'
                });
            }
        }
    });
}

// High fidelity CSV Exporter helper
function exportToCSV(filename, headers, rows) {
    const csvContent = "data:text/csv;charset=utf-8," 
        + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Initial page loading
document.addEventListener('DOMContentLoaded', () => {
    fetchWallets();
});
