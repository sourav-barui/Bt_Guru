@extends('layouts.tenant')
@section('title', 'Schedule Live Class')
@section('page-title', 'Schedule Live Class')

@section('page-content')
<div class="max-w-2xl">

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-red-600 text-sm mb-5">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <p class="text-sm font-medium text-gray-600">Course: <span class="text-gray-900 font-semibold">{{ $course->title }}</span></p>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('tenant.live_classes.store', $course) }}">
                @csrf
                @include('tenant.live_classes._form', ['liveClass' => null])
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary">Schedule Class</button>
                    <a href="{{ route('tenant.live_classes.index', $course) }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
