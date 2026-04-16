<?php

namespace ESolutions\Escpos\Commands;

class QrCommand implements Command
{
    public function __construct(
        private string $data,
        private int    $size = 6,
        private string $align = 'center',
    ) {}

    public function toArray(): array
    {
        $cmd = ['qr' => $this->data, 'qrSize' => $this->size];

        if ($this->align !== 'left') $cmd['align'] = $this->align;

        return $cmd;
    }
}
