@if(modstart_config('ErrorPage_ErrorPage500'))
    {!! modstart_config('ErrorPage_ErrorPage500') !!}
@else
    @include('modstart::core.msg.500')
@endif
