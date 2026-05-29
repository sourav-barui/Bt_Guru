@extends('layouts.tenant')

@section('title', 'Book Orders')
@section('page-title', 'Book Orders')

@section('page-content')

{{-- Pending Purchase Requests --}}
@if($pendingRequests->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            Pending Book Purchase Requests
            <span class="text-xs font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">{{ $pendingRequests->count() }}</span>
        </h3>
        <a href="{{ route('tenant.payments.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Go to Payments →</a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead class="bg-gray-50">
                <tr>
                    <th>Book</th>
                    <th>Student</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($pendingRequests as $req)
                <tr>
                    <td>
                        <p class="font-medium text-gray-900">{{ $req->book?->title ?? '—' }}</p>
                        @if($req->book?->author)
                            <p class="text-sm text-gray-500">by {{ $req->book->author }}</p>
                        @endif
                    </td>
                    <td>
                        <p class="font-medium text-gray-900">{{ $req->student?->name ?? '—' }}</p>
                        <p class="text-sm text-gray-500">{{ $req->student?->email ?? '' }}</p>
                    </td>
                    <td>
                        @php $meta = $req->metadata ?? []; @endphp
                        <span class="badge {{ ($meta['order_type'] ?? '') === 'pdf' ? 'badge-info' : (($meta['order_type'] ?? '') === 'physical' ? 'badge-warning' : 'badge-success') }}">
                            {{ ucfirst($meta['order_type'] ?? '—') }}
                        </span>
                    </td>
                    <td class="font-medium text-gray-900">₹{{ number_format($req->amount, 2) }}</td>
                    <td>
                        <span class="text-sm text-gray-500">{{ $req->reference_number ?? '—' }}</span>
                    </td>
                    <td class="text-sm text-gray-600">{{ $req->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('tenant.payments.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Approve
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-900">All Book Orders</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead class="bg-gray-50">
                <tr>
                    <th>Order ID</th>
                    <th>Book</th>
                    <th>Student</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Delivery</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>
                            <p class="font-medium text-gray-900">{{ $order->book->title }}</p>
                            @if($order->book->author)
                                <p class="text-sm text-gray-500">by {{ $order->book->author }}</p>
                            @endif
                        </td>
                        <td>
                            <p class="font-medium text-gray-900">{{ $order->student->name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->student->email }}</p>
                        </td>
                        <td>
                            <span class="badge {{ $order->order_type === 'pdf' ? 'badge-info' : ($order->order_type === 'physical' ? 'badge-warning' : 'badge-success') }}">
                                {{ $order->order_type_label }}
                            </span>
                        </td>
                        <td>
                            <p class="font-medium text-gray-900">₹{{ number_format($order->total_amount, 2) }}</p>
                            @if($order->order_type === 'both')
                                <p class="text-xs text-gray-500">PDF: ₹{{ number_format($order->pdf_price, 2) }}</p>
                                <p class="text-xs text-gray-500">Physical: ₹{{ number_format($order->physical_price, 2) }}</p>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $order->payment_status_badge_class }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                        </td>
                        <td>
                            @if($order->isPhysicalOrder())
                                <span class="badge badge-{{ $order->delivery_status_badge_class }}">
                                    {{ ucfirst($order->delivery_status ?? 'pending') }}
                                </span>
                                @if($order->tracking_number)
                                    <p class="text-xs text-gray-500 mt-1">Track: {{ $order->tracking_number }}</p>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="showOrderDetails({{ $order->id }})" class="text-blue-600 hover:text-blue-700" title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                @if($order->isPhysicalOrder() && $order->isCompleted())
                                    <button type="button" onclick="updateDeliveryStatus({{ $order->id }}, '{{ $order->delivery_status }}', '{{ $order->tracking_number }}')" class="text-green-600 hover:text-green-700" title="Update Delivery Status">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Order Details Modal -->
                    <div id="order-details-{{ $order->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="hideOrderDetails({{ $order->id }})"></div>
                            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full">
                                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900">Order #{{ $order->id }} Details</h3>
                                    <button type="button" onclick="hideOrderDetails({{ $order->id }})" class="text-gray-400 hover:text-gray-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="px-6 py-4 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Student</p>
                                            <p class="font-medium">{{ $order->student->name }}</p>
                                            <p class="text-sm">{{ $order->student->email }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Order Date</p>
                                            <p class="font-medium">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>

                                    @if($order->isPhysicalOrder())
                                        <div class="border-t border-gray-200 pt-4">
                                            <p class="text-sm text-gray-500 mb-2">Delivery Address</p>
                                            <p class="text-sm">{{ $order->delivery_address ?? 'Not provided' }}</p>
                                            @if($order->delivery_phone)
                                                <p class="text-sm mt-1">Phone: {{ $order->delivery_phone }}</p>
                                            @endif
                                        </div>
                                    @endif

                                    @if($order->transaction_id)
                                        <div class="border-t border-gray-200 pt-4">
                                            <p class="text-sm text-gray-500">Transaction ID</p>
                                            <p class="text-sm font-mono">{{ $order->transaction_id }}</p>
                                        </div>
                                    @endif

                                    @if($order->notes)
                                        <div class="border-t border-gray-200 pt-4">
                                            <p class="text-sm text-gray-500">Notes</p>
                                            <p class="text-sm">{{ $order->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Status Update Modal -->
                    @if($order->isPhysicalOrder() && $order->isCompleted())
                        <div id="delivery-modal-{{ $order->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="hideDeliveryModal({{ $order->id }})"></div>
                                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full">
                                    <form method="POST" action="{{ route('tenant.books.orders.status', $order) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="px-6 py-4 border-b border-gray-200">
                                            <h3 class="text-lg font-medium text-gray-900">Update Delivery Status</h3>
                                        </div>
                                        <div class="px-6 py-4 space-y-4">
                                            <div>
                                                <label for="delivery_status_{{ $order->id }}" class="form-label">Status</label>
                                                <select id="delivery_status_{{ $order->id }}" name="delivery_status" class="form-input" required>
                                                    <option value="pending" {{ $order->delivery_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="processing" {{ $order->delivery_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                    <option value="shipped" {{ $order->delivery_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                    <option value="delivered" {{ $order->delivery_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                    <option value="cancelled" {{ $order->delivery_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="tracking_number_{{ $order->id }}" class="form-label">Tracking Number</label>
                                                <input type="text" id="tracking_number_{{ $order->id }}" name="tracking_number" value="{{ $order->tracking_number }}"
                                                       class="form-input" placeholder="Enter tracking number">
                                            </div>
                                            <div>
                                                <label for="notes_{{ $order->id }}" class="form-label">Notes</label>
                                                <textarea id="notes_{{ $order->id }}" name="notes" rows="3" class="form-input" placeholder="Add any notes...">{{ $order->notes }}</textarea>
                                            </div>
                                        </div>
                                        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                                            <button type="button" onclick="hideDeliveryModal({{ $order->id }})" class="btn-secondary">Cancel</button>
                                            <button type="submit" class="btn-primary">Update Status</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            No orders found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<script>
function showOrderDetails(orderId) {
    document.getElementById('order-details-' + orderId).classList.remove('hidden');
}

function hideOrderDetails(orderId) {
    document.getElementById('order-details-' + orderId).classList.add('hidden');
}

function updateDeliveryStatus(orderId, currentStatus, trackingNumber) {
    document.getElementById('delivery-modal-' + orderId).classList.remove('hidden');
}

function hideDeliveryModal(orderId) {
    document.getElementById('delivery-modal-' + orderId).classList.add('hidden');
}
</script>
@endsection
