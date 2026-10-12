<?php

// Спільний інтерфейс для сповіщень
interface Notification
{
    public function send(string $title, string $message);
}

class EmailNotification implements Notification
{
    private $adminEmail;
    public function __construct(string $adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }
    public function send(string $title, string $message): void
    {
        // Логіка відправки email
        echo "Sent email with title '$title' to '{$this->adminEmail}' that says '$message'." . PHP_EOL;
    }
}

// Сторонній клас Slack
class SlackMessenger
{
    public function __construct(private string $login, private string $apiKey) {}

    public function login(): void
    {
        // Логіка підключення
    }

    public function dispatchMessage(string $channelId, string $content): void
    {
        echo "Slack [Channel: {$channelId}]: {$content}" . PHP_EOL;
    }
}

// Сторонній клас SMS
class SmsGateway
{
    public function dispatchSms(string $targetPhone, string $senderName, string $smsText): void
    {
        echo "SMS to {$targetPhone} (from {$senderName}): {$smsText}" . PHP_EOL;
    }
}

// Адаптер для Slack
class SlackAdapter implements Notification
{
    public function __construct(private SlackMessenger $slack, private string $targetChatId) {}

    public function send(string $title, string $message): void
    {
        $this->slack->login();
        $formatted = "[{$title}] => {$message}";
        $this->slack->dispatchMessage($this->targetChatId, $formatted);
    }
}

// Адаптер для SMS
class SmsAdapter implements Notification
{
    public function __construct(private SmsGateway $gateway, private string $phone,  private string $sender) {}

    public function send(string $title, string $message): void
    {
        $formatted = "{$title}: {$message}";
        $this->gateway->dispatchSms($this->phone, $this->sender, $formatted);
    }
}

// Клієнтський код
$emailAlert = new EmailNotification("admin@test.com");
$emailAlert->send("Alert", "Something went wrong");

$slackApi = new SlackMessenger("user1", "key123");
$slackAlert = new SlackAdapter($slackApi, "general_chat");
$slackAlert->send("System", "CPU usage 99%");

$smsApi = new SmsGateway();
$smsAlert = new SmsAdapter($smsApi, "+380000000000", "InfoBot");
$smsAlert->send("Warning", "Server is down");
