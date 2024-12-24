<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile</title>
</head>
<body>
@extends('start')

@section('content')
<div class="flex_center">
    <div class="grid1">
        <h1 class="flex_center">Profile</h1>
        <div class="flex_center">
            <span>Name: </span>
            <span>{{$user->name}}</span>
        </div>
        <div class="flex_center">
            <span>Email: </span>
            <span>{{$user->email}}</span>
        </div>
        <div class="grid1">
            {{-- <div class="flex_center"> --}}
                <div class="flex_center">
                    <span>Profile Picture: </span>
                </div>
                <div class="flex_center">
                    @if ($user->image != null)
                        <img src="{{asset('storage/images/'.$user->image)}}" alt="Profile Picture" style="width: 100px; height: 100px;">
                    @else
                        <span>No profile picture</span>
                    @endif
                </div>
            {{-- </div> --}}
            <div class="grid1">
                <button onclick="makeForm()">Change pfp</button>
                <div class="flex_center" id="profile_picture">
                    <form 
                        action="{{route('profile.updatePicture')}}" 
                        id="image_form" 
                        method="POST" 
                        enctype="multipart/form-data"
                        style="display: none;"
                    >
                        @csrf
                        @method('POST')
                        <input id="image" type="file" name="image">
                        
                        <button type="submit">Submit</button>
                    </form>
                    @error('image')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

</div>
@endsection 
</body>
</html>

<style>
.flex_center {
    display: flex;
    justify-content: center;
    place-items: center;
}
.grid1 {
    display: grid;
}
span {
    font-size: 30px;
    font-weight: 300;
    font-family: Arial, Helvetica, sans-serif;
    margin: 10px;
}
.error {
    color: red;
}
button {
    border-radius: 15px;
    background-color: rgba(255, 140, 0, 0.75);
    padding: 10px;
    margin: 10px;
    border: none;
    font-size: 20px;
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
}
</style>

<script>
    function makeForm() {
        if (document.getElementById('image_form').style.display != 'none') {
            document.getElementById('image').value = '';
            document.getElementById('image_form').style.display = 'none';
        } else {
            document.getElementById('image_form').style.display = 'block';
        }
    }
</script>