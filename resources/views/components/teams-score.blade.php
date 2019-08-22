<div class="col-lg-6 col-sm-12">
    <span class="justify-content-center font-weight-bold">
        @isset($team['name'])
            {{ $team['name'] }}
        @endisset
    </span>
    <span style="width: 50px">
        <input type="number" class="form-control" name="{{ strtolower($team['name']) }}">
    </span>
</div>