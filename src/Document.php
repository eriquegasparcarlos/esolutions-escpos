<?php

namespace ESolutions\Escpos;

use ESolutions\Escpos\Commands\{
    BarcodeCommand,
    ColumnCommand,
    Command,
    FeedCommand,
    ImageCommand,
    QrCommand,
    SeparatorCommand,
    TextCommand,
};

class Document
{
    /** @var Command[] */
    private array $commands = [];

    private function __construct() {}

    public static function make(): static
    {
        return new static();
    }

    // ─── Texto ────────────────────────────────────────────────────────────────

    public function text(
        string $text,
        string $align = 'left',
        bool   $bold = false,
        bool   $underline = false,
        int    $size = 1,
        string $font = 'A',
    ): static {
        $this->commands[] = new TextCommand($text, $align, $bold, $underline, $size, $font);
        return $this;
    }

    public function textCenter(string $text, bool $bold = false, int $size = 1): static
    {
        return $this->text($text, 'center', $bold, false, $size);
    }

    public function textRight(string $text, bool $bold = false): static
    {
        return $this->text($text, 'right', $bold);
    }

    public function bold(string $text, string $align = 'left', int $size = 1): static
    {
        return $this->text($text, $align, bold: true, size: $size);
    }

    public function small(string $text, string $align = 'left'): static
    {
        return $this->text($text, $align, font: 'B');
    }

    // ─── Layout ───────────────────────────────────────────────────────────────

    /**
     * Columnas de texto en una fila.
     *
     * @param array<array{text: string, width: int, align?: string}> $columns
     */
    public function columns(array $columns): static
    {
        $this->commands[] = new ColumnCommand($columns);
        return $this;
    }

    /**
     * Fila de 2 columnas: etiqueta izquierda / valor derecha.
     */
    public function row(string $label, string $value, int $paperWidth = 48): static
    {
        $labelWidth = (int) round($paperWidth * 0.55);
        $valueWidth = $paperWidth - $labelWidth;

        return $this->columns([
            ['text' => $label, 'width' => $labelWidth, 'align' => 'left'],
            ['text' => $value, 'width' => $valueWidth,  'align' => 'right'],
        ]);
    }

    public function separator(): static
    {
        $this->commands[] = new SeparatorCommand();
        return $this;
    }

    public function feed(int $lines = 1): static
    {
        $this->commands[] = new FeedCommand($lines);
        return $this;
    }

    // ─── Imágenes y códigos ───────────────────────────────────────────────────

    public function qr(string $data, int $size = 6, string $align = 'center'): static
    {
        $this->commands[] = new QrCommand($data, $size, $align);
        return $this;
    }

    public function barcode(string $data, string $type = BarcodeCommand::TYPE_CODE128, string $align = 'center'): static
    {
        $this->commands[] = new BarcodeCommand($data, $type, $align);
        return $this;
    }

    public function imageBase64(string $base64, int $width = 576, string $align = 'center'): static
    {
        $this->commands[] = new ImageCommand($base64, $width, $align);
        return $this;
    }

    public function imageFile(string $path, int $width = 576, string $align = 'center'): static
    {
        $data = base64_encode(file_get_contents($path));
        return $this->imageBase64($data, $width, $align);
    }

    // ─── Control ─────────────────────────────────────────────────────────────

    public function cut(): static
    {
        $this->commands[] = new class implements Command {
            public function toArray(): array { return ['cut' => true]; }
        };
        return $this;
    }

    public function openDrawer(): static
    {
        $this->commands[] = new class implements Command {
            public function toArray(): array { return ['openDrawer' => true]; }
        };
        return $this;
    }

    public function raw(Command $command): static
    {
        $this->commands[] = $command;
        return $this;
    }

    // ─── Serialización ───────────────────────────────────────────────────────

    public function toArray(): array
    {
        return array_map(fn(Command $c) => $c->toArray(), $this->commands);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
