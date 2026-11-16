<?php

// Абстрактний базовий клас
abstract class ApiResourceUpdater
{

    // Шаблонний метод, який розбиває алгоритм на кроки
    final public function processUpdate(int $resourceId, array $payload): array
    {
        $object = $this->fetchResource($resourceId);

        $this->beforeValidationHook($payload); // Хук перед валідацією

        if (!$this->performValidation($payload)) {
            $this->handleValidationError(); // Хук для обробки помилок валідації
            return ['code' => 400, 'status' => 'Error'];
        }

        $this->persistChanges($object, $payload);
        return $this->buildResponse($object);
    }

    // Абстрактні кроки алгоритму
    abstract protected function fetchResource(int $id);
    abstract protected function persistChanges($object, array $payload): void;

    // Кроки з типовою реалізацією
    protected function performValidation(array $payload): bool
    {
        echo "[ApiResourceUpdater] Стандартна валідація полів...\n";
        return true;
    }

    protected function buildResponse($object): array
    {
        // Формування відповіді - код відповіді та статус
        return ['code' => 200, 'status' => 'Success'];
    }

    // Хуки (пусті методи за замовчуванням)
    protected function beforeValidationHook(array $payload): void {}
    protected function handleValidationError(): void {}
}

// Конкретні класи сутностей
// Сутність Товар
class ProductRestUpdater extends ApiResourceUpdater
{
    protected function fetchResource(int $id)
    {
        return "ProductObj#{$id}";
    }

    protected function performValidation(array $payload): bool
    {
        // Імітація провалу валідації
        return false;
    }

    protected function persistChanges($object, array $payload): void
    {
        echo "[Product] Збереження в базу даних...\n";
    }

    // Якщо товар не пройшов валідацію, адміністратор має отримати сповіщення у мессенджер
    protected function handleValidationError(): void
    {
        echo "[Messenger API] УВАГА АДМІНУ: Спроба оновлення товару з невалідними даними!\n";
    }
}

// Сутність Користувач
class UserRestUpdater extends ApiResourceUpdater
{
    protected function fetchResource(int $id)
    {
        return "UserObj#{$id}";
    }

    // У Користувача заборонено змінювати значення у полі email
    protected function performValidation(array $payload): bool
    {
        if (array_key_exists('email', $payload)) {
            echo "[User] Відмова: поле 'email' захищене від змін через це API.\n";
            return false;
        }
        return parent::performValidation($payload);
    }

    protected function persistChanges($object, array $payload): void
    {
        echo "[User] Оновлення профілю користувача...\n";
    }
}

// Сутність Замовлення
class OrderRestUpdater extends ApiResourceUpdater
{
    protected function fetchResource(int $id)
    {
        return ['id' => $id, 'items' => 'Pizza', 'total' => 300]; // Імітація об'єкта
    }

    protected function persistChanges($object, array $payload): void
    {
        echo "[Order] Фіксація змін замовлення...\n";
    }

    // Для сутності Замовлення необхідно повертати JSON-подання сутності
    protected function buildResponse($object): array
    {
        $response = parent::buildResponse($object);
        $response['json_data'] = json_encode($object);
        return $response;
    }
}

// Клієнтський код
$inputData = ['title' => 'Оновлена назва', 'email' => 'new@mail.com'];

echo "Товар\n";
$productHandler = new ProductRestUpdater();
$productHandler->processUpdate(10, $inputData);

echo "\nКористувач\n";
$userHandler = new UserRestUpdater();
$userHandler->processUpdate(42, $inputData);

echo "\nЗамовлення\n";
$orderHandler = new OrderRestUpdater();
$response = $orderHandler->processUpdate(99, ['status' => 'Paid']);
echo "Результат Замовлення: \n";
print_r($response);
