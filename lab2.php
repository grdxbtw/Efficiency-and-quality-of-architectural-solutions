<?php

// Спільний інтерфейс для соціальних мереж
interface SocialPlatform
{
    public function authorize(): void;
    public function shareContent(string $text): void;
    public function disconnect(): void;
}

// Продукт: Facebook
class FacebookApp implements SocialPlatform
{
    public function __construct(private string $userLogin, private string $userPass) {}

    public function authorize(): void
    {
        // Логіка входу в Facebook
    }

    public function shareContent(string $text): void
    {
        echo "[Facebook API] Користувач {$this->userLogin} запостив: {$text}" . PHP_EOL;
    }

    public function disconnect(): void
    {
        // Логіка виходу
    }
}

// Продукт: LinkedIn
class LinkedInApp implements SocialPlatform
{
    public function __construct(private string $userEmail, private string $userPass) {}

    public function authorize(): void
    {
        // Логіка входу в LinkedIn
    }

    public function shareContent(string $text): void
    {
        echo "[LinkedIn API] Користувач {$this->userEmail} поділився статтею: {$text}" . PHP_EOL;
    }

    public function disconnect(): void
    {
        // Логіка виходу
    }
}

// Абстрактний клас-Творець
abstract class ContentPublisher
{
    // Фабричний метод
    abstract protected function initNetwork(): SocialPlatform;

    // Основна бізнес-логіка
    public function publish(string $content): void
    {
        $network = $this->initNetwork();
        $network->authorize();
        $network->shareContent($content);
        $network->disconnect();
    }
}

// Конкретний творець для Facebook
class FacebookPublisher extends ContentPublisher
{
    public function __construct(private string $login, private string $password) {}

    protected function initNetwork(): SocialPlatform
    {
        return new FacebookApp($this->login, $this->password);
    }
}

// Конкретний творець для LinkedIn
class LinkedInPublisher extends ContentPublisher
{
    public function __construct(private string $email, private string $password) {}

    protected function initNetwork(): SocialPlatform
    {
        return new LinkedInApp($this->email, $this->password);
    }
}

$fbSender = new FacebookPublisher("my_login", "12345");
$fbSender->publish("Привіт всім у Фейсбуці!");

$liSender = new LinkedInPublisher("work@email.com", "qwerty");
$liSender->publish("Шукаю нові проєкти.");
