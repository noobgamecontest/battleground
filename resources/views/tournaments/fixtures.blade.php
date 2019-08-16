@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1>{{ $tournament->name }}</h1>
            @foreach($tournament->matches as $match)
                <div class="card mb-3">
                    <div class="card-header">{{ $match->name }}</div>
                    <div class="card-body">
                        <form action="{{ route('tournaments.saveScores', $tournament) }}" method="post">
                            {{ method_field('post') }}
                            {{ csrf_field() }}
                            <input type="hidden" name="match_id" value="{{ $match->id }}">
                            @foreach($match->teams as $team)
                                <div class="form-group row">
                                    <input type="hidden" name="team_id[]" value="{{ $team->id }}">
                                    <label class="col-sm-2 col-form-label">{{ $team->name }}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <input type="text" name="score[]" class="form-control" value="{{ $team->pivot->score }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">1st</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="form-group row">
                                <div class="col-sm-10 offset-2">
                                    <button class="btn btn-primary">Enregistrer les scores</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
