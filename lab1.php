<?php

// Загальний інтерфейс для сховищ
interface StorageInterface
{
    public function uploadData(string $userIdentifier, string $file, string $content): void;
    public function downloadData(string $userIdentifier, string $file): string;
}

// Реалізація локального сховища (Singleton)
class LocalDriveStorage implements StorageInterface
{
    private static ?LocalDriveStorage $instance = null;

    // Блокуємо створення об'єкта ззовні
    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {}

    public static function getInstance(): LocalDriveStorage
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function uploadData(string $userIdentifier, string $file, string $content): void
    {
        // Імітація запису на жорсткий диск
    }

    public function downloadData(string $userIdentifier, string $file): string
    {
        return "Вміст файлу з локального диска";
    }
}

// Реалізація сховища AWS S3 (Singleton)
class CloudS3Storage implements StorageInterface
{
    private static ?CloudS3Storage $instance = null;

    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {}

    public static function getInstance(): CloudS3Storage
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function uploadData(string $userIdentifier, string $file, string $content): void
    {
        // Імітація відправки файлу в хмару Amazon
    }

    public function downloadData(string $userIdentifier, string $file): string
    {
        return "Вміст файлу з бакета S3";
    }
}

// Клас клієнта системи
class SystemUser
{
    public function __construct(private string $identifier, private StorageInterface $activeStorage) {}

    public function changeStorage(StorageInterface $newStorage): void
    {
        $this->activeStorage = $newStorage;
    }

    public function save(string $filename, string $data): void
    {
        $this->activeStorage->uploadData($this->identifier, $filename, $data);
    }

    public function fetch(string $filename): string
    {
        return $this->activeStorage->downloadData($this->identifier, $filename);
    }
}

$localDisk = LocalDriveStorage::getInstance();
$cloudS3 = CloudS3Storage::getInstance();

$student = new SystemUser("student_01", $localDisk);
$student->save("report.pdf", "Дані звіту...");

$student->changeStorage($cloudS3);
$student->save("backup.zip", "Архів...");
