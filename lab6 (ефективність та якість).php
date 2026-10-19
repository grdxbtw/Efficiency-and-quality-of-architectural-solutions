<?php

// Інтерфейс Downloader визначає метод завантаження ресурсу
interface Downloader
{
    public function download(string $resourceUrl): string;
}

// Клас SimpleDownloader реалізує інтерфейс Downloader і виконує завантаження ресурсу
class SimpleDownloader implements Downloader
{
    public function download(string $resourceUrl): string
    {
        // Ресурсоємна операція завантаження
        echo "[З'єднання встановлено] Завантажуємо {$resourceUrl}...\n";
        return "DATA_PAYLOAD_FOR_{$resourceUrl}";
    }
}

// Клас CachedDownloaderProxy реалізує інтерфейс Downloader і додає кешування до завантаження ресурсів
class CachedDownloaderProxy implements Downloader
{
    /** @var array<string, string> */
    private array $downloadHistory = [];

    // Використовуємо агрегацію: реальний об'єкт передається ззовні
    public function __construct(
        private Downloader $baseDownloader
    ) {}

    public function download(string $resourceUrl): string
    {
        // Якщо ресурс вже завантажувався, віддаємо його з масиву-кешу
        if (array_key_exists($resourceUrl, $this->downloadHistory)) {
            echo "[Локальний Кеш] Знайдено: {$resourceUrl}\n";
            return $this->downloadHistory[$resourceUrl];
        }

        // Якщо ні - викликаємо оригінальний об'єкт
        $fetchedData = $this->baseDownloader->download($resourceUrl);

        // Зберігаємо в кеш
        $this->downloadHistory[$resourceUrl] = $fetchedData;

        return $fetchedData;
    }
}

// Клієнтський код, який використовує Downloader
function fetchAndDisplay(Downloader $client, string $url): void
{
    echo "--- Запит ресурсу: {$url} ---\n";
    $result = $client->download($url);
    echo "Отримано: {$result}\n\n";
}

// Ініціалізуємо базовий клас та обгортаємо його в Замісник
$realService = new SimpleDownloader();
$proxyService = new CachedDownloaderProxy($realService);

// Перший виклик (йде через оригінальний клас)
fetchAndDisplay($proxyService, "api/v1/users.json");

// Другий виклик (відпрацьовує кеш)
fetchAndDisplay($proxyService, "api/v1/users.json");

// Завантаження іншого файлу
fetchAndDisplay($proxyService, "assets/logo.svg");

// Повторне завантаження іншого файлу
fetchAndDisplay($proxyService, "assets/logo.svg");
