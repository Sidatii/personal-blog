<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Your Newsletter Subscription</title>
</head>
<body style="margin: 0; padding: 0; background-color: #191724; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #191724;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #1f1d2e; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                    <tr>
                        <td style="padding: 40px;">
                            {{-- Header --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-bottom: 2px solid #31748f; padding-bottom: 20px; margin-bottom: 30px;">
                                        <h1 style="margin: 0; color: #e0def4; font-size: 24px; font-weight: 600;">
                                            Confirm Your Newsletter Subscription
                                        </h1>
                                        <p style="margin: 8px 0 0 0; color: #908caa; font-size: 14px;">
                                            One click away from staying updated
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Greeting --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 20px 0; color: #e0def4; font-size: 16px; line-height: 1.6;">
                                            Hi {{ $subscriber->name ?? 'there' }},
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #e0def4; font-size: 16px; line-height: 1.6;">
                                            Thanks for subscribing to our newsletter! Click the button below to confirm your subscription and start receiving updates.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px; margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $confirmUrl }}" style="display: inline-block; padding: 14px 32px; background-color: #eb6f92; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: 600; text-align: center;">
                                            Confirm Subscription
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Alternative Link --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; color: #908caa; font-size: 14px; line-height: 1.6; text-align: center;">
                                            If the button doesn't work, copy and paste this link into your browser:
                                        </p>
                                        <p style="margin: 10px 0 0 0; color: #31748f; font-size: 14px; line-height: 1.6; text-align: center; word-break: break-all;">
                                            {{ $confirmUrl }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Footer Note --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #26233a;">
                                <tr>
                                    <td style="background-color: #26233a; padding: 16px; border-radius: 6px; border-left: 4px solid #f6c177;">
                                        <p style="margin: 0; color: #e0def4; font-size: 14px; line-height: 1.5;">
                                            <strong>Didn't sign up?</strong> If you didn't request this subscription, you can safely ignore this email.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Unsubscribe Link --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td style="border-top: 1px solid #26233a; padding-top: 20px; text-align: center;">
                                        <p style="margin: 0; color: #6e6a86; font-size: 12px;">
                                            Don't want to receive emails from us?
                                            <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}" style="color: #908caa; text-decoration: underline;">Unsubscribe here</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
