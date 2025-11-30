<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Barokah Parfum')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif; margin: 20px; }
        .container { max-width: 960px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; text-align: left; }
        .btn { padding: 6px 10px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn-danger { background: #dc2626; color: white; border: none; cursor: pointer; }
        .alert { padding: 10px; border-radius: 4px; margin-top: 10px; }
        .alert-success { background: #dcfce7; color: #166534; }
        .field { margin-bottom: 12px; }
        label { display: block; font-weight: 600; margin-bottom: 4px; }
        input[type="text"] { width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; }
        .error { color: #b91c1c; font-size: 13px; margin-top: 4px; }
        nav a { margin-right: 8px; }
    </style>
</head>
<body>
<div class="container">
    <nav>
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('variant-types.index') }}">Variant Types</a>
    </nav>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</div>
</body>
</html>
