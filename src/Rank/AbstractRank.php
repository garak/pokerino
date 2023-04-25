<?php

namespace Garak\Pokerino\Rank;

use Garak\Card\Card;

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
}
