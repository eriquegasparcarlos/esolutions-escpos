<?php

namespace ESolutions\Escpos\Commands;

class BarcodeCommand implements Command
{
    // Tipos soportados por ApiPeruDevPrint
    const TYPE_CODE39  = 'CODE39';
    const TYPE_CODE128 = 'CODE128';
    const TYPE_EAN13   = 'EAN13';
    const TYPE_UPCA    = 'UPCA';

    public function __construct(
        private string $data,
        private string $type = self::TYPE_CODE128,
        private string $align = 'center',
    ) {}

    public function toArray(): array
    {
        $cmd = ['barcode' => $this->data, 'barcodeType' => $this->type];

        if ($this->align !== 'left') $cmd['align'] = $this->align;

        return $cmd;
    }
}
