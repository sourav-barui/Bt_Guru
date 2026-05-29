<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PwaManifestController extends Controller
{
    public function manifest(Request $request)
    {
        $tenant = app('current_tenant');
        
        if (!$tenant) {
            // Return default manifest if no tenant
            return response()->json([
                'name' => 'BT Guru Student Portal',
                'short_name' => 'BT Guru',
                'description' => 'Access your courses, exams, live classes, and notifications',
                'start_url' => '/student/dashboard',
                'display' => 'standalone',
                'background_color' => '#7C3AED',
                'theme_color' => '#7C3AED',
                'orientation' => 'portrait',
                'scope' => '/',
                'icons' => [
                    [
                        'src' => '/build/icon-placeholder.svg',
                        'sizes' => '72x72 96x96 128x128 144x144 152x152 192x192 384x384 512x512',
                        'type' => 'image/svg+xml',
                        'purpose' => 'maskable any'
                    ]
                ],
            ])->header('Content-Type', 'application/json');
        }

        // Build tenant-specific manifest
        $settings = $tenant->settings ?? [];
        $coachingName = $tenant->coaching_name ?? 'BT Guru';
        $portalTitle = $settings['portal_title'] ?? ($coachingName . ' - Student Portal');
        $shortName = $coachingName;
        
        // Determine icon source
        $iconSrc = '/build/icon-placeholder.svg';
        if ($tenant->pwa_icon) {
            $iconSrc = Storage::url($tenant->pwa_icon);
        } elseif ($tenant->logo) {
            $iconSrc = Storage::url($tenant->logo);
        }

        $manifest = [
            'name' => $portalTitle,
            'short_name' => $shortName,
            'description' => 'Access your courses, exams, live classes, and notifications',
            'start_url' => '/student/dashboard',
            'display' => 'standalone',
            'background_color' => '#7C3AED',
            'theme_color' => '#7C3AED',
            'orientation' => 'portrait',
            'scope' => '/',
            'icons' => [
                [
                    'src' => $iconSrc,
                    'sizes' => '72x72 96x96 128x128 144x144 152x152 192x192 384x384 512x512',
                    'type' => $this->getIconType($iconSrc),
                    'purpose' => 'maskable any'
                ]
            ],
            'categories' => ['education', 'learning'],
            'lang' => 'en',
            'dir' => 'ltr',
            'related_applications' => [],
            'prefer_related_applications' => false,
        ];

        return response()->json($manifest)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    private function getIconType($src)
    {
        if (str_ends_with($src, '.svg')) {
            return 'image/svg+xml';
        } elseif (str_ends_with($src, '.png')) {
            return 'image/png';
        } elseif (str_ends_with($src, '.jpg') || str_ends_with($src, '.jpeg')) {
            return 'image/jpeg';
        } elseif (str_ends_with($src, '.webp')) {
            return 'image/webp';
        }
        return 'image/png';
    }
}
