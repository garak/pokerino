<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;

final class RoyalFlushRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        $counts = \array_count_values(self::getCardsSuits($cards));
        if (!\in_array(5, $counts, true)) {
            return new RankResult(false);
        }
        $high = null;
        $kicker = null;
        // check if there's a three: in this case, we remove one card
        $three = ThreeOfAKindRank::isPoint($cards);
        if ($three->isPoint()) {
            $cards = self::removeDuplicated($cards);
        }
        // there's stil a pair: we need to remove one more card
        $pair = PairRank::isPoint($cards);
        if ($pair->isPoint()) {
            $cards = self::removeDuplicated($cards);
        }
        // sorting is not working in case of pair
        CardSorter::sort($cards, true);
        $values = self::getCardsValues($cards);
        $sequence = 0;
        $maxSequence = 0;
        $prev = null;
        $max = null;
        $cardsInStraight = [];

        foreach ($values as $key => $value) {
            if (null !== $prev && 1 === $value - $prev) {
                $max = $value;
                ++$sequence;
            } else {
                if ($sequence > $maxSequence) {
                    $maxSequence = $sequence;
                }
                $sequence = 0;
                $cardsInStraight = [];
            }
            $prev = $value;
            $cardsInStraight[] = $cards[$key];
            if (\count($cardsInStraight) >= 5) {
                break;
            }
        }

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

            if ('A' === $cards[0]->getRank()->getValue()) {
                return new RankResult(true, $high, $kicker);
            }
        }

        return new RankResult(false);
    }
}
