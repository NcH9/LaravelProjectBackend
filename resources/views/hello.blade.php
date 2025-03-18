
@extends('start')

@section('content')
    <div class="home_bubble">
        <div class="grid1">
            <p class="flex_start">
                laravel project.
            </p>
            <p class="flex_center">
                This is a hotel management system.
            </p>
            <p class="flex_center" id="secondaryText">
                Thank god human invented sanctum so i can use this app as an api easily.
            </p>
        </div>
        
        <div class="flex_end">
            <img class="main_page_img" src="{{ asset('storage/php.png') }}" alt="pulp fiction">
        </div>
    </div>
@endsection

<style>
#secondaryText {
    color: rgba(0, 0, 0, 0.35);
}
</style>