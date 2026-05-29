<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookOrder;
use App\Models\PaymentRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        $books = Book::where('tenant_id', $tenant->id)
            ->latest()
            ->withCount('orders')
            ->withCount(['paymentRequests as pending_requests_count' => function ($q) {
                $q->where('payment_type', 'book_purchase')->where('status', 'pending');
            }])
            ->paginate(15);

        return view('tenant.books.index', compact('books'));
    }

    public function create()
    {
        return view('tenant.books.create');
    }

    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'type' => 'required|in:pdf,physical,both',
            'pdf_price' => 'required_if:type,pdf,both|nullable|numeric|min:0',
            'physical_price' => 'required_if:type,physical,both|nullable|numeric|min:0',
            'stock_quantity' => 'required_if:type,physical,both|nullable|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_file' => 'required_if:type,pdf,both|nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:active,inactive,draft',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'tenant_id' => $tenant->id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'isbn' => $request->isbn,
            'type' => $request->type,
            'pdf_price' => $request->pdf_price ?? 0,
            'physical_price' => $request->physical_price ?? 0,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'status' => $request->status,
        ];

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('books/covers', 'public');
            $data['cover_image'] = $path;
        }

        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('books/pdfs', 'public');
            $data['pdf_file'] = $path;
        }

        Book::create($data);

        return redirect()->route('tenant.books.index')
            ->with('success', 'Book created successfully.');
    }

    public function show(Book $book)
    {
        $this->authorize('view', $book);
        $book->load(['orders.student']);
        
        $stats = [
            'total_orders' => $book->orders()->count(),
            'pdf_orders' => $book->orders()->whereIn('order_type', ['pdf', 'both'])->where('payment_status', 'completed')->count(),
            'physical_orders' => $book->orders()->whereIn('order_type', ['physical', 'both'])->where('payment_status', 'completed')->count(),
            'total_revenue' => $book->orders()->where('payment_status', 'completed')->sum('total_amount'),
        ];

        return view('tenant.books.show', compact('book', 'stats'));
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);
        return view('tenant.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'type' => 'required|in:pdf,physical,both',
            'pdf_price' => 'required_if:type,pdf,both|nullable|numeric|min:0',
            'physical_price' => 'required_if:type,physical,both|nullable|numeric|min:0',
            'stock_quantity' => 'required_if:type,physical,both|nullable|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:active,inactive,draft',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'isbn' => $request->isbn,
            'type' => $request->type,
            'pdf_price' => $request->pdf_price ?? 0,
            'physical_price' => $request->physical_price ?? 0,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'status' => $request->status,
        ];

        if ($request->hasFile('cover_image')) {
            // Delete old cover image
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $path = $request->file('cover_image')->store('books/covers', 'public');
            $data['cover_image'] = $path;
        }

        if ($request->hasFile('pdf_file')) {
            // Delete old PDF file
            if ($book->pdf_file) {
                Storage::disk('public')->delete($book->pdf_file);
            }
            $path = $request->file('pdf_file')->store('books/pdfs', 'public');
            $data['pdf_file'] = $path;
        }

        $book->update($data);

        return redirect()->route('tenant.books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        // Delete associated files
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->pdf_file) {
            Storage::disk('public')->delete($book->pdf_file);
        }

        $book->delete();
        return redirect()->route('tenant.books.index')
            ->with('success', 'Book deleted successfully.');
    }

    public function orders()
    {
        $tenant = Auth::user()->tenant;
        $orders = BookOrder::where('tenant_id', $tenant->id)
            ->with(['book', 'student'])
            ->latest()
            ->paginate(20);

        $pendingRequests = PaymentRequest::where('tenant_id', $tenant->id)
            ->where('payment_type', 'book_purchase')
            ->where('status', 'pending')
            ->with(['book', 'student'])
            ->latest()
            ->get();

        return view('tenant.books.orders', compact('orders', 'pendingRequests'));
    }

    public function updateOrderStatus(Request $request, BookOrder $order)
    {
        $this->authorize('update', $order->book);

        $request->validate([
            'delivery_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $data = ['delivery_status' => $request->delivery_status];
        
        if ($request->filled('tracking_number')) {
            $data['tracking_number'] = $request->tracking_number;
        }
        
        if ($request->filled('notes')) {
            $data['notes'] = $request->notes;
        }

        if ($request->delivery_status === 'delivered') {
            $data['delivered_at'] = now();
        }

        $order->update($data);

        return back()->with('success', 'Order status updated successfully.');
    }

    public function downloadPdf(Book $book)
    {
        $this->authorize('view', $book);

        if (!$book->pdf_file || !Storage::disk('public')->exists($book->pdf_file)) {
            return back()->with('error', 'PDF file not found.');
        }

        return Storage::disk('public')->download($book->pdf_file, $book->title . '.pdf');
    }
}
