<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;
use Garak\Pokerino\RankValue;

final class FourOfAKindRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        $count = \array_count_values(self::getCardsValues($cards));
        $high = null;
        $kicker = null;
        if (!\in_array(4, $count, true)) {
            return new RankResult(false);
        }
        $value = \array_search(4, $count, true);
        CardSorter::sort($cards);
        foreach ($cards as $card) {
            if (RankValue::of($card) === $value) {
                $high = $card;
                break;
            }
        }
        foreach ($cards as $card) {
            if (RankValue::of($card) !== $value) {
                $kicker = $card;
                break;
            }
        }

        return new RankResult(true, $high, $kicker);
    }
}
