<nav class="navbar navbar-expand navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">{{ $appName }}</a>

        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/products') }}">Produk</a>
            </li>

            @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                </li>

                @if (auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/users') }}">Kelola Kasir</a>
                    </li>
                @endif

                @if (auth()->user()->role === 'kasir')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pos.history') }}">Riwayat Transaksi Saya</a>
                    </li>
                @endif
            @endauth
        </ul>

        <div class="d-flex align-items-center">
            @auth
                <span class="navbar-text me-3">
                    {{ auth()->user()->name }} ({{ auth()->user()->role }})
                </span>
                <form action="{{ url('/logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            @endauth

            @guest
                <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Login</a>
            @endguest
        </div>
    </div>
</nav>
