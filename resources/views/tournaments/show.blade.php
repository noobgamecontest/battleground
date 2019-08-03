@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                @include('layouts.flash')
                <div class="card">
                    <div class="card-header">
                        <h3>{{ $tournament->name }}</h3>
                        <h4>@lang('layouts.tournaments.show.begin', ['date' => $tournament->started_at])</h4>
                    </div>
                    <div class="card-body">
                        <form class="form-inline" method="post" action="{{ route('tournament.subscribe') }}">
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
                                <th>@lang('layouts.common.number')</th>
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
                                        @if (Auth::check() && Auth::user()->isAdmin())
                                            <td>
                                                <form method="post" action="{{ route('tournament.deleteTeam') }}">
                                                    @csrf
                                                    <input type="hidden" name="teamId" value="{{ $team->id }}">
                                                    <button type="submit" class="btn btn-danger mb-2">@lang('layouts.common.delete')</button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
