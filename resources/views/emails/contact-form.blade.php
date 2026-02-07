<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 40px;">
                            {{-- Header --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-bottom: 2px solid #e4e4e7; padding-bottom: 20px; margin-bottom: 30px;">
                                        <h1 style="margin: 0; color: #27272a; font-size: 24px; font-weight: 600;">
                                            New Contact Form Submission
                                        </h1>
                                        <p style="margin: 8px 0 0 0; color: #71717a; font-size: 14px;">
                                            Received on {{ $submission->created_at->format('F j, Y \a\t g:i A') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Submission Details --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td>
                                        {{-- Name --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                            <tr>
                                                <td>
                                                    <p style="margin: 0 0 6px 0; color: #71717a; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Name</p>
                                                    <p style="margin: 0; color: #27272a; font-size: 16px; font-weight: 500;">{{ $submission->name }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Email --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                            <tr>
                                                <td>
                                                    <p style="margin: 0 0 6px 0; color: #71717a; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Email</p>
                                                    <p style="margin: 0; color: #27272a; font-size: 16px; font-weight: 500;">
                                                        <a href="mailto:{{ $submission->email }}" style="color: #4f46e5; text-decoration: none;">{{ $submission->email }}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Subject (if provided) --}}
                                        @if($submission->subject)
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                            <tr>
                                                <td>
                                                    <p style="margin: 0 0 6px 0; color: #71717a; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Subject</p>
                                                    <p style="margin: 0; color: #27272a; font-size: 16px; font-weight: 500;">{{ $submission->subject }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        {{-- Message --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                            <tr>
                                                <td>
                                                    <p style="margin: 0 0 6px 0; color: #71717a; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Message</p>
                                                    <div style="margin: 0; color: #27272a; font-size: 16px; line-height: 1.6; background-color: #f4f4f5; padding: 16px; border-radius: 6px; border-left: 4px solid #4f46e5;">
                                                        {!! nl2br(e($submission->message)) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Reply Information --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e4e4e7;">
                                <tr>
                                    <td style="background-color: #eff6ff; padding: 16px; border-radius: 6px; border-left: 4px solid #4f46e5;">
                                        <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 1.5;">
                                            <strong>Reply To:</strong> You can reply directly to this email to respond to {{ $submission->name }} at {{ $submission->email }}.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Footer --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td style="border-top: 1px solid #e4e4e7; padding-top: 20px; text-align: center;">
                                        <p style="margin: 0; color: #a1a1aa; font-size: 12px;">
                                            This message was sent from your website's contact form.
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
