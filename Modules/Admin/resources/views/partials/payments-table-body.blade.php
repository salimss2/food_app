@foreach ($orders as $order)
@php
    $pStatus = $order->payment ? $order->payment->payment_status : $order->payment_status;
    $pMethod = $order->payment ? $order->payment->payment_method : $order->payment_method;

    // Order status badge map
    $orderStatusMap = [
        'pending_admin_approval'   => ['label' => 'Pending Approval',   'class' => 'bg-yellow-100 text-yellow-800'],
        'pending_driver_acceptance'=> ['label' => 'Pending Driver',      'class' => 'bg-orange-100 text-orange-800'],
        'driver_assigned'          => ['label' => 'Driver Assigned',     'class' => 'bg-blue-100 text-blue-800'],
        'ready_for_pickup'         => ['label' => 'Ready for Pickup',    'class' => 'bg-indigo-100 text-indigo-800'],
        'on_the_way'               => ['label' => 'On the Way',          'class' => 'bg-purple-100 text-purple-800'],
        'delivered'                => ['label' => 'Delivered',           'class' => 'bg-green-100 text-green-800'],
        'canceled'                 => ['label' => 'Canceled',            'class' => 'bg-gray-100 text-gray-800'],
    ];
    $osBadge = $orderStatusMap[$order->status] ?? ['label' => ucfirst(str_replace('_',' ',$order->status)), 'class' => 'bg-gray-100 text-gray-700'];

    // Payment status badge map
    $paymentStatusMap = [
        'pending_verification' => ['label' => 'Pending Verification', 'class' => 'bg-yellow-100 text-yellow-800'],
        'pending_collection'   => ['label' => 'Pending Collection',   'class' => 'bg-indigo-100 text-indigo-800'],
        'completed'            => ['label' => 'Completed',            'class' => 'bg-green-100 text-green-800'],
        'rejected'             => ['label' => 'Rejected',             'class' => 'bg-red-100 text-red-800'],
        'canceled'             => ['label' => 'Canceled',             'class' => 'bg-gray-100 text-gray-800'],
        'pending_refund'       => ['label' => 'Pending Refund',       'class' => 'bg-red-100 text-red-800 border border-red-200'],
        'refunded'             => ['label' => 'Refunded',             'class' => 'bg-green-100 text-green-800 border border-green-200'],
    ];
    $psBadge = $paymentStatusMap[$pStatus] ?? ['label' => ucfirst(str_replace('_',' ',$pStatus)), 'class' => 'bg-gray-100 text-gray-700'];
@endphp
<tr id="order-row-{{ $order->id }}" class="hover:bg-gray-50 bg-white transition-colors {{ $pStatus === 'pending_verification' ? 'border-l-4 border-yellow-400 bg-yellow-50 hover:bg-yellow-100' : '' }} {{ $pStatus === 'pending_refund' ? 'border-l-4 border-red-400 bg-red-50 hover:bg-red-100' : '' }}">
    {{-- Order ID / Date --}}
    <td class="px-6 py-4">
        <div class="text-sm font-bold text-gray-900">#{{ $order->id }}</div>
        <div class="text-xs text-gray-500">{{ $order->created_at->format('Y-m-d H:i') }}</div>
    </td>

    {{-- Customer --}}
    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $order->user->name ?? 'Unknown' }}</td>

    {{-- Payment Method --}}
    <td class="px-6 py-4">
        @if($pMethod === 'bank_transfer')
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Bank Transfer</span>
        @elseif($pMethod === 'cod')
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Cash on Delivery</span>
        @else
            <span class="text-xs text-gray-500">{{ $pMethod }}</span>
        @endif
    </td>

    {{-- Total Amount --}}
    <td class="px-6 py-4">
        <span class="text-sm font-bold text-green-600">${{ number_format($order->total, 2) }}</span>
    </td>

    {{-- Payment Proof --}}
    <td class="px-6 py-4 text-center">
        @if ($pMethod === 'bank_transfer' && $order->receipt_image)
            <img src="{{ asset($order->receipt_image) }}" alt="Payment Proof"
                 onclick="openImagePreview('{{ asset($order->receipt_image) }}', '{{ $order->id }}')"
                 class="h-10 w-10 object-cover rounded mx-auto cursor-pointer border border-gray-200 hover:opacity-80 transition-opacity ring-1 ring-gray-300">
        @elseif ($pMethod === 'bank_transfer')
            <span class="text-xs text-gray-400">No Image</span>
        @else
            <span class="text-xs text-gray-400">-</span>
        @endif
    </td>

    {{-- Order Status --}}
    <td class="px-6 py-4 text-sm">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $osBadge['class'] }}">
            {{ $osBadge['label'] }}
        </span>
    </td>

    {{-- Payment Status --}}
    <td class="px-6 py-4 text-sm">
        <span id="badge-{{ $order->id }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $psBadge['class'] }}">
            {{ $psBadge['label'] }}
        </span>
    </td>

    {{-- Actions --}}
    <td class="px-6 py-4 text-center whitespace-nowrap" id="actions-{{ $order->id }}">
        @if ($pStatus === 'pending_verification')
            <button onclick="requestAction('Approve', '{{ $order->id }}')" class="bg-green-100 hover:bg-green-200 text-green-800 font-bold py-1.5 px-3 rounded shadow-sm mr-2 text-xs transition-colors">Verify</button>
            <button onclick="requestAction('Reject', '{{ $order->id }}')"  class="bg-red-100 hover:bg-red-200 text-red-800 font-bold py-1.5 px-3 rounded shadow-sm text-xs transition-colors">Reject</button>
        @elseif (in_array($pStatus, ['pending_collection', 'completed']))
            <a href="{{ route('admin.orders.index') }}?customer_name={{ urlencode($order->user->name ?? '') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-1.5 px-3 rounded shadow-sm mr-2 text-xs transition-colors inline-block">View Order</a>
            <button onclick="requestCancel('{{ $order->id }}')" class="bg-red-100 hover:bg-red-200 text-red-800 font-bold py-1.5 px-3 rounded shadow-sm text-xs transition-colors">Cancel Order</button>
        @elseif ($pStatus === 'pending_refund')
            <button onclick="requestRefund('{{ $order->id }}')" class="bg-green-100 hover:bg-green-200 text-green-800 font-bold py-1.5 px-3 rounded shadow-sm mr-2 text-xs transition-colors">Mark Refunded</button>
            <a href="{{ route('admin.order-history.index') }}?customer_name={{ urlencode($order->user->name ?? '') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-1.5 px-3 rounded shadow-sm text-xs transition-colors inline-block">View Order</a>
        @elseif (in_array($pStatus, ['canceled', 'rejected', 'refunded']))
            <a href="{{ route('admin.order-history.index') }}?customer_name={{ urlencode($order->user->name ?? '') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-1.5 px-3 rounded shadow-sm text-xs transition-colors inline-block">View Order</a>
        @endif
    </td>
</tr>
@endforeach

@if ($orders->hasPages())
<tr class="pagination-row" style="display:none;" data-links='{!! $orders->links()->toHtml() !!}'></tr>
@endif
