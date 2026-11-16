<?php

// Інтерфейс Стратегії
interface DeliveryPricingStrategy
{
    public function getPrice(float $cartTotal, float $distanceInKm): float;
}

// Конкретні стратегії
class ClientPickupStrategy implements DeliveryPricingStrategy
{
    public function getPrice(float $cartTotal, float $distanceInKm): float
    {
        // Самовивіз не коштує нічого
        return 0.0;
    }
}

class ThirdPartyCourierStrategy implements DeliveryPricingStrategy
{
    public function getPrice(float $cartTotal, float $distanceInKm): float
    {
        // Стороння служба бере базову ставку плюс за кілометраж
        return 60.0 + ($distanceInKm * 12.5);
    }
}

class InHouseCourierStrategy implements DeliveryPricingStrategy
{
    public function getPrice(float $cartTotal, float $distanceInKm): float
    {
        // Власна служба (може мати безкоштовну доставку для великих замовлень)
        if ($cartTotal > 1000.0) {
            return 0.0;
        }
        return 40.0 + ($distanceInKm * 8.0);
    }
}

// Контекст для використання стратегії
class FoodCartCheckout
{
    public function __construct(
        private DeliveryPricingStrategy $pricingStrategy
    ) {}

    // Метод для "гарячої заміни" алгоритму
    public function changeDeliveryMethod(DeliveryPricingStrategy $newStrategy): void
    {
        $this->pricingStrategy = $newStrategy;
    }

    public function calculateFinalDeliveryCost(float $cartTotal, float $distance): float
    {
        // Делегуємо роботу інкапсульованому алгоритму
        return $this->pricingStrategy->getPrice($cartTotal, $distance);
    }
}

// Клієнтський код
$orderTotal = 850.00;
$distance = 7.2;

// Спочатку користувач обрав доставку нашими кур'єрами
$checkout = new FoodCartCheckout(new InHouseCourierStrategy());
echo "Вартість власної доставки: " . $checkout->calculateFinalDeliveryCost($orderTotal, $distance) . " ₴\n";

// Користувач вирішив забрати сам
$checkout->changeDeliveryMethod(new ClientPickupStrategy());
echo "Вартість самовивозу: " . $checkout->calculateFinalDeliveryCost($orderTotal, $distance) . " ₴\n";

// Користувач передумав і обрав Уклон/Глово (зовнішня служба)
$checkout->changeDeliveryMethod(new ThirdPartyCourierStrategy());
echo "Вартість сторонньої служби: " . $checkout->calculateFinalDeliveryCost($orderTotal, $distance) . " ₴\n";
