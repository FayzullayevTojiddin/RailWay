<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::body.start',
            fn (): string => Blade::render('
                <style>
                    .login-video {
                        position: fixed !important;
                        top: 50% !important;
                        left: 50% !important;
                        min-width: 100vw !important;
                        min-height: 100vh !important;
                        width: auto !important;
                        height: auto !important;
                        transform: translate(-50%, -50%) scale(1.1) !important;
                        object-fit: cover !important;
                        z-index: -2 !important;
                    }
                    
                    .video-overlay {
                        position: fixed !important;
                        top: 0 !important;
                        left: 0 !important;
                        width: 100vw !important;
                        height: 100vh !important;
                        background: rgba(0, 0, 0, 0.5) !important;
                        z-index: -1 !important;
                    }
                    
                    /* Login formni chapga surish */
                    .fi-simple-main {
                        margin-right: 55% !important;
                    }
                </style>
                @if(request()->routeIs(\'filament.*.auth.login\'))
                <video class="login-video" autoplay muted loop playsinline preload="auto">
                    <source src="{{ asset(\'storage/animation_login.mp4\') }}" type="video/mp4">
                </video>
                <div class="video-overlay"></div>
                @endif
            '),
        );
    }
}