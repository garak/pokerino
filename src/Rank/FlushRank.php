<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;

final class FlushRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        $high = null;
        $kicker = null;
        $counts = \array_count_values(self::getCardsSuits($cards));
        if (!\in_array(5, $counts, true)) {
            return new RankResult(false);
        }
        CardSorter::sort($cards);
        foreach ($cards as $card) {
            $suitVal = $card->getSuit()->getInt();
            if (5 === $counts[$suitVal]) {
                $high = $card;
                break;
            }
        }
        foreach ($cards as $card) {
            $suitVal = $card->getSuit()->getInt();
            if (5 !== $counts[$suitVal]) {
                $kicker = $card;
                break;
            }
        }

        return new RankResult(true, $high, $kicker);
    }
}
