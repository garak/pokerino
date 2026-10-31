<?php

namespace Garak\Pokerino\Tests;

use Garak\Card\Card;
use Garak\Card\Rank;
use Garak\Card\Suit;
use Garak\Pokerino\RankValue;
use PHPUnit\Framework\TestCase;

final class RankValueTest extends TestCase
{
    public function testRegularRanks(): void
    {
        self::assertSame(2, RankValue::of(Card::fromRankSuit('2c')));
        self::assertSame(10, RankValue::of(Card::fromRankSuit('Td')));
        self::assertSame(14, RankValue::of(Card::fromRankSuit('As')));
    }

    public function testJoker(): void
    {
        self::assertSame(-1, RankValue::of(new Card(Rank::Joker, Suit::RedJoker)));
    }
}
