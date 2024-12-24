<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hotel</title>
</head>
<body>
@extends('start')

@section('content')
    <div class="grid1">
        <span class="flex_center">
            laravel project.
        </span>
        <div class="flex_center">
            <img src="{{ asset('storage/php.png') }}" alt="pulp fiction">
        </div>
    </div>
@endsection
</body>
</html>

<style>
.flex_center {
    display: flex;
    justify-content: center;
}
.grid1 {
    display: grid;
}
span {
    font-size: 30px;
    font-weight: 700;
    font-family: Arial, Helvetica, sans-serif;
}
img {
    max-width: 900px;
    border-radius: 5px;
}

</style>