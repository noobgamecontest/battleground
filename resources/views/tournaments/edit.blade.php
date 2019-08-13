@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edition d'un tournoi</div>
                <div class="card-body">
                    <form action="{{ route('tournaments.update', $tournament) }}" method="post">
                        {{ method_field('put') }}
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Nom</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" value="{{ old('name') ?: $tournament->name }}"/>
                                @if($errors->has('name'))
                                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="slots" class="col-sm-2 col-form-label">Nombre de places disponibles</label>
                            <div class="col-sm-10">
                                <input type="text" name="slots" class="form-control {{ $errors->has('slots') ? 'is-invalid' : '' }}" id="slots" value="{{ old('slots') ?: $tournament->slots }}"/>
                                @if($errors->has('slots'))
                                    <div class="invalid-feedback">{{ $errors->first('slots') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="opponents_by_match" class="col-sm-2 col-form-label">Nombre d'opposants par match</label>
                            <div class="col-sm-10">
                                <input type="text" name="opponents_by_match" class="form-control {{ $errors->has('opponents_by_match') ? 'is-invalid' : '' }}" id="opponents_by_match" value="{{ old('opponents_by_match') ?: $tournament->opponents_by_match }}"/>
                                @if($errors->has('opponents_by_match'))
                                    <div class="invalid-feedback">{{ $errors->first('opponents_by_match') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="winners_by_match" class="col-sm-2 col-form-label">Nombre de gagnants par match</label>
                            <div class="col-sm-10">
                                <input type="text" name="winners_by_match" class="form-control {{ $errors->has('winners_by_match') ? 'is-invalid' : '' }}" id="winners_by_match" value="{{ old('winners_by_match') ?: $tournament->winners_by_match }}"/>
                                @if($errors->has('winners_by_match'))
                                    <div class="invalid-feedback">{{ $errors->first('winners_by_match') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-10 offset-2">
                                <button class="btn btn-primary">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
