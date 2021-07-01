<?php

namespace Pokerino;

use Garak\Card\Card;

final class CardSorter
{
    /** @param array<int|string, Card> $cards */
    public static function sort(array &$cards, bool $ascending = false): void
    {
        \usort($cards, static function (Card $card1, Card $card2): int {
            if ($card1->getRank()->isEqual($card2->getRank())) {
                return $card1->getSuit()->getInt() <=> $card2->getSuit()->getInt();
            }

            return $card1->getRank()->getInt() <=> $card2->getRank()->getInt();
        });
        if (!$ascending) {
            $cards = \array_reverse($cards);
        }
    }
}
