<?php

namespace Garak\Pokerino;

use Garak\Card\Card;
use Garak\Card\Suit;
use Garak\Card\SuitOrder;

final class CardSorter
{
    /** @param array<int|string, Card> $cards */
    public static function sort(array &$cards, bool $ascending = false): void
    {
        $suitOrder = new SuitOrder([...Suit::jokers(), ...Suit::regular()]);
        \usort($cards, static function (Card $card1, Card $card2) use ($suitOrder): int {
            if ($card1->getRank()->isEqual($card2->getRank())) {
                return $suitOrder->compareCards($card1, $card2);
            }

            return RankValue::of($card1) <=> RankValue::of($card2);
        });
        if (!$ascending) {
            $cards = \array_reverse($cards);
        }
    }
}
