@extends('layouts.student_mobile')

@section('title', 'Book Store')

@section('mobile-content')
<!-- Header -->
<div class="tb-header-gradient">
    <div class="flex items-center justify-between mb-2">
        <div>
            <p class="text-sm text-white/80">Browse & Purchase</p>
            <h1 class="text-2xl font-bold text-white">Book Store</h1>
        </div>
    </div>
</div>

<!-- My Orders Link -->
<a href="{{ route('student.books.my_orders') }}" class="tb-card-flat" style="display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;margin-top:12px;">
    <div style="width:40px;height:40px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
    </div>
    <div>
        <div style="font-size:14px;font-weight:600;color:#1f2937;">My Orders</div>
        <div style="font-size:12px;color:#6b7280;">View purchased books</div>
    </div>
    <svg width="20" height="20" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" style="margin-left:auto;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
</a>

<!-- Books Grid -->
@if($books->count() > 0)
    <div style="padding:12px;display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
        @foreach($books as $book)
        <div class="tb-card" style="margin:0;padding:0;overflow:hidden;">
            <!-- Cover -->
            <div style="aspect-ratio:3/4;background:#f3f4f6;position:relative;overflow:hidden;">
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,#7c3aed,#ec4899);display:flex;align-items:center;justify-content:center;">
                        <svg width="40" height="40" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                @endif
                <span style="position:absolute;top:8px;right:8px;padding:4px 10px;border-radius:20px;font-size:10px;font-weight:700;background:rgba(255,255,255,0.95);color:#7c3aed;">
                    {{ $book->type_label }}
                </span>
            </div>

            <!-- Content -->
            <div style="padding:12px;">
                <h3 style="font-size:13px;font-weight:700;color:#1f2937;margin:0 0 4px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $book->title }}</h3>
                @if($book->author)
                    <p style="font-size:11px;color:#6b7280;margin:0 0 8px;">by {{ $book->author }}</p>
                @endif

                @if(in_array($book->id, $purchasedBookIds))
                    <a href="{{ route('student.books.show', $book) }}" class="tb-btn-primary" style="padding:10px;font-size:12px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download
                    </a>
                @else
                    @if($book->isPdf() && !$book->isPhysical())
                        <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'pdf']) }}" class="tb-btn-primary" style="padding:10px;font-size:12px;">
                            Buy PDF ₹{{ number_format($book->pdf_price, 0) }}
                        </a>
                    @elseif($book->isPhysical() && !$book->isPdf())
                        <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'physical']) }}" class="tb-btn-primary" style="padding:10px;font-size:12px;{{ $book->stock_quantity <= 0 ? 'opacity:0.5;pointer-events:none;' : '' }}">
                            Buy ₹{{ number_format($book->physical_price, 0) }}
                        </a>
                        @if($book->stock_quantity <= 0)
                            <p style="font-size:10px;color:#ef4444;text-align:center;margin-top:4px;">Out of stock</p>
                        @endif
                    @else
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:6px;">
                            <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'pdf']) }}" style="background:#f3e8ff;color:#7c3aed;padding:8px 4px;border-radius:8px;font-size:11px;font-weight:600;text-align:center;text-decoration:none;">PDF ₹{{ number_format($book->pdf_price, 0) }}</a>
                            <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'physical']) }}" style="background:#fef3c7;color:#d97706;padding:8px 4px;border-radius:8px;font-size:11px;font-weight:600;text-align:center;text-decoration:none;{{ $book->stock_quantity <= 0 ? 'opacity:0.5;pointer-events:none;' : '' }}">Physical ₹{{ number_format($book->physical_price, 0) }}</a>
                        </div>
                        <a href="{{ route('student.books.checkout', ['book' => $book, 'type' => 'both']) }}" class="tb-btn-primary" style="padding:8px;font-size:11px;{{ $book->stock_quantity <= 0 ? 'opacity:0.5;pointer-events:none;' : '' }}">
                            Both ₹{{ number_format($book->pdf_price + $book->physical_price, 0) }}
                        </a>
                    @endif
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if($books->hasPages())
        <div style="padding:0 12px 12px;">
            {{ $books->links() }}
        </div>
    @endif
@else
    <div style="text-align:center;padding:48px 16px;">
        <svg width="64" height="64" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <p style="font-size:16px;font-weight:700;color:#374151;margin-bottom:4px;">No Books Available</p>
        <p style="font-size:14px;color:#6b7280;">Check back later for new arrivals.</p>
    </div>
@endif
@endsection
