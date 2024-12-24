<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>  
</head>
<body>
    
@extends('start')
@section('content')
<h1 class="flex_center">Register</h1>
<form method="POST" action="{{route('register')}}">
    @csrf
    <div class="flex_center">
        <span>Name: </span>
        <input 
            type="text"
            name="name"
            id="name"
            value="{{old('name')}}"
        >
        
    </div>
    <div class="flex_center">
        @error('name')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="flex_center">
        <span>Email: </span>
        <input 
            type="email"
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
        <span>Password: </span>
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
        <span>Confirm password: </span>
        <input 
            type="password"
            name="password_confirmation"
        >
    </div>
    <div class="flex_center">
        <button type="submit">Register</button>
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
    color: red;
    font-size: 14px;
    margin-top: 5px;
    display: block;
    font-family: Arial, sans-serif;
}
input {
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
}
</style>