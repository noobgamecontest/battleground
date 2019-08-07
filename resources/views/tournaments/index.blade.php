@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2>Tournois</h2>
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Ouverts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Fermés</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    @foreach($tournaments as $tournament)
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <h5 class="card-title">{{ $tournament->name }}</h5>
                                        <h6 class="card-subtitle text-muted">{{ 'Mario Kart 8' }}</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="m-0">
                                            @if($tournament->started_at)
                                                Débute le {{ $tournament->started_at }}
                                            @else
                                                Aucune date prévue
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="m-0">
                                            @if($tournament->slots)
                                                {{ $tournament->teams->count() }} / {{ $tournament->slots }} équipes
                                            @else
                                                {{ $tournament->teams->count() }} équipes
                                            @endif
                                        </p>
                                        @if ($tournament->slots && $tournament->teams->count() < $tournament->slots)
                                            <p class="m-0"><a href="#">Inscription ouverte</a></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
