<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hotel Project</title>

</head>
<body>

@extends('start')
@section('content')
    <h1 class="flex_center">Login</h1>

    <form method="POST" action="{{route('login')}}">
        @csrf

        <div class="flex_center">
            <span>Email</span>
            <input
                type="text"
                name="email"
                id="email"
                value="{{old('email')}}"
            >
            
        </div>
        <div class="flex_center">
            @error('email')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex_center">
            <span>Password</span>
            <input
                type="password"
                name="password"
                id="password"
            >
            
        </div>
        <div class="flex_center">
            @error('password')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        

        <div class="flex_center">
            <button type="submit">Login</button>
        </div>
    </form>
@endsection
</body>
</html>
<style>
.flex_center {
    display: flex;
    justify-content: center;
    place-items: center;
}
.error {
    color: red; /* Текст ошибки будет красным */
    font-size: 14px; /* Размер шрифта ошибки */
    margin-top: 5px; /* Отступ сверху для разделения с элементами формы */
    display: block; /* Ошибки будут отображаться на отдельной строке */
    font-family: Arial, sans-serif; /* Простой шрифт для читаемости */
}
/* input {
    border: 1px solid black;
    border-radius: 15px;
    padding: 5px;
    margin: 5px;
    background-color: rgb(230, 249, 253);
}
span {
    display: flex;
    justify-content: center;
    place-items: center;
    font-family: Georgia, 'Times New Roman', Times, serif;
    font-size: 20px;
}
button {
    background-color: rgba(255, 162, 0, 0.75);
    padding: 10px;
    margin: 10px;
    box-shadow: 3px -3px 5px gray;
    border-radius: 15px;
} */
</style>