@extends('layouts.student')

@section('title', 'Book Store')
@section('page-title', 'Book Store')

@section('page-content')
<div class="space-y-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-100 to-pink-100 rounded-2xl p-4">
        <h1 class="text-2xl font-bold mb-2 text-gray-900">Book Store</h1>
        <p class="text-gray-600">Browse and purchase books for your studies</p>
    </div>

    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Cover Image -->
                <div class="aspect-[3/4] bg-gray-100 relative overflow-hidden">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $book->type === 'pdf' ? 'bg-blue-100 text-blue-700' : ($book->type === 'physical' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                            {{ $book->type_label }}
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 text-lg mb-1 line-clamp-1">{{ $book->title }}</h3>
                    @if($book->author)
                        <p class="text-sm text-gray-500 mb-2">by {{ $book->author }}</p>
                    @endif

                    @if($book->description)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($book->description, 100) }}</p>
                    @endif

                    <!-- Prices -->
                    <div class="space-y-1 mb-4">
                        @if($book->isPdf())
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">PDF</span>
                                <span class="font-semibold text-gray-900">₹{{ number_format($book->pdf_price, 2) }}</span>
                            </div>
                        @endif
                        @if($book->isPhysical())
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Physical</span>
                                <span class="font-semibold text-gray-900">₹{{ number_format($book->physical_price, 2) }}</span>
                            </div>
                            @if($book->stock_quantity <= 0)
                                <p class="text-xs text-red-500">Out of stock</p>
                            @endif
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    @if(in_array($book->id, $purchasedBookIds))
                        <a href="{{ route('student.books.show', $book) }}" class="btn-success w-full text-center block">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download
                        </a>
                    @else
                        <div class="space-y-2">
                            @if($book->isPdf() && !$book->isPhysical())
                                <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'pdf']) }}" class="btn-primary w-full text-center block">
                                    Buy PDF - ₹{{ number_format($book->pdf_price, 2) }}
                                </a>
                            @elseif($book->isPhysical() && !$book->isPdf())
                                <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'physical']) }}" class="btn-primary w-full text-center block {{ $book->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $book->stock_quantity <= 0 ? 'onclick="return false;"' : '' }}>
                                    Buy Physical - ₹{{ number_format($book->physical_price, 2) }}
                                </a>
                            @else
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'pdf']) }}" class="btn-primary text-center text-sm py-2">
                                        PDF ₹{{ number_format($book->pdf_price, 2) }}
                                    </a>
                                    <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'physical']) }}" class="btn-warning text-center text-sm py-2 {{ $book->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $book->stock_quantity <= 0 ? 'onclick="return false;"' : '' }}>
                                        Physical ₹{{ number_format($book->physical_price, 2) }}
                                    </a>
                                </div>
                                <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'both']) }}" class="btn-success w-full text-center block {{ $book->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $book->stock_quantity <= 0 ? 'onclick="return false;"' : '' }}>
                                    Buy Both - ₹{{ number_format($book->pdf_price + $book->physical_price, 2) }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($books->hasPages())
            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-gray-700 font-bold text-lg mb-2">No Books Available</p>
            <p class="text-gray-500">Please check back later for new book arrivals.</p>
        </div>
    @endif

    <!-- My Orders Link -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <div>
            <p class="text-sm font-bold text-blue-800 mb-1">My Orders</p>
            <p class="text-sm text-blue-600">View your <a href="{{ route('student.books.my_orders') }}" class="underline font-bold">purchased books</a></p>
        </div>
    </div>
</div>
@endsection
