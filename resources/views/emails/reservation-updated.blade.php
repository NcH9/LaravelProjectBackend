<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<div>
    <p>Reservation in PalmoProjectHotel is updated. New data:</p>
    <p>
        Period: From the {{$reservation->reservation_start}} to the {{$reservation->reservation_end}}
    </p>
    <p>
        Reserved by: {{$reservation->user->name}}
    </p>
    <p>
        Room number: {{$reservation->room->id}} on the floor: {{$reservation->room->floor}}
    </p>
</div>
</body>
</html>