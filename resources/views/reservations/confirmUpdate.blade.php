
@extends('start')
    @section('content')
    <div class="bubble" id="confirm_bubble">
        <p class="flex_center">
            You ordered <span class="green">room {{$reservation['room_id']}}</span> on the <span class="green">floor {{$reservation['floor']}}</span>.
        </p>
        <p class="flex_center">
            <span>Reservation start on {{$reservation['reservation_start']}} and ends on {{$reservation['reservation_end']}}.</span>
        </p>
        <p class="flex_center">
            In total of <span class="green">{{$reservation['days_amount']}} days</span>,
            the price of this booking is <span class="green">{{$reservation['price']}} UAH</span>.
        </p>
        <p class="flex_center">
            <span>Does this booking satisfy you?</span>
        </p>
    </div>

    <form
        method="POST"
        action="{{route('reservations.update', ['reservation' => $reservation['id']]) }}"
        >
        @csrf
        @method('PUT')

        <input type="hidden" name="reservation_start" value="{{ $reservation['reservation_start'] }}">
        <input type="hidden" name="reservation_end" value="{{ $reservation['reservation_end'] }}">
        <input type="hidden" name="room_id" value="{{ $reservation['room_id'] }}">
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
        <div class="flex_center"><button type="submit" id="confirm_reservation_btn">Yes</button></div>
    </form>
    <div class="flex_center">
        <button id="confirm_reservation_btn"
            onclick="window.location.href = './confirmUpdate/${{$reservation['id']}}/edit'"
        >
            No
        </button>
    </div>
@endsection

<link rel="stylesheet" href="{{ asset('css/app.css') }}">
