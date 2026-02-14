@if(config('services.umami.enabled') && config('services.umami.website_id') && config('services.umami.url'))
<script
    defer
    src="{{ rtrim(config('services.umami.url'), '/') }}/script.js"
    data-website-id="{{ config('services.umami.website_id') }}"
></script>
@endif
