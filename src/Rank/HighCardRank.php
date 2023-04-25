<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;

final class HighCardRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        CardSorter::sort($cards);
        [$high, $kicker] = $cards;

        return new RankResult(true, $high, $kicker);
    }
}
