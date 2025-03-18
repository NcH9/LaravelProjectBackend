<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
</head>
<body>
    @foreach ($reservations as $index => $reservation)
        <div class="bubble1">
            <div class="bubble">Number: {{$index}}</div>
            <div class="bubble">Reservation Start: {{$reservation->reservation_start}}</div>
            <div class="bubble">Reservation End: {{$reservation->reservation_end}}</div>
            <div class="bubble">Room: {{$reservation->room_id}}</div>
            <div class="bubble">User Email: {{$reservation->user->email}}</div>
        </div>
    @endforeach


</body>
</html>

<style>
.bubble1 {
    display: flex;
    justify-content: center;
    place-items: center;
    border: 1px solid black;
    border-radius: 10px;
    margin: 1px;
}
.griddy5 {
    display: grid;
    grid-template-columns: repeat(5, auto);
}
</style>