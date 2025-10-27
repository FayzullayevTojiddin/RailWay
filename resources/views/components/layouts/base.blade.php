<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Dashboard' }}</title>
    
    @stack('styles')
    @filamentStyles
</head>
<body>
    {{ $slot }}
    
    @stack('scripts')
</body>
</html>