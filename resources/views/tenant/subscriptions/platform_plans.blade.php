@extends('layouts.tenant')

@section('title', 'Subscription Plans')
@section('page-title', 'Choose Your Plan')

@section('page-content')
<div class="max-w-6xl mx-auto">
    @if($currentSubscription && $currentSubscription->isActive())
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-800 font-medium">You have an active subscription</p>
                    <p class="text-blue-600 text-sm">Your {{ $currentSubscription->plan->name }} plan is valid until {{ $currentSubscription->end_date->format('M d, Y') }}</p>
                </div>
                <a href="{{ route('tenant.subscriptions.current') }}" class="btn-primary">View Details</a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden {{ $plan->is_popular ? 'ring-2 ring-purple-500' : '' }}">
                @if($plan->is_popular)
                    <div class="bg-purple-600 text-white text-center py-2 text-sm font-semibold">
                        Most Popular
                    </div>
                @endif

                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                    <p class="text-gray-500 text-sm mt-1">{{ $plan->description }}</p>

                    <div class="mt-4">
                        <span class="text-3xl font-bold text-gray-900">{{ $plan->formatted_price }}</span>
                        <span class="text-gray-500">/ {{ $plan->duration_text }}</span>
                    </div>

                    @if($plan->trial_days > 0)
                        <div class="mt-2 text-sm text-green-600">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $plan->trial_days }} days free trial
                        </div>
                    @endif

                    @if($plan->features)
                        <ul class="mt-6 space-y-3">
                            @foreach(is_array($plan->features) ? $plan->features : json_decode($plan->features, true) as $feature)
                                <li class="flex items-start text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <form action="{{ route('tenant.subscriptions.subscribe') }}" method="POST" class="mt-6 space-y-3">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                        <div>
                            <input type="text" name="coupon_code" id="coupon_{{ $plan->id }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="Coupon code (optional)">
                        </div>

                        <button type="submit" class="w-full btn-primary {{ $plan->is_popular ? 'bg-purple-600 hover:bg-purple-700' : '' }}">
                            {{ $plan->trial_days > 0 ? 'Start Free Trial' : 'Subscribe Now' }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('form').forEach(form => {
    const couponInput = form.querySelector('input[name="coupon_code"]');
    const planId = form.querySelector('input[name="plan_id"]').value;

    couponInput.addEventListener('blur', function() {
        if (this.value.trim()) {
            fetch('{{ route("tenant.subscriptions.apply_coupon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    code: this.value,
                    plan_id: planId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.valid) {
                    alert('Coupon applied! ' + data.coupon.discount_type + ' discount: ' + data.pricing.discount_amount);
                } else {
                    alert('Invalid coupon: ' + data.message);
                }
            });
        }
    });
});
</script>
@endpush
@endsection
