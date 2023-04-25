<?php

namespace Garak\Pokerino;

use Garak\Card\Card;
use Garak\Card\Hand as BaseHand;

final class Hand extends BaseHand
{
    private ?Card $high = null;

    private ?Card $kicker = null;

    public function __construct(array $cards, bool $start = true, ?callable $checking = null, ?callable $sorting = null)
    {
        $this->cards = $cards;
        if (null !== $sorting) {
            $this->sorting = $sorting;
        }
        if ($start && null !== $checking) {
            $checking($cards);
        }
    }

    public function getPoint(): string
    {
        $rank = new PokerRank($this->cards);
        $point = $rank->getPoint();
        $this->high = $rank->getHigh();
        $this->kicker = $rank->getKicker();

        return $point;
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
