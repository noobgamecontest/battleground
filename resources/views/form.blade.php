@extends('layouts.app')

@section('content')
    <h1>Saisie de résultats</h1>

    @foreach($matches as $round => $matchesRound)
        <div class="alert alert-dark" role="alert">
            Round  #{{ $round }}
        </div>
        @foreach ($matches[$round] as $match)
            <form action="{{ route('results') }}" method="post">
                {{ method_field('post') }}
                {{ csrf_field() }}
                <div class="card" style="margin-bottom: 15px">
                    <div class="card-header">
                        {{ $match->name }}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if ($match->type === 'versus')
                                <div class="col-sm">
                                    @isset($match->teams[0])
                                        <span>
                                            {{ $match->teams[0]['name'] }}
                                        </span>
                                        <span>
                                            <input type="number" class="form-control" name="">
                                        </span>
                                    @endisset
                                </div>
                                <div class="col-sm">
                                    VS
                                </div>
                                <div class="col-sm">
                                    @isset($match->teams[1])
                                        <span class="align-items-center">
                                    {{ $match->teams[1]['name'] }}
                                </span>
                                        <span>
                                    <input type="number" class="form-control">
                                </span>
                                    @endisset
                                </div>
                                <div class="col-sm">
                                    <button class="btn btn-primary float-right">Enregistrer</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        @endforeach
    @endforeach
@endsection