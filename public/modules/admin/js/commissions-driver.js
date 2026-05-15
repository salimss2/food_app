/* ================================================
 * commissions-driver.js — Page-specific logic
 * ================================================ */

// Global Data
let commData = [];

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    fetchDriverWallets();
    const searchInput = document.getElementById('driverSearch');
    if (searchInput) {
        searchInput.addEventListener('input', renderTable);
    }
});

async function fetchDriverWallets() {
    const tbody = document.getElementById('driverTableBody');
    const emptyState = document.getElementById('emptyState');

    // Show Loading state if needed
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
        emptyState.classList.add('hidden-el');
    }
}

function fixCurrency(num) {
    const val = parseFloat(num);
    const formatted = Math.abs(val).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    if (val < 0) {
        return `(${formatted} SAR)`;
    }
    return `${formatted} SAR`;
}

function viewBreakdown(index) {
    // Note: Detail breakdown still uses localized logic or can be fetched per driver
    const driver = commData[index];
    document.getElementById('breakdownTitle').innerText = `${driver.name} - Breakdown`;

    const tbody = document.querySelector('#breakdownModal tbody');
    tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-400 italic">Detailed breakdown logic to be linked...</td></tr>';

    document.getElementById('breakdownModal').classList.remove('hidden-el');
}

function settleAccount(index) {
    const driver = commData[index];
    alert(`Initiating settlement for ${driver.name}. Balance: ${fixCurrency(driver.netBalance)}`);
}

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
        tr.className = "hover:bg-gray-50 transition-colors";
        tr.innerHTML = `
            <td class="px-6 py-4 font-bold text-gray-900 flex items-center">
                <img class="w-8 h-8 rounded-full border border-gray-200 mr-3" src="${d.avatar}">
                ${d.name}
            </td>
            <td class="px-6 py-4 text-center font-medium text-gray-600">${d.deliveries}</td>
            <td class="px-6 py-4 text-right font-medium text-gray-900">${d.driverEarnings.toFixed(2)} SAR</td>
            <td class="px-6 py-4 text-right font-medium text-orange-600">${d.cashInHand.toFixed(2)} SAR</td>
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
