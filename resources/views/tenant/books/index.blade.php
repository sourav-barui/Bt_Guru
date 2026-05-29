@extends('layouts.tenant')

@section('title', 'Books')
@section('page-title', 'Manage Books')

@section('page-content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">All Books</h3>
        <a href="{{ route('tenant.books.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Book
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead class="bg-gray-50">
                <tr>
                    <th>Book</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Orders</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($books as $book)
                    <tr>
                        <td>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $book->title }}</p>
                                    @if($book->author)
                                        <p class="text-sm text-gray-500">by {{ $book->author }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $book->type === 'pdf' ? 'badge-info' : ($book->type === 'physical' ? 'badge-warning' : 'badge-success') }}">
                                {{ $book->type_label }}
                            </span>
                        </td>
                        <td>
                            @if($book->type === 'pdf')
                                <p class="font-medium text-gray-900">₹{{ number_format($book->pdf_price, 2) }}</p>
                            @elseif($book->type === 'physical')
                                <p class="font-medium text-gray-900">₹{{ number_format($book->physical_price, 2) }}</p>
                            @else
                                <p class="text-sm text-gray-900">PDF: ₹{{ number_format($book->pdf_price, 2) }}</p>
                                <p class="text-sm text-gray-900">Physical: ₹{{ number_format($book->physical_price, 2) }}</p>
                            @endif
                        </td>
                        <td>
                            @if($book->isPhysical())
                                <span class="{{ $book->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $book->stock_quantity }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-900 font-medium">{{ $book->orders_count }}</span>
                                @if($book->pending_requests_count > 0)
                                    <a href="{{ route('tenant.books.orders') }}" class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700" title="Pending approvals">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $book->pending_requests_count }} pending
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $book->status_badge_class }}">
                                {{ ucfirst($book->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tenant.books.show', $book) }}" class="text-blue-600 hover:text-blue-700" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('tenant.books.edit', $book) }}" class="text-gray-600 hover:text-gray-700" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @if($book->pdf_file)
                                    <a href="{{ route('tenant.books.download', $book) }}" class="text-green-600 hover:text-green-700" title="Download PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('tenant.books.destroy', $book) }}" class="inline" onsubmit="return confirm('Delete this book?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No books found. <a href="{{ route('tenant.books.create') }}" class="text-blue-600">Add one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($books->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $books->links() }}
        </div>
    @endif
</div>
@endsection
