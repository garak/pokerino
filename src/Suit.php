<?php

namespace Pokerino;

final class Suit
{
    /** @var array<string, string> */
    public static array $suits = [
        'c' => '♣',
        'd' => '♦',
        'h' => '♥',
        's' => '♠',
    ];

    /** @var array<string, string> */
    private static array $colors = [
        'c' => 'black',
        'd' => 'red',
        'h' => 'red',
        's' => 'black',
    ];

    /** @var array<string, int> */
    private static array $values = [
        'c' => 1,
        'd' => 2,
        'h' => 4,
        's' => 8,
    ];

    private string $name;

    public function __construct(string $name)
    {
        if (!isset(self::$suits[$name])) {
            throw new \InvalidArgumentException('Invalid suit name: '.$name);
        }
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSymbol(): string
    {
        return self::$suits[$this->name];
    }

    public function getColor(): string
    {
        return self::$colors[$this->name];
    }

    public function getIntValue(): int
    {
        return self::$values[$this->name];
    }
}
