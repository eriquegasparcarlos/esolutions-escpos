<?php

namespace ESolutions\Escpos\Commands;

class FeedCommand implements Command
{
    public function __construct(private int $lines = 1) {}

    public function toArray(): array
    {
        return ['feed' => $this->lines];
    }
}
