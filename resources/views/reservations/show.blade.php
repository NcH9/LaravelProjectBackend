
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
    <div class="reservation_bubble">
        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
            <div class="flex_start">
                <span>ID: </span>
                <span>{{$reservation->id}}</span>
            </div>
        @endif
        <div class="flex_start">
            <span>Reservation Start: </span>
            <span>{{$reservation->reservation_start}}</span>
        </div>
        <div class="flex_start">
            <span>Reservation End: </span>
            <span>{{$reservation->reservation_end}}</span>
        </div>
        <div class="flex_start">
            <span>Room №: </span>
            <span>{{$reservation->room_id}}</span>
        </div>
        <div class="flex_start">
            <span>User: </span>
            <span>{{$reservation->user->email}}</span>
        </div>
        @if (Gate::allows('show-and-redact-reservation', $reservation))
            <form action="{{route('reservations.edit', ['reservation' => $reservation])}}"
                class="hidden_form"
                method="POST"
            >
                @csrf
                <input type="hidden" name="reservation_id" value="{{$reservation->id}}">
                <button type="submit">Edit</button>
            </form>
        @endif
    </div>
@endsection
</body>
</html>

<style>
form {
    border: none;
    box-shadow: none;
}
.hidden_form {
    width: 100%;
}

</style>