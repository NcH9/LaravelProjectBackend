<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hotel Project</title>
    @vite('../resources/css/forms.css')
    @vite('../resources/css/general.css')
    @vite('../resources/css/bubbles.css')
    {{-- @vite('../resources/css/app.css') --}}
    {{-- <link rel="stylesheet" href="{{asset('css/app.css')}}"> --}}
    {{-- <link rel="stylesheet" href="{{asset('css/forms.css')}}"> --}}
</head>
<body>

    <header>
        <div class="nav_bubble">
            
            <div class="flex_center">

                <a href="{{route('home')}}">Home</a>
                @auth()
                    <a href="{{route('reservations.index')}}">Reservations</a>
                @endauth

                @can('create reservations') 
                    <a href="{{route('reservations.create')}}">Create reservation</a>
                @endcan

                @auth()
                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin'))
                        <a href="{{route('reports.list')}}">Reports</a>
                    @endif
                @endauth
                
                <a href="{{route('rooms.index')}}">Rooms</a>

            </div>

            <div class="flex_center">
                @guest()
                    <a href="{{route('login')}}">Login</a>
                    <a href="{{route('register')}}">Register</a>
                @endguest

                @auth()
                    @if (auth()->user()->image)
                        <img src="{{asset('storage/images/' . auth()->user()->image)}}" alt="profile image" style="width: 50px; height: 50px; border-radius: 50%;">
                    @endif
                    <a href="{{route('profile')}}">Profile</a>
                    <a href="{{route('logout')}}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    >Logout</a>
                    <form id="logout-form" method="POST" action="{{route('logout')}}" style="display: none;">
                        @csrf
                    </form>
                @endauth
            </div>

        </div>
    </header>
    
    <main>
        @yield('content')
    </main>
</body>
</html>



<style>
</style>