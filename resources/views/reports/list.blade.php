<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hotel Project</title>

</head>
<body>
@extends('start')
@section('content')
    <form action="{{ route('reports.list') }}" method="GET">
        @csrf
        <div class="flex_center">
            <div class="grid1">
                <input type="text" name="term" placeholder="find reports" value="{{ old('term') }}">

                <button type="submit">Search</button>
            </div>
        </div>
    </form>

    @if (!empty($reports))
        <div class="grid1">
            @foreach($reports as $report)
                <div class="flex_center">
                    <span>{{$report['name']}}</span>
                    <a href="{{$report['url']}}">read</a>
                </div>
            @endforeach
        </div>
    @endif
    
@endsection
</body>
</html>
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