@extends('layouts.admin')

@section('title', 'Coupons')
@section('page-title', 'Coupons')

@section('page-content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">Manage Coupons</h3>
        <a href="{{ route('admin.coupons.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Coupon
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead class="bg-gray-50">
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Usage</th>
                    <th>Valid Period</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($coupons as $coupon)
                    <tr>
                        <td>
                            <div>
                                <p class="font-mono font-semibold text-gray-900">{{ $coupon->code_upper }}</p>
                                <p class="text-sm text-gray-500">{{ $coupon->description }}</p>
                            </div>
                        </td>
                        <td>
                            @if($coupon->discount_type === 'percentage')
                                <span class="font-semibold text-gray-900">{{ $coupon->discount_value }}%</span>
                            @else
                                <span class="font-semibold text-gray-900">{{ number_format($coupon->discount_value, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm text-gray-600">{{ $coupon->used_count }}</span>
                            @if($coupon->max_uses)
                                <span class="text-sm text-gray-400"> / {{ $coupon->max_uses }}</span>
                            @else
                                <span class="text-sm text-gray-400"> / Unlimited</span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->valid_from && $coupon->valid_until)
                                <span class="text-sm text-gray-600">{{ $coupon->valid_from->format('M d') }} - {{ $coupon->valid_until->format('M d, Y') }}</span>
                            @elseif($coupon->valid_from)
                                <span class="text-sm text-gray-600">From {{ $coupon->valid_from->format('M d, Y') }}</span>
                            @elseif($coupon->valid_until)
                                <span class="text-sm text-gray-600">Until {{ $coupon->valid_until->format('M d, Y') }}</span>
                            @else
                                <span class="text-sm text-gray-400">No limit</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.coupons.show', $coupon) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No coupons found. Create your first coupon to offer discounts.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
