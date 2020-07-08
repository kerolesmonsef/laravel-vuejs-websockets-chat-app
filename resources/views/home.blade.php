@extends('layouts.app')

@section('content')
    <div class="container">

        <chat :auth="{{ auth()->user() }}"/>

    </div>
@endsection
