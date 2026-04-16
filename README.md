# esolutions-escpos

PHP client para impresión ESC/POS via [ApiPeruDevPrint](https://github.com/eriquegasparcarlos/apiperudevprint).

Reemplaza `mike42/escpos-php` con una API fluida que envía comandos a ApiPeruDevPrint, el servicio local de impresión.

## Instalación

```bash
composer require eriquegasparcarlos/esolutions-escpos
```

## Requisitos

- PHP 8.1+
- ext-json, ext-curl
- [ApiPeruDevPrint](https://github.com/eriquegasparcarlos/apiperudevprint) corriendo en `localhost:8443`

## Uso básico

```php
use ESolutions\Escpos\Document;
use ESolutions\Escpos\Printer;

$doc = Document::make()
    ->textCenter('MI EMPRESA SAC', bold: true, size: 2)
    ->textCenter('RUC: 20123456789')
    ->separator()
    ->row('Cliente:', 'Juan Pérez')
    ->row('Fecha:', '16/04/2026')
    ->separator()
    ->columns([
        ['text' => 'Producto A x2', 'width' => 28, 'align' => 'left'],
        ['text' => 'S/ 20.00',      'width' => 10, 'align' => 'right'],
    ])
    ->columns([
        ['text' => 'Producto B x1', 'width' => 28, 'align' => 'left'],
        ['text' => 'S/  5.00',      'width' => 10, 'align' => 'right'],
    ])
    ->separator()
    ->textRight('TOTAL: S/ 29.50', bold: true)
    ->feed(1)
    ->qr('https://cpe.sunat.gob.pe/...')
    ->cut();

Printer::create()
    ->to('TP-300')
    ->paper80mm()
    ->send($doc);
```

## Métodos del Document

| Método | Descripción |
|--------|-------------|
| `text($text, $align, $bold, $underline, $size, $font)` | Texto con formato |
| `textCenter($text, $bold, $size)` | Texto centrado |
| `textRight($text, $bold)` | Texto alineado a la derecha |
| `bold($text, $align, $size)` | Texto en negrita |
| `small($text, $align)` | Texto pequeño (font B) |
| `columns($columns)` | Columnas en una fila |
| `row($label, $value, $paperWidth)` | Fila etiqueta / valor |
| `separator()` | Línea separadora |
| `feed($lines)` | Avance de líneas |
| `qr($data, $size, $align)` | Código QR |
| `barcode($data, $type, $align)` | Código de barras |
| `imageBase64($base64, $width, $align)` | Imagen desde base64 |
| `imageFile($path, $width, $align)` | Imagen desde archivo |
| `cut()` | Corte de papel |
| `openDrawer()` | Abrir cajón de dinero |

## Métodos del Printer

```php
Printer::create()
    ->to('Nombre Impresora')   // nombre de la impresora
    ->paper58mm()              // 32 chars/línea
    ->paper80mm()              // 48 chars/línea (default)
    ->paperWidth(48)           // ancho personalizado
    ->withCut(true)            // cortar al final (default: true)
    ->withDrawer(false)        // abrir cajón (default: false)
    ->url('https://localhost:8443') // URL de ApiPeruDevPrint
    ->send($document);
```

## Listar impresoras disponibles

```php
$printers = Printer::availablePrinters();
// ['TP-300', 'Microsoft Print to PDF', ...]
```

## Integración con Laravel

```php
// En un Service Provider o directamente en el controlador:
use ESolutions\Escpos\Document;
use ESolutions\Escpos\Printer;
use ESolutions\Escpos\Exceptions\PrinterException;

try {
    $doc = Document::make()
        ->textCenter('Boleta de Venta', bold: true)
        // ...
        ->cut();

    Printer::create()->to(config('printing.printer'))->send($doc);
} catch (PrinterException $e) {
    Log::error('Error de impresión: ' . $e->getMessage());
}
```

## Licencia

MIT
