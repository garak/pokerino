<?php

namespace Garak\Pokerino;

use Garak\Card\Card;
use Garak\Card\Hand as BaseHand;

final class Hand extends BaseHand
{
    private ?Card $high = null;

    private ?Card $kicker = null;
    /** @var int[] */
    private array $tieBreak = [];

    private int $handStrength = 10; // defaults to High Card strength

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
        $this->handStrength = $rank->getHandStrength();
        $this->tieBreak = $rank->getTieBreak();

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

    /** @return int[] */
    public function getTieBreak(): array
    {
        return $this->tieBreak;
    }

    /**
     * Returns a numeric hand strength: 1 = Royal Flush … 10 = High Card.
     * Call getPoint() first, or it will be lazily evaluated when needed.
     */
    public function getHandStrength(): int
    {
        // Lazily evaluate the hand if it has not been evaluated yet. We detect
        // this by checking whether `high` and `kicker` are still null. In that
        // case, calling getPoint() will populate high, kicker and
        // handStrength.
        if (null === $this->high && null === $this->kicker) {
            $this->getPoint();
        }

        return $this->handStrength;
    }
}
