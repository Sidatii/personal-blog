<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Your Newsletter Subscription</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f0eb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f5f0eb;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%;">

                    {{-- Logo header --}}
                    <tr>
                        <td align="center" style="padding-bottom: 32px;">
                            <a href="{{ url('/') }}" style="text-decoration: none;">
                                <img src="{{ url('/oob-black.png') }}" alt="{{ config('app.name') }}" width="240" style="display: block; margin: 0 auto; max-width: 100%;">
                            </a>
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td style="background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e8e0d8;">

                            {{-- Accent bar --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background: linear-gradient(90deg, #f9c97c 0%, #d4b5ea 100%); height: 4px; font-size: 0; line-height: 0;">&nbsp;</td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding: 40px;">

                                        {{-- Header --}}
                                        <h1 style="margin: 0 0 8px 0; color: #1f1d2e; font-size: 24px; font-weight: 700;">
                                            Confirm Your Subscription
                                        </h1>
                                        <p style="margin: 0 0 32px 0; color: #9893a5; font-size: 14px;">
                                            One click away from staying updated
                                        </p>

                                        {{-- Divider --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 32px;">
                                            <tr>
                                                <td style="border-top: 1px solid #e8e0d8; font-size: 0; line-height: 0;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        {{-- Greeting --}}
                                        <p style="margin: 0 0 16px 0; color: #575279; font-size: 16px; line-height: 1.6;">
                                            Hi {{ $subscriber->name ?? 'there' }},
                                        </p>
                                        <p style="margin: 0 0 32px 0; color: #575279; font-size: 16px; line-height: 1.6;">
                                            Thanks for subscribing! Click the button below to confirm your subscription and start receiving updates.
                                        </p>

                                        {{-- CTA Button --}}
                                        <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 32px;">
                                            <tr>
                                                <td style="border-radius: 8px; background-color: #f9c97c;">
                                                    <a href="{{ $confirmUrl }}" style="display: inline-block; padding: 14px 32px; color: #1f1d2e; text-decoration: none; font-size: 15px; font-weight: 700; border-radius: 8px;">
                                                        Confirm Subscription
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Alternative Link --}}
                                        <p style="margin: 0 0 8px 0; color: #9893a5; font-size: 13px; line-height: 1.6;">
                                            If the button doesn't work, copy and paste this link:
                                        </p>
                                        <p style="margin: 0 0 32px 0; color: #907aa9; font-size: 13px; line-height: 1.6; word-break: break-all;">
                                            {{ $confirmUrl }}
                                        </p>

                                        {{-- Footer Note --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="background-color: #faf4ed; padding: 16px; border-radius: 8px; border-left: 4px solid #f9c97c;">
                                                    <p style="margin: 0; color: #575279; font-size: 14px; line-height: 1.5;">
                                                        <strong>Didn't sign up?</strong> You can safely ignore this email.
                                                    </p>
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
                            <p style="margin: 0 0 8px 0; color: #9893a5; font-size: 12px; line-height: 1.6;">
                                You're receiving this because you subscribed to {{ config('app.name') }}.
                            </p>
                            <p style="margin: 0; color: #9893a5; font-size: 12px;">
                                <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}" style="color: #797593; text-decoration: underline;">Unsubscribe</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
