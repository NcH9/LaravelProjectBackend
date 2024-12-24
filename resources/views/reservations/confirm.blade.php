<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Confirm reservation</title>
</head>
@extends('start')
<body>
    @section('content')
    <form method="POST" action="{{route('reservations.store')}}">
        @csrf
        <div class="flex_center">
            You ordered room {{$room_id}} on the floor {{$floor}}. <br>
            Reservation start on {{$reservation_start}} and ends on {{$reservation_end}}. <br>
            In total of {{$days_amount}} days, the price of this booking will cost {{$price}} UAH. <br>
            Does this booking satisfy you?
        </div>
        <input type="hidden" name="reservation_start" value="{{ $reservation_start }}">
        <input type="hidden" name="reservation_end" value="{{ $reservation_end }}">
        <input type="hidden" name="room_id" value="{{ $room_id }}">
        <div class="grid1">
            @error('reservation_start')
                <span class="error">{{ $message }}</span>
            @enderror
            @error('reservation_end')
                <span class="error">{{ $message }}</span>
            @enderror
            @error('room_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex_center"><button type="submit">Yes</button></div>

    </form>
    <div class="flex_center"><button onclick="window.location.href = './create'">No</button></div>
    </body>
</html>
@endsection

<style>
.flex_center {
    display: flex;
    justify-content: center;
    place-items: center;
}
.grid1 {
    display: grid;
}
</style>