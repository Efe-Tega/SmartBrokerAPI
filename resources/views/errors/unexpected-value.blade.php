<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <div class="text-center py-5">
        <h1 class="text-danger">Unexpected Error</h1>
        <p class="lead">{{ $errorMessage }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">Go Home</a>
    </div>
</body>

</html>
