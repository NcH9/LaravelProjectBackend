<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@extends('start')

@section('content')
    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
        <div class="report_form">
            <div class="flex_center">
                <div class="grid1">
                    <button onclick="showForm()">Generate Report</button>
                    <form action="{{route('reservations.generateReport')}}" style="display: none;" id="pdf_report_form" method="POST">
                        @csrf
                        <div class="grid1">
                            <input type="date" name="start_date">
                            @error('start_date')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="grid1">
                            <input type="date" name="end_date">
                            @error('end_date')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit">Generate</button>
                    </form>
                </div> 
            </div> 
        </div>  
    @endif

    @can('edit reservations')
        <div class="griddy">
    @else
        <div class="griddy4">
    @endcan

            @can('edit reservations')
            <div class="grid1">
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'id', 'direction' => 'asc'])}}" 
                        class="flex_center"
                    >
                        ↑
                    </a>
                </button>
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'id', 'direction' => 'desc'])}}" 
                        class="flex_center"
                    >
                        ↓
                    </a>
                </button>
                <div class="bubble">ID</div>
            </div>
            @endcan

            <div class="grid1">
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'reservation_start', 'direction' => 'asc'])}}" 
                        class="flex_center"
                    >
                        ↑
                    </a>
                </button>
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'reservation_start', 'direction' => 'desc'])}}"
                        class="flex_center"
                    >
                        ↓
                    </a>
                </button>
                <div class="bubble">Start</div>
            </div>
            <div class="grid1">
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'reservation_end', 'direction' => 'asc'])}}" 
                        class="flex_center"
                    >
                        ↑
                    </a>
                </button>
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'reservation_end', 'direction' => 'desc'])}}" 
                        class="flex_center"
                    >
                        ↓
                    </a>
                </button>
                <div class="bubble">End</div>
            </div>
            <div class="grid1">
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'room_id', 'direction' => 'asc'])}}" 
                        class="flex_center"
                    >
                        ↑
                    </a>
                </button>
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'room_id', 'direction' => 'desc'])}}" 
                        class="flex_center"
                    >
                        ↓
                    </a>
                </button>
                <div class="bubble">Room №</div>
            </div>
            <div class="grid1">
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'user_id', 'direction' => 'asc'])}}" 
                        class="flex_center"
                    >
                        ↑
                    </a>
                </button>
                <button class="sort">
                    <a href="{{route('reservations.index', ['sort_by' => 'user_id', 'direction' => 'desc'])}}" 
                        class="flex_center"
                    >
                        ↓
                    </a>
                </button>
                <div class="bubble">User</div>
            </div>


            @can('edit reservations')
                <div class="bubble">Edit</div>
            @endcan

        </div>
    @foreach ($reservations as $reservation)
        
        <a href="{{route('reservations.show', $reservation)}}">
        @can('edit reservations')
            <div class="griddy">
        @else
            <div class="griddy4">
        @endcan
                @can('edit reservations')
                    <div class="bubble">№{{$reservation->id}}</div>
                @endcan
                <div class="bubble">{{$reservation->reservation_start}}</div>
                <div class="bubble">{{$reservation->reservation_end}}</div>
                <div class="bubble">{{$reservation->room_id}}</div>
                <div class="bubble">{{$reservation->user->email}}</div>
                @can('edit reservations')
                    <div class="bubble">
                        <form action="{{route('reservations.edit', ['reservation' => $reservation])}}" method="POST">
                            @csrf
                            <input type="hidden" name="reservation_id" value="{{$reservation->id}}">
                            <button type="submit" class="button">Edit</button>
                        </form>
                    </div>
                @endcan
            </div>
        </a>

    @endforeach

    <div class="flex_center">
        {{$reservations->appends(['sort_by' => request('sort_by'), 'direction' => request('direction')])->links()}}
    </div>
    
@endsection
</body>
</html>

<style>

.report_form {
    display: grid;
    margin: 15px;
}
.flex_center {
    display: flex;
    justify-content: center;
    place-items: center;
}
.bubble {
    display: flex;
    justify-content: center;
    place-items: center;
    border: 1px solid black;
    border-radius: 10px;
    margin: 1px;
}
.griddy {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
}
.griddy4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}
.grid1 {
    display: grid;
}
.button {
    border: 1px solid black;
    background-color: rgba(255, 140, 0, 0.75);
    border-radius: 15px;
    min-width: 80px;
    padding: 10px;
    margin: 5px;
    box-shadow: 3px -3px 5px rgba(0, 0, 0, 0.75);
}
.sort {
    background-color: rgba(255, 166, 58, 0.8);
    border: 1px solid black;
    border-radius: 15px;
    margin-bottom: 5px;
}
.sort:hover {
    transition: 0.3s;
    background-color: rgba(158, 92, 10, 0.8);
}
.error {
    color: rgba(255, 46, 46, 0.75);
}
</style>
<script>
    function showForm() {
        if (document.getElementById('pdf_report_form').style.display != 'block') {
            document.getElementById('pdf_report_form').style.display = 'block';
        } else {
            document.getElementById('pdf_report_form').style.display = 'none';
        }
    }
</script>