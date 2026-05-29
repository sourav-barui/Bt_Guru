@extends('layouts.student_mobile')

@section('title', 'Notifications')

@section('mobile-content')
<div class="tb-header-gradient">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-white/80">Stay updated</p>
            <h1 class="text-2xl font-bold text-white">Notifications</h1>
        </div>
        <div class="notif-bell">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="px-4 py-4">

    {{-- Filter tabs --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
        @foreach(['All', 'Notice', 'Exam', 'Live Class', 'Course', 'Video', 'Payment'] as $tab)
        <button onclick="filterNotifs('{{ strtolower(str_replace(' ', '_', $tab)) }}')"
                id="filter-{{ strtolower(str_replace(' ', '_', $tab)) }}"
                class="flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-semibold border transition
                       {{ $loop->first ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200' }}">
            {{ $tab }}
        </button>
        @endforeach
    </div>

    @forelse($notifications as $n)
    <div class="bg-white rounded-xl border {{ $n->is_read ? 'border-gray-100' : 'border-indigo-100 bg-indigo-50/30' }} mb-3 overflow-hidden shadow-sm">
        <div class="flex items-start gap-3 p-4" data-type="{{ $n->type }}">
            <div class="notif-icon {{ $n->icon_class }} flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $n->icon_svg !!}
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-sm {{ $n->is_read ? 'font-medium text-gray-800' : 'font-bold text-gray-900' }} leading-snug">
                        {{ $n->title }}
                    </p>
                    @if(!$n->is_read)
                    <span class="w-2 h-2 rounded-full bg-indigo-600 flex-shrink-0 mt-1.5"></span>
                    @endif
                </div>
                @if($n->body)
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $n->body }}</p>
                @endif
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-gray-400">{{ $n->created_at->diffForHumans() }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ match($n->type) {
                            'notice'     => 'bg-blue-100 text-blue-700',
                            'exam'       => 'bg-orange-100 text-orange-700',
                            'live_class' => 'bg-red-100 text-red-700',
                            'course'     => 'bg-indigo-100 text-indigo-700',
                            'video'      => 'bg-purple-100 text-purple-700',
                            'payment'    => 'bg-green-100 text-green-700',
                            default      => 'bg-gray-100 text-gray-600',
                        } }}">
                        {{ ucwords(str_replace('_', ' ', $n->type)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-16">
        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <p class="text-gray-500 font-semibold mb-1">No notifications yet</p>
        <p class="text-sm text-gray-400">You'll be notified about notices, exams, live classes and more.</p>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function filterNotifs(type) {
    document.querySelectorAll('[id^="filter-"]').forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        btn.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
    });
    document.getElementById('filter-' + type)?.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
    document.getElementById('filter-' + type)?.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');

    document.querySelectorAll('[data-type]').forEach(el => {
        const card = el.closest('[data-type]') || el.parentElement;
        const t = el.getAttribute('data-type') || card.getAttribute('data-type');
        if (type === 'all' || t === type || t === type.replace('_', ' ')) {
            el.closest('.bg-white, .bg-indigo-50\\/30').style.display = '';
        } else {
            el.closest('.bg-white, .bg-indigo-50\\/30').style.display = 'none';
        }
    });
}
</script>
@endpush
@endsection
