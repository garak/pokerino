<?php

namespace Garak\Pokerino\Rank;

use Garak\Card\Card;
use Garak\Pokerino\CardSorter;

final class StraightFlushRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        // Build counts using int keys to avoid string/int mismatch from array_count_values
        $counts = [];
        foreach ($cards as $card) {
            $s = $card->getSuit()->getInt();
            $counts[$s] = ($counts[$s] ?? 0) + 1;
        }

        // Evaluate all suits that have 5 or more cards and choose the best
        // straight flush (highest straight among suited cards).
        $bestHigh = null;
        $bestKicker = null;
        $bestHighRank = 0;

        foreach ($counts as $suit => $count) {
            if ($count < 5) {
                continue;
            }

            // Collect cards of this suit
            $flushCards = \array_values(\array_filter(
                $cards,
                static fn (Card $card): bool => $card->getSuit()->getInt() === $suit
            ));
            if ([] === $flushCards) {
                continue;
            }

            // Evaluate straight among these suited cards
            [
                'max' => $max,
                'sequence' => $sequence,
                'maxSequence' => $maxSequence,
                'values' => $values,
                'cardsInStraight' => $cardsInStraight,
                'cards' => $flushCards,
            ] = self::straight($flushCards);

            $isStraight = 4 === \max($sequence, $maxSequence);

            $high = null;
            $kicker = null;

            if ($isStraight) {
                $maxKey = \array_search($max, $values, true);
                foreach ($flushCards as $card) {
                    if ($card->getRank()->getInt() === $values[$maxKey]) {
                        $high = $card;
                        break;
                    }
                }
            } else {
                // Check Ace-low straight (wheel) among suited cards: A-2-3-4-5
                $first = \current($values);
                if (\in_array(14, $values, true) && 2 === $first && 3 === \max($sequence, $maxSequence) && \in_array(5, $values, true) && \in_array(4, $values, true) && \in_array(3, $values, true)) {
                    // high is the 5 in the wheel
                    foreach ($flushCards as $card) {
                        if (5 === $card->getRank()->getInt()) {
                            $high = $card;
                            break;
                        }
                    }
                    // prepare cardsInStraight already contains the straight suited cards
                }
            }

            if (null === $high) {
                continue;
            }

            // Kicker: highest card not in the straight (from all cards, any suit)
            CardSorter::sort($cards);
            foreach ($cards as $card) {
                if (!\in_array($card, $cardsInStraight, true)) {
                    $kicker = $card;
                    break;
                }
            }

            $highRank = $high->getRank()->getInt();
            if ($highRank > $bestHighRank) {
                $bestHighRank = $highRank;
                $bestHigh = $high;
                $bestKicker = $kicker;
            } elseif ($highRank === $bestHighRank) {
                // tie-break by kicker
                $kickerRank = $kicker?->getRank()->getInt() ?? 0;
                $bestKickerRank = $bestKicker?->getRank()->getInt() ?? 0;
                if ($kickerRank > $bestKickerRank) {
                    $bestHigh = $high;
                    $bestKicker = $kicker;
                }
            }
        }

        if (null === $bestHigh) {
            return new RankResult(false);
        }

        return new RankResult(true, $bestHigh, $bestKicker);
    }
}
