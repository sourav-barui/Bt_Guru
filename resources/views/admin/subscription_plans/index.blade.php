@extends('layouts.admin')

@section('title', 'Subscription Plans')
@section('page-title', 'Subscription Plans')

@section('page-content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">Manage Subscription Plans</h3>
        <a href="{{ route('admin.subscription_plans.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Plan
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead class="bg-gray-50">
                <tr>
                    <th>Plan</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Trial</th>
                    <th>Subscriptions</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($plans as $plan)
                    <tr>
                        <td>
                            <div class="flex items-center">
                                @if($plan->is_popular)
                                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-0.5 rounded-full mr-2">Popular</span>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $plan->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $plan->description }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-semibold text-gray-900">{{ $plan->formatted_price }}</span>
                        </td>
                        <td>
                            <span class="text-sm text-gray-600">{{ $plan->duration_text }}</span>
                        </td>
                        <td>
                            @if($plan->trial_days > 0)
                                <span class="text-sm text-green-600">{{ $plan->trial_days }} days</span>
                            @else
                                <span class="text-sm text-gray-400">No trial</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm text-gray-600">{{ $plan->subscriptions_count }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $plan->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.subscription_plans.show', $plan) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                <a href="{{ route('admin.subscription_plans.edit', $plan) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                                <form action="{{ route('admin.subscription_plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No subscription plans found. Create your first plan to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
