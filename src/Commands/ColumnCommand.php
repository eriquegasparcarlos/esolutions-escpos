<?php

namespace ESolutions\Escpos\Commands;

class ColumnCommand implements Command
{
    private array $columns = [];

    /**
     * @param array<array{text: string, width: int, align?: string}> $columns
     */
    public function __construct(array $columns)
    {
        foreach ($columns as $col) {
            $this->columns[] = [
                'text'  => (string) ($col['text']  ?? ''),
                'width' => (int)    ($col['width'] ?? 10),
                'align' => (string) ($col['align'] ?? 'left'),
            ];
        }
    }

    public function toArray(): array
    {
        return ['columns' => $this->columns];
    }
}
