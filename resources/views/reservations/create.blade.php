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
<div class="flex_center">

    <form method="POST" action="{{ route('reservations.confirm') }}" id="create_reservation_form">
        @csrf
        <div class="flex_center">
            <div class="grid1">
                <div class="flex_start">
                    <span>Start of wished reservation</span>
                    <input
                        type="date"
                        name="reservation_start"
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
                    >
                </div>
                @error('room_id')
                    <span class="error">{{ $message }}</span>
                @enderror

                <div class="flex_center">
                    <span>choose the room for me</span>
                    <input
                        type="checkbox"
                        name="choice"
                        id="choice"
                        onchange="choiceChange()"
                    >
                </div>


                <div class="flex_center">
                    <button type="submit">Reserve Room</button>
                </div>
            </div>
        </div>

    </form>
</div>

@endsection
</body>
</html>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<script>
    function choiceChange() {
        if (document.getElementById('choice').checked) {
            document.getElementById('room').disabled = true;
            document.getElementById('room').value = '0';
        } else {
            document.getElementById('room').disabled = false;
        }
    }
</script>
