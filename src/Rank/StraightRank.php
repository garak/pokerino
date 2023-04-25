<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;

final class StraightRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
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
        $first = \current($values);
        $sequence = 0;
        $maxSequence = 0;
        $prev = null;
        $max = null;
        $value = null;
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

            return new RankResult(true, $high, $kicker);
        }
        // straight with ace in first position
        if (14 === $value && 2 === $first && 3 === \max($sequence, $maxSequence) && \in_array(5, $values, true) && \in_array(4, $values, true) && \in_array(3, $values, true)) {
            foreach ($cards as $card) {
                if (5 === $card->getRank()->getInt()) { // A, 2, 3, 4, 5
                    $high = $card;
                    break;
                }
            }
            // kicker is the fifth card, because the ace is ranked as higher (so it's at the end)
            $kicker = $cards[4];

            return new RankResult(true, $high, $kicker);
        }

        return new RankResult(false);
    }
}
