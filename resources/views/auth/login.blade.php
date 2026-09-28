<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

    <h2>Login</h2>

    @if ($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form action="/login" method="POST">
        @csrf
        <p><input type="email" name="email" value="{{ old('email') }}" placeholder="Email"></p>
        <p><input type="password" name="password" placeholder="Password"></p>
        <button type="submit">Masuk</button>
    </form>

</body>
</html>
