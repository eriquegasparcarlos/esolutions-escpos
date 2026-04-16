<?php

namespace ESolutions\Escpos\Commands;

class SeparatorCommand implements Command
{
    public function toArray(): array
    {
        return ['separator' => true];
    }
}
