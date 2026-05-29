@extends('layouts.student')

@section('title', 'My Book Orders')
@section('page-title', 'My Book Orders')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-6 text-white">
        <h1 class="text-2xl font-bold mb-2">My Book Orders</h1>
        <p class="text-white/80">View and download your purchased books</p>
    </div>

    @if($orders->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($order->book->cover_image)
                                            <img src="{{ asset('storage/' . $order->book->cover_image) }}" alt="" class="w-12 h-16 object-cover rounded mr-3">
                                        @else
                                            <div class="w-12 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded flex items-center justify-center mr-3">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $order->book->title }}</p>
                                            @if($order->book->author)
                                                <p class="text-sm text-gray-500">by {{ $order->book->author }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->order_type === 'pdf' ? 'bg-blue-100 text-blue-800' : ($order->order_type === 'physical' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800') }}">
                                        {{ $order->order_type_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-medium text-gray-900">₹{{ number_format($order->total_amount, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->payment_status === 'completed' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                    @if($order->isPhysicalOrder())
                                        <p class="text-xs text-gray-500 mt-1">Delivery: {{ ucfirst($order->delivery_status ?? 'pending') }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if($order->canDownload())
                                            <a href="{{ route('student.books.download', $order) }}" class="text-green-600 hover:text-green-700" title="Download PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('student.books.show', $order->book) }}" class="text-blue-600 hover:text-blue-700" title="View Book">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($orders->hasPages())
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <p class="text-gray-700 font-bold text-lg mb-2">No Orders Yet</p>
            <p class="text-gray-500 mb-4">You haven't purchased any books yet.</p>
            <a href="{{ route('student.books.index') }}" class="btn-primary">
                Browse Book Store
            </a>
        </div>
    @endif
</div>
@endsection
