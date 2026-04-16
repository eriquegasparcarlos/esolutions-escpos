<?php

namespace ESolutions\Escpos\Commands;

class ImageCommand implements Command
{
    public function __construct(
        private string $base64,
        private int    $width = 576,
        private string $align = 'center',
    ) {}

    public function toArray(): array
    {
        $cmd = ['imageBase64' => $this->base64, 'imageWidth' => $this->width];

        if ($this->align !== 'left') $cmd['align'] = $this->align;

        return $cmd;
    }
}
