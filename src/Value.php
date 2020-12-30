<?php

namespace Pokerino;

final class Value
{
    /** @var array<int|string, int> */
    public static array $values = [
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'T' => 10,
        'J' => 11,
        'Q' => 12,
        'K' => 13,
        'A' => 14,
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!isset(self::$values[$value])) {
            throw new \InvalidArgumentException('Invalid value: '.$value);
        }
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getIntValue(): int
    {
        return self::$values[$this->value];
    }
}
