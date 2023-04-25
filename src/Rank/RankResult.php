<?php

namespace Garak\Pokerino\Rank;

use Garak\Card\Card;

class RankResult
{
    private bool $point;

    private ?Card $high;

    private ?Card $kicker;

    public function __construct(bool $point, ?Card $high = null, ?Card $kicker = null)
    {
        $this->point = $point;
        $this->high = $high;
        $this->kicker = $kicker;
    }

    public function isPoint(): bool
    {
        return $this->point;
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
