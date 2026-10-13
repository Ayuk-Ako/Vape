<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

require_once BASE_DIR . '/app/lib/PHPMailer/Exception.php';
require_once BASE_DIR . '/app/lib/PHPMailer/PHPMailer.php';
require_once BASE_DIR . '/app/lib/PHPMailer/SMTP.php';

/**
 * Email sender. Uses SMTP when configured in Admin > Settings, otherwise PHP mail().
 * Failures never break the customer flow; they are logged to storage/mail.log.
 */
class Mailer
{
    public static function send(string $to, string $subject, string $html, ?string $replyTo = null): bool
    {
        try {
            $m = new PHPMailer(true);
            $m->CharSet = 'UTF-8';
            $fromEmail = setting('mail_from_email', setting('store_email', 'no-reply@localhost'));
            $fromName = setting('mail_from_name', setting('store_name', 'Store'));

            if (setting('smtp_host')) {
                $m->isSMTP();
                $m->Host = setting('smtp_host');
                $m->Port = (int)setting('smtp_port', '587');
                $m->SMTPAuth = setting('smtp_user') !== '';
                $m->Username = setting('smtp_user');
                $m->Password = setting('smtp_pass');
                $enc = setting('smtp_encryption', 'tls');
                if ($enc === 'ssl') $m->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                elseif ($enc === 'tls') $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                else { $m->SMTPSecure = ''; $m->SMTPAutoTLS = false; }
                $m->Timeout = 12;
            } else {
                $m->isMail();
            }
            $m->setFrom($fromEmail, $fromName);
            $m->addAddress($to);
            if ($replyTo) $m->addReplyTo($replyTo);
            $m->isHTML(true);
            $m->Subject = $subject;
            $m->Body = $html;
            $m->AltBody = trim(html_entity_decode(strip_tags(preg_replace('#<br\s*/?>|</p>|</tr>#i', "\n", $html))));
            $m->send();
            return true;
        } catch (MailException $e) {
            self::log('Mail to ' . $to . ' failed: ' . $e->getMessage());
        } catch (Throwable $e) {
            self::log('Mail error: ' . $e->getMessage());
        }
        return false;
    }

    private static function log(string $line): void
    {
        @file_put_contents(BASE_DIR . '/storage/mail.log', '[' . date('Y-m-d H:i:s') . '] ' . $line . PHP_EOL, FILE_APPEND);
    }

    /** Render an email template from app/views/emails with the shared wrapper. */
    public static function render(string $template, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include BASE_DIR . '/app/views/emails/' . $template . '.php';
        $content = ob_get_clean();
        ob_start();
        include BASE_DIR . '/app/views/emails/_wrapper.php';
        return ob_get_clean();
    }
}
