
@extends('start')

@section('content')
<div class="flex_center">
    <div class="grid1">
        <div class="flex_center">
            <div class="grid1">
                <div class="flex_center">
                    <span>Check out when rooms are available</span>
                    <button onclick="showForm()" id="showFormBtn">+</button>
                </div>
                <form action="{{route('rooms.index')}}" 
                    id="look_occupied_rooms_form"
                    method="GET"
                >
                    <div class="flex_center">
                        <span>Start</span>
                        <input type="date" name="start" value="{{request()->start}}">
                        <span>End</span>
                        <input type="date" name="end" value="{{request()->end}}">
                    </div>
                    <button type="submit">Look</button>
                </form>
            </div>
        </div>
        @foreach ($groupedRooms as $floor => $rooms)
            <div class="floor" id="floor">
                <span class="floorName" >Floor {{ $floor }}</span>
                @foreach ($rooms as $room)
                    <a href="{{ route('rooms.show', $room->id) }}">
                        <div class="room_bubble">
                            <div class="flex_center">
                                {{ $room->id }}
                            </div>
                            <div class="flex_center">
                                @if ($room->calculatedStatus == 'Available')
                                    <div class="green">
                                @else
                                    <div class="red">
                                @endif
                                    {{$room->calculatedStatus}}
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endsection

<script>
function showForm() {
    let form = document.getElementById('look_occupied_rooms_form');
    if (form.style.display != 'block') {
        document.getElementById('showFormBtn').innerText = '-';
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
        document.getElementById('showFormBtn').innerText = '+';
    }
}
</script>
<style>
#showFormBtn {
    display: flex;
    justify-content: center;
    place-items: center;
    width: 50px;
    height: 50px;
    font-size: 25px;
    border-radius: 50%;
    background-color: #ffffff;
}
#showFormBtn:hover {
    background-color: #f0f0f0;
}
</style>

