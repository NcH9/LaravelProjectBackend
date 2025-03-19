<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hotel Project</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@extends('start')

@section('content')
<div class="flex_center">

    <form method="POST" id="reservation_update_form" action="{{ route('reservations.confirmUpdate', ['reservation' => $reservation->id]) }}">
        @csrf
        @method('POST')
        <div class="flex_center">
            <div class="grid1">
                <div class="flex_start">
                    <span>Start of wished reservation</span>
                    <input
                        type="date"
                        name="reservation_start"
                        value="{{$reservation->reservation_start}}"
                    >
                </div>
                @error('reservation_start')
                    <span class="error">{{ $message }}</span>
                @enderror

                <div class="flex_start">
                    <span>End of wished reservation</span>
                    <input
                        type="date"
                        name="reservation_end"
                        value="{{$reservation->reservation_end}}"
                    >
                </div>
                @error('reservation_end')
                    <span class="error">{{ $message }}</span>
                @enderror

                <div class="flex_start">
                    <span>Room №</span>
                    <input
                        type="number"
                        name="room_id"
                        id="room"
                        value="{{$reservation->room_id}}"
                    >
                </div>
                @error('room_id')
                    <span class="error">{{ $message }}</span>
                @enderror

                <div class="flex_center">
                    <button type="submit">Update Reservation</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
</body>
</html>

