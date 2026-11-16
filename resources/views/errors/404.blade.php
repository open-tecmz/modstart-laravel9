@if(modstart_config('ErrorPage_ErrorPage404'))
    {!! modstart_config('ErrorPage_ErrorPage404') !!}
@else
    @include('modstart::core.msg.404')
@endif
