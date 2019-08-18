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
                @admin
                    @if ($tournament->readyToLaunch())
                        <div class="card-footer">
                            <a class="btn btn-primary" href="{{ route('tournaments.launch', $tournament) }}" onclick="event.preventDefault(); document.getElementById('tournament-launch').submit();">
                                {{ __('Lancer') }}
                            </a>

                            <form id="tournament-launch" action="{{ route('tournaments.launch', $tournament) }}" method="POST" style="display: none;">
                                @method('patch')
                                @csrf
                            </form>
                        </div>
                    @endif
                @endadmin
            </div>
        </div>
    </div>
@endsection
