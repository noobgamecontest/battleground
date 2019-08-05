@inject('messaging', 'App\Services\Message\MessageService')

@if($messaging->hasOne())
    <div class="alert alert-{{ $messaging->get()->getType() }}" role="alert">
        {{ $messaging->get()->getContent() }}
    </div>
@endif
