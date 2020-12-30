<?php

namespace Pokerino;

final class Card
{
    private Value $value;

    private Suit $suit;

    public function __construct(Value $value, Suit $suit)
    {
        $this->value = $value;
        $this->suit = $suit;
    }

    public static function card(string $value, string $suit): self
    {
        return new self(new Value($value), new Suit($suit));
    }

    public function __toString(): string
    {
        return $this->value.' '.$this->suit->getSymbol();
    }

    public function getSuit(): Suit
    {
        return $this->suit;
    }

    public function getValue(): Value
    {
        return $this->value;
    }
}
