<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Membership QR Code - EZ Fitness</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header with Logo -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); padding: 40px 30px; text-align: center;">
                            <img src="{{ $message->embed(public_path('logo_image/ez_fitness_gym_logo.png')) }}" alt="EZ Fitness Logo" style="width: 80px; height: 80px; margin-bottom: 15px;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                <span style="color: #ff6b6b;">EZ</span> FITNESS GYM
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 10px 0; color: #1f2937; font-size: 24px; font-weight: 600;">
                                Welcome, {{ $name }}! 🎉
                            </h2>
                            
                            <p style="margin: 0 0 25px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Your membership has been approved! Here are your details:
                            </p>

                            <!-- Membership Details -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9fafb; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Membership Plan</p>
                                        <p style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $plan }}</p>
                                        
                                        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Subscription</p>
                                        <p style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $subscription }}</p>
                                        
                                        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Valid Until</p>
                                        <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $end_date }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- QR Code Section -->
                           <!-- Replace the QR Code Section in your email template with this: -->
<div style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 12px; padding: 30px; text-align: center; margin-bottom: 20px;">
    <p style="margin: 0 0 20px 0; color: #1f2937; font-size: 16px; font-weight: 600;">
        Your Membership QR Code
    </p>
    <img src="data:image/png;base64,{{ $qr_code_base64 }}" alt="Membership QR Code" style="width: 200px; height: 200px; border: 3px solid #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <p style="margin: 20px 0 0 0; color: #6b7280; font-size: 13px; line-height: 1.5;">
        Save this QR code on your phone and show it at the gym entrance for check-in.
    </p>
</div>

                            <!-- Important Notice -->
                            <div style="background-color: #fef2f2; border-left: 4px solid #ff6b6b; padding: 15px; border-radius: 4px;">
                                <p style="margin: 0; color: #991b1b; font-size: 13px; line-height: 1.5;">
                                    <strong>Important:</strong> Keep your QR code secure and don't share it with anyone. This is your personal membership pass.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 12px;">
                                Account Email: {{ $email }}
                            </p>
                            <p style="margin: 0; color: #6b7280; font-size: 12px;">
                                © {{ date('Y') }} EZ Fitness Gym. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>