@if(config('services.umami.enabled') && config('services.umami.host') && config('services.umami.website_id') && app()->environment('production'))
    <script async defer
        src="{{ config('services.umami.host') }}/{{ config('services.umami.script_name', 'script.js') }}"
        data-website-id="{{ config('services.umami.website_id') }}"
        data-do-not-track="true"
        data-cache="true">
    </script>
@endif