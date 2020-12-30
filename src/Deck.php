<?php

namespace Pokerino;

final class Deck
{
    /** @var array<Card> */
    private array $cards = [];

    public function __construct()
    {
        $values = Value::$values;
        foreach (Suit::$suits as $suit => $symbol) {
            foreach ($values as $value) {
                $this->cards[] = new Card(new Value((string) $value), new Suit($suit));
            }
        }
    }

    /**
     * @return array<Card>
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function draw(): ?Card
    {
        return \array_pop($this->cards);
    }

    public function shuffle(): void
    {
        \shuffle($this->cards);
    }
}
