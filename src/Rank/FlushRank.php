<?php

namespace Garak\Pokerino\Rank;

use Garak\Pokerino\CardSorter;

final class FlushRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        $high = null;
        $kicker = null;
        // Build counts using int keys to avoid string/int mismatch from array_count_values
        $counts = [];
        foreach ($cards as $card) {
            $s = $card->getSuit()->getInt();
            $counts[$s] = ($counts[$s] ?? 0) + 1;
        }

        // Evaluate all suits that have 5 or more cards and choose the best flush.
        // This handles the case where callers pass more than 7 cards and multiple
        // suits may qualify; we must pick the best flush by comparing the top
        // five suited cards lexicographically.
        $bestFlush = null; // array of Card
        foreach ($counts as $suit => $count) {
            // array_count_values can produce string keys even when values are ints.
            // Use non-strict comparisons below to avoid type mismatch between
            // the array key (string) and getSuit()->getInt() (int) while keeping
            // static analysis happy.
            if ($count < 5) {
                continue;
            }

            // Collect cards of this suit and sort them descending
            $suitCards = [];
            foreach ($cards as $card) {
                if ($card->getSuit()->getInt() === $suit) {
                    $suitCards[] = $card;
                }
            }
            if ([] === $suitCards) {
                continue;
            }
            CardSorter::sort($suitCards);
            $topFive = \array_slice($suitCards, 0, 5);

            if (null === $bestFlush) {
                $bestFlush = $topFive;
                continue;
            }

            // Compare topFive with bestFlush lexicographically by rank
            for ($i = 0; $i < 5; ++$i) {
                $r1 = $topFive[$i]->getRank()->getInt();
                $r2 = $bestFlush[$i]->getRank()->getInt();
                if ($r1 > $r2) {
                    $bestFlush = $topFive;
                    break;
                }
                if ($r1 < $r2) {
                    break;
                }
                // otherwise equal -> continue to next card
            }
        }

        if (null === $bestFlush) {
            return new RankResult(false);
        }

        // The highest card of the flush is the first element. For compatibility
        // with existing behavior, the kicker is the highest card that is NOT
        // part of the flush (a side card), so we search the full card set for
        // the first card of a different suit after sorting.
        $high = $bestFlush[0];
        $flushSuit = $high->getSuit()->getInt();

        CardSorter::sort($cards);
        $kicker = null;
        foreach ($cards as $card) {
            if ($card->getSuit()->getInt() !== $flushSuit) {
                $kicker = $card;
                break;
            }
        }

        return new RankResult(true, $high, $kicker);
    }
}
