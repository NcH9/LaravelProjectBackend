@extends('start')

@section('content')
<div class="flex_center">
    {{-- <div class="griddy">
        @foreach ($rooms as $floor => $rooms)
            <div class="sq">
                <div class="grid1">
                    <div class="flex_center">
                        Floor: {{$room->floor}}
                    </div>
                    <div class="flex_center">
                        {{$room->id}}
                    </div>
                    <div class="flex_center">
                        @if ($room->status->id == 2)
                            <div class="green">
                        @else
                            <div class="red">
                        @endif
                            {{$room->status->name}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div> --}}
    {{-- @foreach ($groupedRooms as $floor => $rooms)
    <div class="floor">
        <h3>Этаж {{ $floor }}</h3>
        <ul>
            @foreach ($rooms as $room)
                <li>Комната {{ $room->room_number }} (Статус: {{ $room->status->name }})</li>
            @endforeach
        </ul>
    </div> --}}
{{-- @endforeach --}}
<div class="building">
    @foreach ($groupedRooms as $floor => $rooms)
        <div class="floor">
            <h3>Floor {{ $floor }}</h3>
            <div class="rooms">
                @foreach ($rooms as $room)
                    <div class="room">
                        <div class="flex_center">
                            {{ $room->id }}
                        </div>
                        <div class="flex_center">
                            @if ($room->status->id == 2)
                                <div class="green">
                            @else
                                <div class="red">
                            @endif
                                {{$room->status->name}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
</div>
@endsection



<style>
.flex_center {
    display: flex;
    justify-content: center;
    place-items: center;
}
.green {
    color: rgba(2, 225, 2, 0.75);
}
.red {
    color: rgba(255, 47, 47, 0.75);
}
.building {
    display: flex;
    flex-direction: column;
    /* gap: 20px; */
}

.floor {
    display: flex;
    flex-direction: column; /* Комнаты идут слева направо */
    gap: 10px; /* Расстояние между комнатами */
}

.rooms {
    display: flex;
    flex-wrap: wrap;
}

.room {
    display: grid;
    width: 150px; 
    margin-right: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    text-align: center;
}
</style>