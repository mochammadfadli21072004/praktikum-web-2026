<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

    <h2>Dashboard</h2>
    <p>Halo, <strong>{{ auth()->user()->name }}</strong> (role: {{ auth()->user()->role }})</p>

    <ul>
        <li><a href="/categories">/categories</a> (khusus admin)</li>
        <li><a href="/users">/users</a> (khusus admin)</li>
    </ul>

    <form action="/logout" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

</body>
</html>
