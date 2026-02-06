<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title>{{ $meta['title'] }}</title>
    <subtitle>{{ $meta['description'] }}</subtitle>
    <link href="{{ url($meta['url']) }}" rel="self" />
    <link href="{{ url('/') }}" />
    <id>{{ url($meta['url']) }}</id>
    <updated>{{ $meta['updated'] ?? now()->toAtomString() }}</updated>
    <author>
        <name>{{ config('app.name', 'Blog Author') }}</name>
        <email>{{ config('mail.from.address', 'author@example.com') }}</email>
    </author>
    @foreach($items as $item)
    <entry>
        <title>{{ $item->title }}</title>
        <link href="{{ $item->link }}" />
        <id>{{ $item->id }}</id>
        <updated>{{ $item->updated->toAtomString() }}</updated>
        <summary>{{ $item->summary }}</summary>
        <author>
            <name>{{ $item->authorName }}</name>
            <email>{{ $item->authorEmail }}</email>
        </author>
    </entry>
    @endforeach
</feed>
