@extends('layouts.tenant')

@section('title', 'Edit Book')
@section('page-title', 'Edit Book')

@section('page-content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Book Details</h3>
        </div>

        <form method="POST" action="{{ route('tenant.books.update', $book) }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="form-label">Book Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}"
                           class="form-input @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Author -->
                <div>
                    <label for="author" class="form-label">Author</label>
                    <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}"
                           class="form-input @error('author') border-red-500 @enderror">
                    @error('author')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publisher -->
                <div>
                    <label for="publisher" class="form-label">Publisher</label>
                    <input type="text" id="publisher" name="publisher" value="{{ old('publisher', $book->publisher) }}"
                           class="form-input @error('publisher') border-red-500 @enderror">
                    @error('publisher')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ISBN -->
                <div>
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}"
                           class="form-input @error('isbn') border-red-500 @enderror">
                    @error('isbn')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="form-label">Book Type <span class="text-red-500">*</span></label>
                    <select id="type" name="type" class="form-input @error('type') border-red-500 @enderror" required onchange="toggleTypeFields()">
                        <option value="pdf" {{ old('type', $book->type) === 'pdf' ? 'selected' : '' }}>PDF Only</option>
                        <option value="physical" {{ old('type', $book->type) === 'physical' ? 'selected' : '' }}>Physical Only</option>
                        <option value="both" {{ old('type', $book->type) === 'both' ? 'selected' : '' }}>PDF & Physical</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PDF Price -->
                <div id="pdf_price_container" class="{{ $book->type === 'physical' ? 'hidden' : '' }}">
                    <label for="pdf_price" class="form-label">PDF Price (₹) <span class="text-red-500 pdf-required">*</span></label>
                    <input type="number" id="pdf_price" name="pdf_price" value="{{ old('pdf_price', $book->pdf_price) }}" step="0.01" min="0"
                           class="form-input @error('pdf_price') border-red-500 @enderror">
                    @error('pdf_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Physical Price -->
                <div id="physical_price_container" class="{{ $book->type === 'pdf' ? 'hidden' : '' }}">
                    <label for="physical_price" class="form-label">Physical Price (₹) <span class="text-red-500 physical-required">*</span></label>
                    <input type="number" id="physical_price" name="physical_price" value="{{ old('physical_price', $book->physical_price) }}" step="0.01" min="0"
                           class="form-input @error('physical_price') border-red-500 @enderror">
                    @error('physical_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Quantity -->
                <div id="stock_container" class="{{ $book->type === 'pdf' ? 'hidden' : '' }}">
                    <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-red-500 physical-required">*</span></label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $book->stock_quantity) }}" min="0"
                           class="form-input @error('stock_quantity') border-red-500 @enderror">
                    @error('stock_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="form-input @error('status') border-red-500 @enderror" required>
                        <option value="active" {{ old('status', $book->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $book->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="draft" {{ old('status', $book->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cover Image -->
                <div class="md:col-span-2">
                    <label for="cover_image" class="form-label">Cover Image</label>
                    @if($book->cover_image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-40 object-cover rounded-lg">
                            <p class="text-sm text-gray-500 mt-1">Current cover image</p>
                        </div>
                    @endif
                    <input type="file" id="cover_image" name="cover_image" accept="image/*"
                           class="form-input @error('cover_image') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Leave empty to keep current image. Recommended: 400x600 pixels, JPG or PNG (max 2MB)</p>
                    @error('cover_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PDF File -->
                <div id="pdf_file_container" class="md:col-span-2 {{ $book->type === 'physical' ? 'hidden' : '' }}">
                    <label for="pdf_file" class="form-label">PDF File</label>
                    @if($book->pdf_file)
                        <div class="mb-3">
                            <a href="{{ route('tenant.books.download', $book) }}" class="text-blue-600 hover:underline">
                                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Current PDF file
                            </a>
                        </div>
                    @endif
                    <input type="file" id="pdf_file" name="pdf_file" accept=".pdf"
                           class="form-input @error('pdf_file') border-red-500 @enderror">
                    <p class="text-sm text-gray-500 mt-1">Leave empty to keep current file. Max 10MB</p>
                    @error('pdf_file')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="form-input @error('description') border-red-500 @enderror">{{ old('description', $book->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('tenant.books.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Book</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTypeFields() {
    const type = document.getElementById('type').value;
    const pdfPriceContainer = document.getElementById('pdf_price_container');
    const physicalPriceContainer = document.getElementById('physical_price_container');
    const stockContainer = document.getElementById('stock_container');
    const pdfFileContainer = document.getElementById('pdf_file_container');
    const pdfRequired = document.querySelectorAll('.pdf-required');
    const physicalRequired = document.querySelectorAll('.physical-required');

    if (type === 'pdf') {
        pdfPriceContainer.classList.remove('hidden');
        physicalPriceContainer.classList.add('hidden');
        stockContainer.classList.add('hidden');
        pdfFileContainer.classList.remove('hidden');
        pdfRequired.forEach(el => el.classList.remove('hidden'));
        physicalRequired.forEach(el => el.classList.add('hidden'));
    } else if (type === 'physical') {
        pdfPriceContainer.classList.add('hidden');
        physicalPriceContainer.classList.remove('hidden');
        stockContainer.classList.remove('hidden');
        pdfFileContainer.classList.add('hidden');
        pdfRequired.forEach(el => el.classList.add('hidden'));
        physicalRequired.forEach(el => el.classList.remove('hidden'));
    } else {
        pdfPriceContainer.classList.remove('hidden');
        physicalPriceContainer.classList.remove('hidden');
        stockContainer.classList.remove('hidden');
        pdfFileContainer.classList.remove('hidden');
        pdfRequired.forEach(el => el.classList.remove('hidden'));
        physicalRequired.forEach(el => el.classList.remove('hidden'));
    }
}

// Initialize on page load
toggleTypeFields();
</script>
@endsection
