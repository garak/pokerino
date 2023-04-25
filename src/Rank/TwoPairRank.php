<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;

final class TwoPairRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        $high = null;
        $kicker = null;
        $counts = \array_count_values(self::getCardsValues($cards));
        $suitNames = \array_keys($counts, 2);
        if (2 !== \count($suitNames)) {
            return new RankResult(false);
        }
        CardSorter::sort($cards);
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (2 === $counts[$rank]) {
                $high = $card;
                break;
            }
        }
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (2 !== $counts[$rank]) {
                $kicker = $card;
                break;
            }
        }

        return new RankResult(true, $high, $kicker);
    }
}
