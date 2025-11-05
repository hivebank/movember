<?php

namespace FormFlow\Services;

class EmailService
{
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUser;
    private string $smtpPassword;
    private string $fromEmail;

    public function __construct()
    {
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? 'localhost';
        $this->smtpPort = (int)($_ENV['SMTP_PORT'] ?? 25);
        $this->smtpUser = $_ENV['SMTP_USER'] ?? '';
        $this->smtpPassword = $_ENV['SMTP_PASSWORD'] ?? '';
        $this->fromEmail = $_ENV['SMTP_FROM'] ?? 'noreply@formflow.com';
    }

    public function sendFormSubmissionNotification(
        string $to,
        string $formTitle,
        array $answers
    ): bool {
        $subject = "New submission for: {$formTitle}";

        $body = "<h2>New Form Submission</h2>";
        $body .= "<h3>Form: {$formTitle}</h3>";
        $body .= "<table border='1' cellpadding='10'>";

        foreach ($answers as $answer) {
            $body .= "<tr>";
            $body .= "<td><strong>" . htmlspecialchars($answer['label']) . "</strong></td>";
            $body .= "<td>" . htmlspecialchars($this->formatValue($answer['value'])) . "</td>";
            $body .= "</tr>";
        }

        $body .= "</table>";

        return $this->send($to, $subject, $body);
    }

    public function sendWelcomeEmail(string $to, string $name): bool
    {
        $subject = "Welcome to FormFlow!";

        $body = "<h2>Welcome to FormFlow, {$name}!</h2>";
        $body .= "<p>Thank you for signing up. You can now start creating beautiful forms.</p>";
        $body .= "<p>Your free plan includes:</p>";
        $body .= "<ul>";
        $body .= "<li>3 forms</li>";
        $body .= "<li>100 responses per month</li>";
        $body .= "</ul>";
        $body .= "<p>Visit your dashboard to get started!</p>";

        return $this->send($to, $subject, $body);
    }

    public function sendSubscriptionConfirmation(string $to, string $planName): bool
    {
        $subject = "Subscription Confirmed - {$planName} Plan";

        $body = "<h2>Subscription Confirmed!</h2>";
        $body .= "<p>Your {$planName} plan is now active.</p>";
        $body .= "<p>Thank you for upgrading your FormFlow account!</p>";

        return $this->send($to, $subject, $body);
    }

    private function send(string $to, string $subject, string $body): bool
    {
        // For development/testing, just log the email
        if ($_ENV['APP_ENV'] === 'development') {
            error_log("=== EMAIL ===");
            error_log("To: {$to}");
            error_log("Subject: {$subject}");
            error_log("Body: {$body}");
            error_log("=============");
            return true;
        }

        // In production, implement actual SMTP sending
        // You can use PHPMailer or SwiftMailer library
        // For now, this is a placeholder

        $headers = [
            'From' => $this->fromEmail,
            'Content-Type' => 'text/html; charset=UTF-8',
        ];

        $headerString = '';
        foreach ($headers as $key => $value) {
            $headerString .= "{$key}: {$value}\r\n";
        }

        return mail($to, $subject, $body, $headerString);
    }

    private function formatValue($value): string
    {
        if (is_array($value)) {
            return implode(', ', $value);
        }
        return (string)$value;
    }
}
