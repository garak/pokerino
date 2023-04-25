<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;

final class ThreeOfAKindRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        $high = null;
        $kicker = null;
        $counts = \array_count_values(self::getCardsValues($cards));
        if (!\in_array(3, $counts, true)) {
            return new RankResult(false);
        }
        CardSorter::sort($cards);
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (3 === $counts[$rank]) {
                $high = $card;
                break;
            }
        }
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (3 !== $counts[$rank]) {
                $kicker = $card;
                break;
            }
        }

        return new RankResult(true, $high, $kicker);
    }
}
