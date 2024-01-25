<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;

final class StraightFlushRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        $counts = \array_count_values(self::getCardsSuits($cards));
        if (!\in_array(5, $counts, true)) {
            return new RankResult(false);
        }

        $high = null;
        $kicker = null;
        [
            'max' => $max,
            'sequence' => $sequence,
            'maxSequence' => $maxSequence,
            'values' => $values,
            'cardsInStraight' => $cardsInStraight,
            'cards' => $cards,
        ] = self::straight($cards);

        if (4 === \max($sequence, $maxSequence)) {
            $maxKey = \array_search($max, $values, true);
            foreach ($cards as $card) {
                if ($card->getRank()->getInt() === $values[$maxKey]) {
                    $high = $card;
                    break;
                }
            }
            CardSorter::sort($cards);
            foreach ($cards as $card) {
                if (!\in_array($card, $cardsInStraight, true)) {
                    $kicker = $card;
                    break;
                }
            }

            return new RankResult(true, $high, $kicker);
        }

        return new RankResult(false);
    }
}
