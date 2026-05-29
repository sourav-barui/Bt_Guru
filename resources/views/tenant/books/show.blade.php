@extends('layouts.tenant')

@section('title', $book->title)
@section('page-title', 'Book Details')

@section('page-content')
<div class="space-y-6">

    {{-- Header Bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('tenant.books.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Books
        </a>
        <div class="flex gap-2">
            <a href="{{ route('tenant.books.orders') }}" class="btn-secondary">
                View Orders
            </a>
            <a href="{{ route('tenant.books.edit', $book) }}" class="btn-primary">
                Edit Book
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Book Info Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-8 flex gap-5 items-center">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="" class="w-20 h-28 rounded-xl object-cover shadow">
                    @else
                        <div class="w-20 h-28 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl font-bold text-white truncate">{{ $book->title }}</h1>
                        @if($book->author)
                            <p class="text-white/75 mt-1 text-sm">by {{ $book->author }}</p>
                        @endif
                        <div class="mt-2 flex items-center gap-2">
                            <span class="px-2 py-1 rounded-full text-xs bg-white/20 text-white font-semibold">
                                {{ $book->type_label }}
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs {{ $book->status === 'active' ? 'bg-green-400/30 text-green-100' : 'bg-gray-400/30 text-gray-100' }} font-semibold">
                                {{ ucfirst($book->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @if($book->isPdf())
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">PDF Price</p>
                            <p class="mt-1 font-bold text-gray-900 text-lg">₹{{ number_format($book->pdf_price, 2) }}</p>
                        </div>
                    @endif
                    @if($book->isPhysical())
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Physical Price</p>
                            <p class="mt-1 font-bold text-gray-900 text-lg">₹{{ number_format($book->physical_price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Stock</p>
                            <p class="mt-1 font-bold {{ $book->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }} text-lg">
                                {{ $book->stock_quantity }}
                            </p>
                        </div>
                    @endif
                    @if($book->publisher)
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Publisher</p>
                            <p class="mt-1 font-medium text-gray-900">{{ $book->publisher }}</p>
                        </div>
                    @endif
                    @if($book->isbn)
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">ISBN</p>
                            <p class="mt-1 font-medium text-gray-900">{{ $book->isbn }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Created</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $book->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                @if($book->description)
                    <div class="px-6 pb-6">
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide mb-2">Description</p>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $book->description }}</p>
                    </div>
                @endif

                @if($book->pdf_file)
                    <div class="px-6 pb-6">
                        <a href="{{ route('tenant.books.download', $book) }}" class="btn-success inline-flex">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download PDF
                        </a>
                    </div>
                @endif
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Overview</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Orders</span>
                        <span class="font-bold text-gray-900">{{ $stats['total_orders'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">PDF Orders</span>
                        <span class="font-bold text-blue-600">{{ $stats['pdf_orders'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Physical Orders</span>
                        <span class="font-bold text-orange-600">{{ $stats['physical_orders'] }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Revenue</span>
                            <span class="font-bold text-green-600">₹{{ number_format($stats['total_revenue'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Quick Links</h3>
                <div class="space-y-2">
                    <a href="{{ route('tenant.books.orders') }}"
                       class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-3 py-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        View All Orders
                    </a>
                    <a href="{{ route('tenant.books.edit', $book) }}"
                       class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-700 hover:bg-gray-50 rounded-lg px-3 py-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Book
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Recent Orders</h3>
            <a href="{{ route('tenant.books.orders') }}" class="text-blue-600 hover:text-blue-700 text-sm">View All Orders</a>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th>Student</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Delivery</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($book->orders()->with('student')->latest()->take(10)->get() as $order)
                        <tr>
                            <td>
                                <p class="font-medium text-gray-900">{{ $order->student->name }}</p>
                                <p class="text-sm text-gray-500">{{ $order->student->email }}</p>
                            </td>
                            <td>
                                <span class="badge {{ $order->order_type === 'pdf' ? 'badge-info' : ($order->order_type === 'physical' ? 'badge-warning' : 'badge-success') }}">
                                    {{ $order->order_type_label }}
                                </span>
                            </td>
                            <td>₹{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $order->payment_status_badge_class }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                @if($order->isPhysicalOrder())
                                    <span class="badge badge-{{ $order->delivery_status_badge_class }}">
                                        {{ ucfirst($order->delivery_status ?? 'pending') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
