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

</style>