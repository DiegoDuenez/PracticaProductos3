<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h3>Bienvenid@ {{$name}}</h1>
    <h4>Email de activación</h2>
    <a href="{{ url('/api/activar/cuenta/' . $codigo) }}">Activar mi cuenta</a>
</body>
</html>