@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3>@lang('layouts.tournaments.index.title')</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('layouts.common.name')</th>
                                    <th>@lang('layouts.common.begin')</th>
                                    <th>@lang('layouts.common.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tournaments as $tournament)
                                    <tr>
                                        <td>{{ $tournament->name }}</td>
                                        <td>{{ $tournament->started_at }}</td>
                                        <td>
                                            <a class="btn btn-primary" href="{{ route('tournament.show', $tournament->id) }}">@lang('layouts.common.subscribe')</a>
                                        </td>
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
