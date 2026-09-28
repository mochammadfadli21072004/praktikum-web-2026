<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') - {{ $appName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    @include('partials.nav')

    <main class="container py-4">

        {{-- Pesan sukses dari redirect()->with('status', ...) --}}
        @if (session('status'))
            <x-alert type="success">{{ session('status') }}</x-alert>
        @endif

        {{-- $errors otomatis tersedia di semua view setelah validasi gagal --}}
        @if ($errors->any())
            <x-alert type="error">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        @yield('content')

    </main>

    <footer class="text-center text-muted small py-3">
        &copy; {{ date('Y') }} {{ $appName }} &middot; Praktikum Framework Pemrograman Web
    </footer>

</body>
</html>
