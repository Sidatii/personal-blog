<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #100f14; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #100f14;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%;">

                    {{-- Logo header --}}
                    <tr>
                        <td align="center" style="padding-bottom: 32px;">
                            <a href="{{ url('/') }}" style="text-decoration: none;">
                                <img src="{{ url('/oob-white.png') }}" alt="{{ config('app.name') }}" width="480" style="display: block; margin: 0 auto; max-width: 100%;">
                            </a>
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td style="background-color: #1a1520; border-radius: 12px; overflow: hidden; border: 1px solid #221c2a;">

                            {{-- Accent bar --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background: linear-gradient(90deg, #4a8ca0 0%, #d4b5ea 100%); height: 4px; font-size: 0; line-height: 0;">&nbsp;</td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 40px;">

                                        {{-- Label --}}
                                        <p style="margin: 0 0 16px 0; color: #4a8ca0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;">
                                            New Post
                                        </p>

                                        {{-- Title --}}
                                        <h1 style="margin: 0 0 20px 0; color: #e8e4f0; font-size: 26px; font-weight: 700; line-height: 1.3;">
                                            {{ $post->title }}
                                        </h1>

                                        {{-- Divider --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                            <tr>
                                                <td style="border-top: 1px solid #221c2a; font-size: 0; line-height: 0;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        {{-- Excerpt --}}
                                        @if($post->excerpt)
                                        <p style="margin: 0 0 32px 0; color: #9d92b0; font-size: 16px; line-height: 1.7;">
                                            {{ $post->excerpt }}
                                        </p>
                                        @endif

                                        {{-- Meta --}}
                                        @if($post->published_at)
                                        <p style="margin: 0 0 32px 0; color: #857a96; font-size: 13px;">
                                            Published {{ $post->published_at->format('F j, Y') }}
                                            @if($post->category)
                                             &middot; {{ $post->category->name }}
                                            @endif
                                        </p>
                                        @endif

                                        {{-- CTA --}}
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="border-radius: 8px; background-color: #4a8ca0;">
                                                    <a href="{{ $postUrl }}" style="display: inline-block; padding: 14px 32px; color: #e8e4f0; text-decoration: none; font-size: 15px; font-weight: 600; border-radius: 8px;">
                                                        Read the full post &rarr;
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 32px 0 0 0; text-align: center;">
                            <p style="margin: 0 0 8px 0; color: #857a96; font-size: 12px; line-height: 1.6;">
                                You're receiving this because you subscribed to {{ config('app.name') }}.
                            </p>
                            <p style="margin: 0; color: #857a96; font-size: 12px;">
                                <a href="{{ $unsubscribeUrl }}" style="color: #9d92b0; text-decoration: underline;">Unsubscribe</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
