<nav class="breadcrumb-style-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url(getGuardFromURL(request(), false).'/dashboard') }}">Dashboard</a>
        </li>
        @php $link = ""; @endphp
        @for($i = 1; $i <= count(Request::segments()); $i++) @if($i < count(Request::segments()) & $i> 0)
            @php $link .= "/" . Request::segment($i); @endphp
            <li class="breadcrumb-item {{ checkRoute($link) }}">
                @if(checkRoute($link))
                <a href="{{ url($link) }}">
                    {{ ucwords(str_replace('_',' ',Request::segment($i))) }}
                </a>
                @else
                {{ ucwords(str_replace('_',' ',Request::segment($i))) }}
                @endif
            </li>
            @else
            <li class="breadcrumb-item active" aria-current="page">
                @if(Illuminate\Support\Str::isUuid(Request::segment($i)))
                {{ __('Edit') }}
                @else
                {{ ucwords(str_replace('_',' ',Request::segment($i))) }}
                @endif
            </li>
            @endif @endfor
    </ol>
</nav>