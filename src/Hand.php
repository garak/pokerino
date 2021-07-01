<?php

namespace Pokerino;

use Garak\Card\Card;

final class Hand
{
    /** @var array<Card> */
    private array $cards;

    private ?Card $high = null;

    private ?Card $kicker = null;

    /**
     * @param array<Card> $cards
     */
    public function __construct(array $cards)
    {
        $this->cards = $cards;
    }

    public function getPoint(): string
    {
        $rank = new PokerRank($this->cards);
        $point = $rank->getPoint();
        $this->high = $rank->getHigh();
        $this->kicker = $rank->getKicker();

        return $point;
    }

    /**
     * @return array<Card>
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function getHigh(): ?Card
    {
        return $this->high;
    }

    public function getKicker(): ?Card
    {
        return $this->kicker;
    }
}
