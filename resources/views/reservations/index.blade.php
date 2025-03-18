<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hotel Project</title>
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


    {{-- Sort --}}
    @if ($reservations->count() > 1)
        <h3 class="flex_center">Sort By</h3>
        <div class="flex_center">
            {{-- Id --}}
            @can('edit reservations')
                <div class="sort_bubble">
                    <span>ID</span>
                    <a href="{{route('reservations.index', ['sort_by' => 'id', 'direction' => 'asc'])}}" 
                        class="sort"
                    >
                        ▲
                    </a>
                    <a href="{{route('reservations.index', ['sort_by' => 'id', 'direction' => 'desc'])}}" 
                        class="sort"
                    >
                        ▼
                    </a>
                </div>
            @endcan
                
            {{-- Start --}}
            <div class="sort_bubble">
                <span>Start</span>
                <a href="{{route('reservations.index', ['sort_by' => 'reservation_start', 'direction' => 'asc'])}}" 
                    class="sort"
                >
                    ▲
                </a>
                <a href="{{route('reservations.index', ['sort_by' => 'reservation_start', 'direction' => 'desc'])}}"
                    class="sort"
                >
                    ▼
                </a>
            </div>
            
            {{-- End --}}
            <div class="sort_bubble">
                <span>End</span>
                <a href="{{route('reservations.index', ['sort_by' => 'reservation_end', 'direction' => 'asc'])}}" 
                    class="sort"
                >
                    ▲
                </a>
                <a href="{{route('reservations.index', ['sort_by' => 'reservation_end', 'direction' => 'desc'])}}" 
                    class="sort"
                >
                    ▼
                </a>
            </div>
            
            {{-- Room --}}
            <div class="sort_bubble">
                <span>Room</span>
                <a href="{{route('reservations.index', ['sort_by' => 'room_id', 'direction' => 'asc'])}}" 
                    class="sort"
                >
                    ▲
                </a>
                <a href="{{route('reservations.index', ['sort_by' => 'room_id', 'direction' => 'desc'])}}" 
                    class="sort"
                >
                    ▼
                </a>
            </div>
            
            {{-- User --}}
            @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                <div class="sort_bubble">
                    <span>User</span>
                    <a href="{{route('reservations.index', ['sort_by' => 'user_id', 'direction' => 'asc'])}}" 
                        class="sort"
                    >
                        ▲
                    </a>
                    <a href="{{route('reservations.index', ['sort_by' => 'user_id', 'direction' => 'desc'])}}" 
                        class="sort"
                    >
                        ▼
                    </a>
                </div>
            @endif
        </div>
    @endif

    <div class="grid2">
        @foreach ($reservations as $reservation)
            <a href="{{route('reservations.show', $reservation)}}">

                <div class="reservation_bubble_link">
                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                        <div class="flex_start">
                            <span>ID: </span>
                            <span>{{$reservation->id}}</span>
                        </div>
                    @endif
                    <div class="flex_start">
                        <span>Reservation Start: </span>
                        <span>{{$reservation->reservation_start}}</span>
                    </div>
                    <div class="flex_start">
                        <span>Reservation End: </span>
                        <span>{{$reservation->reservation_end}}</span>
                    </div>
                    <div class="flex_start">
                        <span>Room №: </span>
                        <span>{{$reservation->room_id}}</span>
                    </div>
                    <div class="flex_start">
                        <span>User: </span>
                        <span>{{$reservation->user->email}}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="flex_center">
        {{$reservations->appends(['sort_by' => request('sort_by'), 'direction' => request('direction')])->links()}}
    </div>
    
@endsection
</body>
</html>

<style>


.reservation_bubble {
    display: flex;
    justify-content: center;
    place-items: center;
    border: 1px solid black;
    border-radius: 10px;
    margin: 1px;
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
    display: flex;
    justify-content: center;
    place-items: center;
    font-size: medium;
}
.sort:hover {
    color: rgba(97, 97, 97, 0.75);
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