<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Tenant::whereNotNull('custom_domain')
            ->orWhere('custom_domain', '!=', '')
            ->latest()
            ->paginate(20);
        
        return view('admin.domains.index', compact('domains'));
    }

    public function create()
    {
        $tenants = Tenant::whereNull('custom_domain')->orWhere('custom_domain', '')->get();
        return view('admin.domains.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|exists:tenants,id',
            'custom_domain' => 'required|string|unique:tenants,custom_domain',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tenant = Tenant::find($request->tenant_id);
        $tenant->update([
            'custom_domain' => $request->custom_domain,
        ]);

        return redirect()->route('admin.domains.index')
            ->with('success', 'Custom domain added successfully.');
    }

    public function edit(Tenant $tenant)
    {
        return view('admin.domains.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'custom_domain' => 'required|string|unique:tenants,custom_domain,' . $tenant->id,
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tenant->update([
            'custom_domain' => $request->custom_domain,
        ]);

        return redirect()->route('admin.domains.index')
            ->with('success', 'Custom domain updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->update(['custom_domain' => null]);
        return redirect()->route('admin.domains.index')
            ->with('success', 'Custom domain removed successfully.');
    }

    public function verify(Tenant $tenant)
    {
        // Placeholder for domain verification logic
        // In production, this would verify DNS records
        return back()->with('success', 'Domain verification check completed.');
    }
}
