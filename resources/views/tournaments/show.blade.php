@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card w-100">
                <div class="card-header">Information du tournoi {{ $tournament->name }}</div>
                <div class="card-body">
                    <ul>
                        <li># {{ $tournament->id }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
