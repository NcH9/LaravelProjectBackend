@extends('start')
@section('content')
    <div class="flex_center">
        <div class="grid1">
            <div class="flex_start">
                <span>Room №</span>
                <span>{{ $room->id }}</span>
            </div>
            <div class="flex_start">
                <span>Room status</span>
                <span>{{ $room->calculatedStatus }}</span>
            </div>
        </div>
    </div>
@endsection

<link rel="stylesheet" href="{{ asset('css/app.css') }}">
