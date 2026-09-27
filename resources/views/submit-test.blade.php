<!DOCTYPE html>
<html>
<head>
    <title>Submit Test</title>
</head>
<body>

    <h2>Test Submit (POST biasa)</h2>
    <form action="/submit" method="POST">
        @csrf
        <input type="text" name="pesan" placeholder="Tulis pesan">
        <button type="submit">Kirim</button>
    </form>

    <hr>

    <h3>Test Store Photo</h3>
    <form action="/photos" method="POST">
        @csrf
        <input type="text" name="judul" placeholder="Judul foto">
        <button type="submit">Kirim</button>
    </form>

</body>
</html>