<?php

namespace ESolutions\Escpos\Commands;

class TextCommand implements Command
{
    public function __construct(
        private string $text,
        private string $align = 'left',
        private bool   $bold = false,
        private bool   $underline = false,
        private int    $size = 1,
        private string $font = 'A',
    ) {}

    public function toArray(): array
    {
        $cmd = ['text' => $this->text];

        if ($this->align !== 'left')      $cmd['align']     = $this->align;
        if ($this->bold)                  $cmd['bold']      = true;
        if ($this->underline)             $cmd['underline'] = true;
        if ($this->size !== 1)            $cmd['size']      = $this->size;
        if ($this->font !== 'A')          $cmd['font']      = $this->font;

        return $cmd;
    }
}
