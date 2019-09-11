@extends('layouts.app')

@section('content')
    <h1>Saisie de résultats</h1>

    @if($errors->has('teams'))
        <div class="alert alert-danger">
            <p>{{ $errors->first('teams') }}</p>
        </div>
    @endif

    @foreach($matches as $round => $matchesRound)
        <div class="alert alert-dark" role="alert">
            Round  #{{ $round }}
            @admin
                <button class="btn btn-sm btn-primary float-right" {{ $matches[$round]['complete'] ? '' : "disabled" }} >
                    Fermer le round
                </button>
            @endadmin
        </div>

        <div class="row">
            @foreach ($matches[$round]['matches'] as $match)
                <div class="col-lg-4 col-sm-12">
                    <form class="form" action="{{route('tournaments.results.post', ['match' => $match])}}" method="post">
                        {{ method_field('post') }}
                        {{ csrf_field() }}
                        <div class="card" style="margin-bottom: 15px">
                            <div class="card-header">
                                {{ $match->name }}
                            </div>
                            <div class="card-body">

                            @foreach($match->teams as $team)
                                <div class="form-group">
                                    <label for="">{{ $team->name }}</label>
                                    <input type="number" class="form-control" name="teams[{{ $team->id }}]" value="{{ $team->pivot->score  }}">
                                </div>
                            @endforeach
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <button class="btn btn-primary btn-block">Enregistrer</button>
                                </div>
                            </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection