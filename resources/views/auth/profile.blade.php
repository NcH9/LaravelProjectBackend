
@extends('start')

@section('content')
<div class="flex_center">
    <div class="grid1">
        <h1 class="flex_center">Profile</h1>

        {{-- User info --}}
        <div class="flex_around">
            <div class="grid1">
                <div class="flex_start">
                    <span>Name: </span>
                    <span>{{$user->name}}</span>
                </div>
                <div class="flex_start">
                    <span>Email: </span>
                    <span>{{$user->email}}</span>
                </div>
            </div>
            
            <div class="grid1">
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
            </div>
        </div>
        

        {{-- Update profile image --}}
        <div class="grid1">
            <button onclick="makeForm()" id="makeFormBtn">Change profile picture</button>
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
@endsection 

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