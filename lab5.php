<?php

// Клас товару
class ProductItem
{
    public function __construct(
        public readonly int $identifier,
        public readonly string $headline,
        public readonly string $details,
        public readonly string $pictureUrl
    ) {}
}

// Інтерфейс для форматування вмісту сторінки
interface FormatEngine
{
    public function buildHeading(string $text): string;
    public function buildParagraph(string $text): string;
    public function buildPicture(string $url): string;
    public function buildMetaId(int $id): string;
}

// Клас для форматування вмісту сторінки у HTML
class HtmlEngine implements FormatEngine
{
    public function buildHeading(string $text): string
    {
        return "<h1>{$text}</h1>";
    }
    public function buildParagraph(string $text): string
    {
        return "<p>{$text}</p>";
    }
    public function buildPicture(string $url): string
    {
        return "<img src='{$url}' alt=''/>";
    }
    public function buildMetaId(int $id): string
    {
        return "<div class='id'>#{$id}</div>";
    }
}

// Клас для форматування вмісту сторінки у JSON
class JsonEngine implements FormatEngine
{
    public function buildHeading(string $text): string
    {
        return '"headline": "' . $text . '"';
    }
    public function buildParagraph(string $text): string
    {
        return '"content": "' . $text . '"';
    }
    public function buildPicture(string $url): string
    {
        return '"media": "' . $url . '"';
    }
    public function buildMetaId(int $id): string
    {
        return '"ref_id": ' . $id;
    }
}

// Клас для форматування вмісту сторінки у XML
class XmlEngine implements FormatEngine
{
    public function buildHeading(string $text): string
    {
        return "<headline>{$text}</headline>";
    }
    public function buildParagraph(string $text): string
    {
        return "<content>{$text}</content>";
    }
    public function buildPicture(string $url): string
    {
        return "<media>{$url}</media>";
    }
    public function buildMetaId(int $id): string
    {
        return "<ref_id>{$id}</ref_id>";
    }
}

// Абстрактний клас сторінки
abstract class WebPage
{
    // Міст зв'язується через конструктор
    public function __construct(protected FormatEngine $engine) {}

    public function swapEngine(FormatEngine $newEngine): void
    {
        $this->engine = $newEngine;
    }

    abstract public function render(): string;
}

// Проста сторінка
class BasicPage extends WebPage
{
    public function __construct(
        FormatEngine $engine,
        private string $title,
        private string $body
    ) {
        parent::__construct($engine);
    }

    public function render(): string
    {
        $output = [];
        $output[] = $this->engine->buildHeading($this->title);
        $output[] = $this->engine->buildParagraph($this->body);
        return implode("\n", $output);
    }
}

// Сторінка товару
class CatalogItemPage extends WebPage
{
    public function __construct(
        FormatEngine $engine,
        private ProductItem $product
    ) {
        parent::__construct($engine);
    }

    public function render(): string
    {
        $output = [];
        $output[] = $this->engine->buildMetaId($this->product->identifier);
        $output[] = $this->engine->buildHeading($this->product->headline);
        $output[] = $this->engine->buildParagraph($this->product->details);
        $output[] = $this->engine->buildPicture($this->product->pictureUrl);
        return implode("\n", $output);
    }
}

// Клієнтський код
$htmlFormat = new HtmlEngine();
$jsonFormat = new JsonEngine();
$xmlFormat  = new XmlEngine();

// Рендеринг BasicPage у форматі HTML та JSON
$infoPage = new BasicPage($htmlFormat, "Контакти", "Номер телефону: 123-456");
echo "HTML Output:\n" . $infoPage->render() . "\n\n";

$infoPage->swapEngine($jsonFormat);
echo "JSON Output:\n{\n" . $infoPage->render() . "\n}\n\n";

// Рендеринг CatalogItemPage у форматі XML
$item = new ProductItem(99, "Смартфон", "Флагман 2026 року", "phone.jpg");
$productPage = new CatalogItemPage($xmlFormat, $item);

echo "XML Output:\n<item>\n" . $productPage->render() . "\n</item>\n";
