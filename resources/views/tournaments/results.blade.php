@extends('layouts.app')

@section('content')
    <h1>Saisie de résultats</h1>

    @foreach($matches->rounds as $round => $matchesRound)
        <div class="alert alert-dark" role="alert">
            Round  #{{ $round }}

            <button class="btn btn-primary float-right">
                Close round
            </button>
        </div>

        <div class="row">
            @foreach ($matches->rounds[$round] as $match)
                <div class="col-lg-4 col-sm-12">
                    <form action="{{route('results.post', [$matches->tounamentId, $match->id])}}" method="post">
                        {{ method_field('post') }}
                        {{ csrf_field() }}
                        <input type="hidden" name="tournament" value="{{$matches->tounamentId}}">
                        <div class="card" style="margin-bottom: 15px">
                            <div class="card-header">
                                {{ $match->name }}
                            </div>
                            <div class="card-body">
                                <div class="row" style="margin-bottom: 10px">
                                    @if (count($match->teams) <= 2)
                                        @foreach($match->teams as $team)
                                            @include('components.teams-score', [$team])
                                        @endforeach
                                    @endif
                                </div>
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