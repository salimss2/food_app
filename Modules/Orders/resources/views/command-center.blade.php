@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">غرفة العمليات - Command Center</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- New Orders Column -->
        <div class="bg-gray-100 rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-3 border-b pb-2">جديد (<span id="new-orders-count">{{ $kpi['new_count'] ?? 0 }}</span>)</h2>
            <div id="kanban-new-orders" class="space-y-3">
                @if(isset($orders_new))
                    @foreach($orders_new as $order)
                        <div class="bg-white p-3 rounded shadow-sm border-l-4 border-blue-500" id="order-card-{{ $order->id }}">
                            <div class="flex justify-between">
                                <span class="font-bold">#{{ $order->id }}</span>
                                <span class="text-sm text-gray-500">{{ $order->created_at->format('H:i') }}</span>
                            </div>
                            <div class="text-sm mt-2">{{ $order->user->name ?? 'عميل' }}</div>
                            <div class="font-bold mt-1">{{ $order->total }} ريال</div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Accepted Orders Column -->
        <div class="bg-indigo-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-3 border-b pb-2">تم القبول</h2>
            <div id="kanban-accepted-orders" class="space-y-3">
                <!-- Data here -->
            </div>
        </div>

        <!-- Preparing Orders Column -->
        <div class="bg-orange-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-3 border-b pb-2">قيد التحضير</h2>
            <div id="kanban-preparing-orders" class="space-y-3">
                <!-- Data here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const restaurantId = "{{ auth()->user()->restaurant_id ?? '' }}"; // Assuming user context has restaurant_id
    
    if(restaurantId && typeof Echo !== 'undefined') {
        const channelName = 'private-restaurant.' + restaurantId;

        Echo.private(channelName)
            .listen('.OrderCreated', (e) => {
                console.log('New Order Received via Echo!', e);
                
                // Update KPI Count
                const countEl = document.getElementById('new-orders-count');
                if(countEl) {
                    countEl.innerText = parseInt(countEl.innerText) + 1;
                }

                // Render New Order Card dynamically
                const orderData = e;
                const newCard = `
                    <div class="bg-white p-3 rounded shadow-sm border-l-4 border-blue-500 animate-pulse" id="order-card-${orderData.id}">
                        <div class="flex justify-between">
                            <span class="font-bold">#${orderData.id}</span>
                            <span class="text-sm text-green-500 font-bold">الآن</span>
                        </div>
                        <div class="text-sm mt-2">${orderData.user ? orderData.user.name : 'عميل'}</div>
                        <div class="font-bold mt-1">${orderData.total} ريال</div>
                    </div>
                `;

                const kanbanContainer = document.getElementById('kanban-new-orders');
                if(kanbanContainer) {
                    kanbanContainer.insertAdjacentHTML('afterbegin', newCard);
                    
                    // Stop pulsing after 3 seconds
                    setTimeout(() => {
                        const cardElement = document.getElementById('order-card-' + orderData.id);
                        if(cardElement) {
                            cardElement.classList.remove('animate-pulse');
                        }
                    }, 3000);
                }
            });
    } else {
        console.warn("Laravel Echo is not defined or Restaurant ID is missing. Falling back to polling...");
        
        // Simple JS polling fallback (every 10 seconds) if Echo isn't configured yet
        setInterval(() => {
            fetch('/api/v1/orders/check-new') // Custom polling endpoint logic
                .then(res => res.json())
                .then(data => {
                    // Logic to process fetched new orders
                }).catch(err => console.error("Polling error", err));
        }, 10000);
    }
});
</script>
@endsection
