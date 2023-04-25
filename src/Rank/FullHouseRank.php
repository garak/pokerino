<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;

final class FullHouseRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        $high = null;
        $kicker = null;
        $counts = \array_count_values(self::getCardsValues($cards));
        $three = \in_array(3, $counts, true);
        $two = \in_array(2, $counts, true);
        if (false === $three || false === $two) {
            return new RankResult(false);
        }
        CardSorter::sort($cards);
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (3 === $counts[$rank] || 2 === $counts[$rank]) {
                $high = $card;
                break;
            }
        }
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (2 !== $counts[$rank] && 3 !== $counts[$rank]) {
                $kicker = $card;
                break;
            }
        }

        return new RankResult(true, $high, $kicker);
    }
}
