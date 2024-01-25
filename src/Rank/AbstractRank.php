<?php

namespace Garak\Pokerino\Rank;

use Garak\Card\Card;
use Garak\Pokerino\CardSorter;

abstract class AbstractRank
{
    /**
     * @param array<int, Card> $cards
     */
    abstract public static function isPoint(array $cards): RankResult;

    /**
     * @param array<int, Card> $cards
     *
     * @return array<int, int>
     */
    protected static function getCardsValues(array $cards): array
    {
        return \array_map(static fn (Card $card): int => $card->getRank()->getInt(), $cards);
    }

    /**
     * @param array<int, Card> $cards
     *
     * @return array<int, int>
     */
    protected static function getCardsSuits(array $cards): array
    {
        return \array_map(static fn (Card $card): int => $card->getSuit()->getInt(), $cards);
    }

    /**
     * @param array<int, Card> $cards
     *
     * @return array<int, Card>
     */
    protected static function removeDuplicated(array $cards): array
    {
        $counts = \array_count_values(self::getCardsValues($cards));
        $return = $cards;
        foreach ($cards as $key => $card) {
            $rank = $card->getRank()->getInt();
            if ($counts[$rank] > 1) {
                unset($return[$key]);
                break;
            }
        }

        return $return;
    }

    /**
     * @param array<int, Card> $cards
     *
     * @return array{
     *     'max': mixed,
     *     'sequence': mixed,
     *     'maxSequence': mixed,
     *     'values': mixed,
     *     'cardsInStraight': mixed,
     *     'cards': array<int, Card>
     * }
     */
    protected static function straight(array $cards): array
    {
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

        return \compact('max', 'sequence', 'maxSequence', 'values', 'cardsInStraight', 'cards');
    }
}
