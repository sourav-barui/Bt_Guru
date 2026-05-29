<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        return view('tenant.settings', compact('tenant'));
    }

    public function update(Request $request)
    {
        $request->validate([
            // System
            'coaching_name' => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'tagline'       => 'nullable|string|max:255',
            'website'       => 'nullable|url|max:255',
            // Contact
            'phone'         => 'nullable|string|max:20',
            'phone_alt'     => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'city'          => 'nullable|string|max:100',
            'state'         => 'nullable|string|max:100',
            'pincode'       => 'nullable|string|max:10',
            // Social
            'facebook'      => 'nullable|url|max:255',
            'instagram'     => 'nullable|url|max:255',
            'youtube'       => 'nullable|url|max:255',
            'telegram'      => 'nullable|string|max:255',
            'whatsapp'      => 'nullable|string|max:20',
            'twitter'       => 'nullable|url|max:255',
            'linkedin'      => 'nullable|url|max:255',
            // Payment
            'upi_id'        => 'nullable|string|max:100',
            'upi_name'      => 'nullable|string|max:100',
            'bank_name'     => 'nullable|string|max:100',
            'bank_account'  => 'nullable|string|max:50',
            'bank_ifsc'     => 'nullable|string|max:20',
            'bank_holder'   => 'nullable|string|max:100',
            // Email Config
            'mail_driver'   => 'nullable|in:smtp,sendmail,mailgun,ses,log',
            'mail_host'     => 'nullable|string|max:255',
            'mail_port'     => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption'  => 'nullable|in:tls,ssl,none',
            'mail_from_address'=> 'nullable|email|max:255',
            'mail_from_name'   => 'nullable|string|max:255',
            // WhatsApp API
            'wa_provider'   => 'nullable|in:twilio,wati,ultramsg,custom',
            'wa_api_url'    => 'nullable|url|max:500',
            'wa_api_key'    => 'nullable|string|max:500',
            'wa_instance_id'=> 'nullable|string|max:255',
            'wa_token'      => 'nullable|string|max:500',
            'wa_from_number'=> 'nullable|string|max:20',
            // Branding
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
            'pwa_icon'      => 'nullable|image|mimes:png,svg|max:2048',
            'portal_icon'   => 'nullable|image|mimes:jpg,jpeg,png,svg,ico,webp|max:2048',
            'portal_title'  => 'nullable|string|max:100',
        ]);

        $tenant = Auth::user()->tenant;
        $settings = $tenant->settings ?? [];

        // System settings
        if ($request->filled('coaching_name')) {
            $tenant->coaching_name = $request->coaching_name;
        }
        if ($request->filled('email')) {
            $tenant->email = $request->email;
        }
        $tenant->phone   = $request->phone;
        $tenant->address = $request->address;

        // Handle logo uploads
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $tenant->logo = $request->file('logo')->store('tenant/logos', 'public');
        }

        if ($request->hasFile('pwa_icon')) {
            // Delete old PWA icon if exists
            if ($tenant->pwa_icon) {
                Storage::disk('public')->delete($tenant->pwa_icon);
            }
            $tenant->pwa_icon = $request->file('pwa_icon')->store('tenant/pwa_icons', 'public');
        }

        if ($request->hasFile('portal_icon')) {
            // Delete old portal icon if exists
            if ($tenant->portal_icon) {
                Storage::disk('public')->delete($tenant->portal_icon);
            }
            $tenant->portal_icon = $request->file('portal_icon')->store('tenant/portal_icons', 'public');
        }

        // Settings JSON fields
        foreach ([
            'tagline', 'website', 'phone_alt', 'city', 'state', 'pincode',
            'facebook', 'instagram', 'youtube', 'telegram', 'whatsapp', 'twitter', 'linkedin',
            'upi_id', 'upi_name', 'bank_name', 'bank_account', 'bank_ifsc', 'bank_holder',
            'mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_encryption',
            'mail_from_address', 'mail_from_name',
            'wa_provider', 'wa_api_url', 'wa_api_key', 'wa_instance_id', 'wa_token', 'wa_from_number',
            'portal_title',
        ] as $field) {
            $settings[$field] = $request->input($field);
        }

        // Never overwrite password if blank (keep existing)
        if ($request->filled('mail_password')) {
            $settings['mail_password'] = $request->mail_password;
        } elseif (!isset($settings['mail_password'])) {
            $settings['mail_password'] = null;
        }

        $tenant->settings = $settings;
        $tenant->save();

        return back()->with('success', 'Settings saved successfully.')->with('active_tab', $request->input('active_tab', 'system'));
    }
}
