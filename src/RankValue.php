<?php

namespace Garak\Pokerino;

use Garak\Card\Card;
use Garak\Card\CardValues;
use Garak\Card\Rank;

final class RankValue
{
    private static ?CardValues $values = null;

    /**
     * Two is 2, ace is 14. A joker is -1, so it never takes part in a sequence.
     */
    public static function of(Card $card): int
    {
        self::$values ??= CardValues::aceHigh()->withRank(Rank::Joker, -1);

        return self::$values->ofRank($card->getRank());
    }
}
