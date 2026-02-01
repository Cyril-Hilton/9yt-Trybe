@php
    $languages = config('services.ai.seo.languages', ['en']);
    $languages = array_values(array_filter(array_map(fn ($lang) => strtolower(trim((string) $lang)), $languages)));
    $currentUrl = url()->current();
@endphp

@if(count($languages) > 1)
    @foreach($languages as $lang)
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ request()->fullUrlWithQuery(['lang' => $lang]) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $currentUrl }}">
@endif
