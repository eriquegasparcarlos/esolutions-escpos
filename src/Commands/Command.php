<?php

namespace ESolutions\Escpos\Commands;

interface Command
{
    public function toArray(): array;
}
