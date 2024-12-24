
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
@extends('start')

@section('content')
    <div class="griddy">

        @can('edit reservations')
            <div class="bubble">ID</div>
        @endcan

        <div class="bubble">Start</div>
        <div class="bubble">End</div>
        <div class="bubble">Room №</div>
        <div class="bubble">User</div>

        @can('edit reservations')
            <div class="bubble">Edit</div>
        @endcan

    </div>
    <div class="griddy">

        @can('edit reservations')
            <div class="bubble">№{{$reservation->id}}</div>
        @endcan

        <div class="bubble">{{$reservation->reservation_start}}</div>
        <div class="bubble">{{$reservation->reservation_end}}</div>
        <div class="bubble">{{$reservation->room_id}}</div>
        <div class="bubble">{{$reservation->user->email}}</div>

        @can('edit reservations')
            <div class="bubble">
                <form action="{{route('reservations.startUpdate', ['reservation' => $reservation])}}" method="POST">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{$reservation->id}}">
                    <button type="submit" class="button">Edit</button>
                </form>
            </div>
        @endcan
        
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
.bubble {
    display: flex;
    justify-content: center;
    place-items: center;
    border: 1px solid black;
    border-radius: 10px;
    margin: 1px;
}
.griddy {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
}
.grid1 {
    display: grid;
}
.button {
    border: 1px solid black;
    background-color: rgba(255, 140, 0, 0.75);
    border-radius: 15px;
    min-width: 80px;
    padding: 10px;
    margin: 5px;
    box-shadow: 3px -3px 5px rgba(0, 0, 0, 0.75);
}
.button:hover {
    cursor: pointer;
}
</style>