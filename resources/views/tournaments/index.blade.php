@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card w-100">
                <div class="card-header">Liste des tournois</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tournaments as $tournament)
                            <tr>
                                <td>{{ $tournament->id }}</td>
                                <td>{{ $tournament->name }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
