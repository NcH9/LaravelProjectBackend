@extends('start')

<title>Create Reservation</title>

@section('content')
<form method="POST" action="{{ route('reservations.confirm') }}">
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
        
            <div class="flex_center">
                <button type="submit">Reserve Room</button>
            </div>
        </div>
    </div>
    
</form>
@endsection

<style>
.flex_center {
    display: flex;
    justify-content: center;
    place-items: center;
}
.flex_start {
    display: flex;
    justify-content: flex-start;
}
.grid1 {
    display: grid;
}
input {
    border: 1px solid black;
    border-radius: 15px;
    padding: 5px;
    margin: 5px;
    background-color: rgb(230, 249, 253);
}
span {
    display: flex;
    justify-content: center;
    place-items: center;
    font-family: Georgia, 'Times New Roman', Times, serif;
    font-size: 20px;
}
button {
    background-color: rgba(255, 162, 0, 0.75);
    padding: 10px;
    margin: 10px;
    box-shadow: 3px -3px 5px gray;
    border-radius: 15px;
}
.error {
    color: rgba(255, 46, 46, 0.75);
}
</style>