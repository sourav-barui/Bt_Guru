@extends('layouts.student')

@section('title', $book->title)
@section('page-title', 'Book Details')

@section('page-content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Cover Image -->
                <div class="w-full md:w-48 flex-shrink-0">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full rounded-lg shadow-md">
                    @else
                        <div class="w-full aspect-[2/3] bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Book Details -->
                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $book->title }}</h1>
                            @if($book->author)
                                <p class="text-lg text-gray-600 mt-1">by {{ $book->author }}</p>
                            @endif
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $book->type === 'pdf' ? 'bg-blue-100 text-blue-700' : ($book->type === 'physical' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                            {{ $book->type_label }}
                        </span>
                    </div>

                    @if($book->publisher || $book->isbn)
                        <div class="mt-4 space-y-1">
                            @if($book->publisher)
                                <p class="text-sm text-gray-600"><span class="font-medium">Publisher:</span> {{ $book->publisher }}</p>
                            @endif
                            @if($book->isbn)
                                <p class="text-sm text-gray-600"><span class="font-medium">ISBN:</span> {{ $book->isbn }}</p>
                            @endif
                        </div>
                    @endif

                    @if($book->description)
                        <div class="mt-6">
                            <h3 class="font-medium text-gray-900 mb-2">Description</h3>
                            <p class="text-gray-600">{{ $book->description }}</p>
                        </div>
                    @endif

                    <!-- Prices -->
                    <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($book->isPdf())
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500">PDF Price</p>
                                <p class="text-2xl font-bold text-gray-900">₹{{ number_format($book->pdf_price, 2) }}</p>
                            </div>
                        @endif
                        @if($book->isPhysical())
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500">Physical Price</p>
                                <p class="text-2xl font-bold text-gray-900">₹{{ number_format($book->physical_price, 2) }}</p>
                                <p class="text-xs {{ $book->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                                    {{ $book->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex gap-3">
                        @if($canDownload)
                            <a href="{{ route('student.books.download', $order) }}" class="btn-success">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download PDF
                            </a>
                        @elseif($order && $order->isPending())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-center">
                                <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-yellow-800">Payment pending verification</span>
                            </div>
                        @else
                            @if($book->isPdf() && !$book->isPhysical())
                                <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'pdf']) }}" class="btn-primary">
                                    Buy PDF - ₹{{ number_format($book->pdf_price, 2) }}
                                </a>
                            @elseif($book->isPhysical() && !$book->isPdf())
                                <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'physical']) }}" class="btn-primary {{ $book->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $book->stock_quantity <= 0 ? 'onclick="return false;"' : '' }}>
                                    Buy Physical - ₹{{ number_format($book->physical_price, 2) }}
                                </a>
                            @else
                                <div class="flex gap-2">
                                    <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'pdf']) }}" class="btn-primary">
                                        Buy PDF - ₹{{ number_format($book->pdf_price, 2) }}
                                    </a>
                                    <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'physical']) }}" class="btn-warning {{ $book->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $book->stock_quantity <= 0 ? 'onclick="return false;"' : '' }}>
                                        Buy Physical - ₹{{ number_format($book->physical_price, 2) }}
                                    </a>
                                </div>
                            @endif
                        @endif

                        <a href="{{ route('student.books.index') }}" class="btn-secondary">
                            Back to Store
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($order)
        <!-- Order Details -->
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Your Order</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Order Type</p>
                    <p class="font-medium">{{ $order->order_type_label }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Payment Status</p>
                    <span class="badge badge-{{ $order->payment_status_badge_class }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Amount Paid</p>
                    <p class="font-medium">₹{{ number_format($order->total_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Order Date</p>
                    <p class="font-medium">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            @if($order->isPhysicalOrder())
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-2">Delivery Status</h4>
                    <div class="flex items-center">
                        <span class="badge badge-{{ $order->delivery_status_badge_class }}">
                            {{ ucfirst($order->delivery_status ?? 'pending') }}
                        </span>
                        @if($order->tracking_number)
                            <span class="ml-4 text-sm text-gray-600">Tracking: {{ $order->tracking_number }}</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
