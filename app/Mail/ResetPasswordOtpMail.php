<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otpCode;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otpCode)
    {
        $this->otpCode = $otpCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رمز التحقق لإعادة تعيين كلمة المرور',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    /**
     * Build the HTML body of the email inline (no Blade view needed).
     */
    private function buildHtml(): string
    {
        $code = htmlspecialchars($this->otpCode);
        $appName = config('app.name', 'Food App');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="ar" dir="rtl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>رمز التحقق</title>
        </head>
        <body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f4;padding:40px 0;">
                <tr>
                    <td align="center">
                        <table width="560" cellpadding="0" cellspacing="0" border="0"
                               style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.08);">

                            <!-- Header -->
                            <tr>
                                <td align="center" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:36px 24px;">
                                    <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:1px;">
                                        🔐 {$appName}
                                    </h1>
                                    <p style="margin:8px 0 0;color:#c7d2fe;font-size:14px;">إعادة تعيين كلمة المرور</p>
                                </td>
                            </tr>

                            <!-- Body -->
                            <tr>
                                <td style="padding:36px 40px;text-align:center;">
                                    <p style="margin:0 0 8px;font-size:16px;color:#374151;">مرحباً،</p>
                                    <p style="margin:0 0 28px;font-size:15px;color:#6b7280;line-height:1.6;">
                                        تلقينا طلباً لإعادة تعيين كلمة المرور لحسابك.<br>
                                        استخدم الرمز التالي للمتابعة. <strong>صالح لمدة 10 دقائق فقط.</strong>
                                    </p>

                                    <!-- OTP Code Box -->
                                    <div style="display:inline-block;background:#f5f3ff;border:2px dashed #7c3aed;border-radius:12px;padding:20px 48px;margin-bottom:28px;">
                                        <span style="font-size:48px;font-weight:800;letter-spacing:12px;color:#4f46e5;font-family:monospace;">
                                            {$code}
                                        </span>
                                    </div>

                                    <p style="margin:0;font-size:13px;color:#9ca3af;">
                                        إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة بأمان.
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:16px 40px;text-align:center;">
                                    <p style="margin:0;font-size:12px;color:#9ca3af;">
                                        © {$appName} — جميع الحقوق محفوظة
                                    </p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
