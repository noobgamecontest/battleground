<div class="row justify-content-center">
    <div class="col-md-8">
        @if ($message = session('success'))
            <div class="alert alert-success">{{ $message }}</div>
        @endif

        @if ($message = session('error'))
            <div class="alert alert-danger">{{ $message }}</div>
        @endif

        @if ($message = session('warning'))
            <div class="alert alert-warning">{{ $message }}</div>
        @endif

        @if ($message = session('info'))
            <div class="alert alert-info">{{ $message }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">Il y a des erreurs dans le formulaire suivant.</div>
        @endif
    </div>
</div>
