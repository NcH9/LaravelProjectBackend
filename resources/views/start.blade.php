<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>

    <header>
        <div class="bubble_around">
            
            @auth()
                <div class="bubble_center">
                    <a href="{{route('reservations.index')}}">Reservations</a>
                </div>
            @endauth

            @can('create reservations') 
                <div class="bubble_center">
                    <a href="{{route('reservations.create')}}">Create reservation</a>
                </div>
            @endcan

            @auth()
                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin'))
                    <div class="bubble_center">
                        <a href="{{route('reports.list')}}">Reports</a>
                    </div>
                @endif
            @endauth
            

            <div class="bubble_center">
                <a href="{{route('rooms.index')}}">Rooms</a>
            </div>

            @guest()

                <div class="bubble_center">
                    <a href="{{route('login')}}">Login</a>
                </div>
                <div class="bubble_center">
                    <a href="{{route('register')}}">Register</a>
                </div>

            @endguest

            @auth()

                <div class="bubble_center">
                    <a href="{{route('profile')}}">Profile</a>
                </div>
                <div class="bubble_center">
                    <a href="{{route('logout')}}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    >Logout</a>
                </div>
                <form id="logout-form" method="POST" action="{{route('logout')}}" style="display: none;">
                    @csrf
                </form>

            @endauth
        </div>
    </header>
    
    <main>
        @yield('content')
    </main>
</body>
</html>



<style>
.bubble_around {
    display: flex;
    justify-content: space-around;
    background-color: rgba(204, 77, 30, 0.75);
    border-radius: 15px;
    border: 1px solid black;
    box-shadow: 3px -3px 5px black;
    margin: 10px;
    padding: 10px;
}
.bubble_center {
    display: flex;
    place-items: center;
    justify-content: center;
    background-color: rgba(255, 236, 247, 0.75);
    border-radius: 15px;
    border: 1px solid black;
    box-shadow: 3px -3px 5px black;
    margin: 10px;
    padding: 10px;
}
a {
    color:rgb(0, 0, 0);
    text-decoration: none;
    font-size: 25px;
    font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
}
a:hover {
    color:rgb(110, 110, 110);
}
</style>