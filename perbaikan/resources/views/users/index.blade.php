<!DOCTYPE html>
<html>
<head>
    <title>Kelola Akun Kasir</title>
</head>
<body>

    <h2>Kelola Akun Kasir</h2>
    <p><a href="/dashboard">&larr; Dashboard</a></p>

    @if (session('status'))
        <p style="color:green">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <ul style="color:red">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h3>Tambah Kasir</h3>
    <form action="/users" method="POST">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama">
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email">
        <input type="password" name="password" placeholder="Password (min. 8)">
        <button type="submit">Simpan</button>
    </form>

    <h3>Daftar Kasir</h3>
    <table border="1" cellpadding="6">
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
        @forelse ($kasirs as $kasir)
            <tr>
                <td>{{ $kasir->name }}</td>
                <td>{{ $kasir->email }}</td>
                <td>
                    <form action="/users/{{ $kasir->id }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3">Belum ada akun kasir.</td></tr>
        @endforelse
    </table>

</body>
</html>
