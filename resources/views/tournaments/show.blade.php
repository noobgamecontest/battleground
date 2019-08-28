@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>{{ $tournament->name }}</h3>
                        <h4>@lang('layouts.tournaments.show.begin', ['date' => $tournament->started_at])</h4>
                    </div>
                    <div class="card-body">
                        <form class="form-inline" method="post" action="{{ route('tournaments.subscribe', $tournament) }}">
                            @csrf
                            <input type="hidden" name="tournamentId" value="{{ $tournament->id }}">
                            <div class="form-group mx-sm-3 mb-2">
                                <label for="teamName" class="col-sm-3 col-form-label">@lang('layouts.common.team_name')</label>
                                <input type="text" name="teamName" class="form-control" id="teamName">
                                @if ($errors->has('teamName'))
                                    <div class="error">{{ $errors->first('teamName') }}</div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">@lang('layouts.common.register')</button>
                        </form>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>@lang('layouts.common.id')</th>
                                <th>@lang('layouts.common.teams')</th>
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
                                                    <input type="hidden" name="teamId" value="{{ $team->id }}">
                                                    <button type="submit" class="btn btn-danger mb-2">@lang('layouts.common.delete')</button>
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
