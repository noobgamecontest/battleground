@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="jumbotron p-3 p-md-5 text-white rounded bg-dark w-100">
                <div class="col-md-12 px-0">
                    <h1 class="display-4 font-italic">{{ $tournament->name }}</h1>
                    <p class="lead my-3">@lang('layouts.tournaments.show.begin', ['date' => $tournament->started_at])</p>
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-md-6"><label>@lang('layouts.common.platform')</label></div>
                        <div class="col-md-6"><p>{{ $tournament->platform ?? 'PC' }}</p></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><label>@lang('layouts.common.game')</label></div>
                        <div class="col-md-6"><p>{{ $tournament->game ?? 'Mario Kart 8' }}</p></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><label>@lang('layouts.common.number_of_teams')</label></div>
                        <div class="col-md-6"><p>{{ $tournament->teams->count() }} / {{ $tournament->slots }}</p></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><label>@lang('layouts.common.state')</label></div>
                        @if ($tournament->slots && $tournament->teams->count() < $tournament->slots)
                            <div class="col-md-6"><p>Inscription ouverte</p></div>
                        @else
                            <div class="col-md-6"><p>Inscription fermée</p></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-12 px-0">
                <div class="card">
                    <div class="card-header">
                        @lang('layouts.common.teams')
                    </div>
                    <div class="card-body">
                        <form class="form-inline" method="post" action="{{ route('tournaments.subscribe', $tournament) }}">
                            @csrf
                            <input type="hidden" name="tournamentId" value="{{ $tournament->id }}">
                            <div class="w-100">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="teamName" placeholder="@lang('layouts.common.team_name')" aria-label="@lang('layouts.common.team_name')" aria-describedby="button-add-team">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit" id="button-add-team">@lang('layouts.common.register')</button>
                                    </div>
                                </div>
                                @if ($errors->has('teamName'))
                                    <div class="alert alert-danger">{{ $errors->first('teamName') }}</div>
                                @endif
                            </div>
                        </form>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>@lang('layouts.common.id')</th>
                                <th>@lang('layouts.common.team')</th>
                                @if (Auth::check() && Auth::user()->isAdmin())
                                    <th>@lang('layouts.common.action')</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($tournament->teams as $team)
                                <tr>
                                    <td>{{ $team->id }}</td>
                                    <td>{{ $team->name }}</td>
                                    @admin
                                    <td>
                                        <form method="post" action="{{ route('tournaments.unsubscribe', [$tournament, $team]) }}">
                                            @csrf
                                            @method('patch')
                                            <input type="hidden" name="teamId" value="{{ $team->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm mb-2">@lang('layouts.common.delete')</button>
                                        </form>
                                    </td>
                                    @endadmin
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
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
