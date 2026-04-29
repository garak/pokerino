<?php

namespace Garak\Pokerino\Rank;

use Garak\Card\Card;
use Garak\Pokerino\CardSorter;

final class RoyalFlushRank extends AbstractRank
{
    public static function isPoint(array $cards): RankResult
    {
        // Build counts using int keys to avoid string/int mismatch from array_count_values
        $counts = [];
        foreach ($cards as $card) {
            $s = $card->getSuit()->getInt();
            $counts[$s] = ($counts[$s] ?? 0) + 1;
        }

        // Evaluate all suits that have 5 or more cards and check for a royal
        // flush. We prefer any royal flush found; if multiple exist we keep
        // the one with the best kicker.
        $bestHigh = null;
        $bestKicker = null;

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

            if (4 !== \max($sequence, $maxSequence)) {
                continue;
            }

            $maxKey = \array_search($max, $values, true);
            $high = null;
            foreach ($flushCards as $card) {
                if ($card->getRank()->getInt() === $values[$maxKey]) {
                    $high = $card;
                    break;
                }
            }
            if (null === $high) {
                continue;
            }

            // Ensure the straight is specifically Ten-to-Ace (T-J-Q-K-A).
            // The `straight()` helper returns the suited straight in
            // `cardsInStraight` (ascending order). Verify ranks are [10,11,12,13,14].
            $straightRanks = \array_map(static fn (Card $c): int => $c->getRank()->getInt(), $cardsInStraight);
            \sort($straightRanks);
            if ($straightRanks !== [10, 11, 12, 13, 14]) {
                continue;
            }

            // Kicker: highest card not in the straight (from all cards, any suit)
            CardSorter::sort($cards);
            $kicker = null;
            foreach ($cards as $card) {
                if (!\in_array($card, $cardsInStraight, true)) {
                    $kicker = $card;
                    break;
                }
            }

            if (null === $bestHigh) {
                $bestHigh = $high;
                $bestKicker = $kicker;
                continue;
            }

            // Tie-breaker: prefer higher kicker
            $kickerRank = $kicker?->getRank()->getInt() ?? 0;
            $bestKickerRank = $bestKicker?->getRank()->getInt() ?? 0;
            if ($kickerRank > $bestKickerRank) {
                $bestHigh = $high;
                $bestKicker = $kicker;
            }
        }

        if (null === $bestHigh) {
            return new RankResult(false);
        }

        return new RankResult(true, $bestHigh, $bestKicker);
    }
}
