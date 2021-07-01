<?php

namespace Garak\Pokerino\Tests;

use Garak\Card\Card;
use Garak\Pokerino\Hand;
use PHPUnit\Framework\TestCase;

final class HandTest extends TestCase
{
    /**
     * @dataProvider getPoints
     *
     * @param array<Card> $cards
     */
    public function testPoint(array $cards, string $point, string $high, string $kicker): void
    {
        $hand = new Hand($cards);
        self::assertEquals($point, $hand->getPoint());
        self::assertEquals($high, $hand->getHigh());
        self::assertEquals($kicker, $hand->getKicker());
    }

    /**
     * @return array<string, array<string, array<Card>|string>>
     */
    public function getPoints(): array
    {
        return [
            'none' => [
                'cards' => [
                    Card::fromRankSuit('6c'),
                    Card::fromRankSuit('Td'),
                    Card::fromRankSuit('Jh'),
                    Card::fromRankSuit('8s'),
                    Card::fromRankSuit('3s'),
                    Card::fromRankSuit('2s'),
                    Card::fromRankSuit('Kd'),
                ],
                'point' => 'High Card',
                'high' => 'Kd',
                'kicker' => 'Jh',
            ],
            'poker of 6s' => [
                'cards' => [
                    Card::fromRankSuit('6c'),
                    Card::fromRankSuit('6d'),
                    Card::fromRankSuit('6h'),
                    Card::fromRankSuit('8s'),
                    Card::fromRankSuit('6s'),
                ],
                'point' => '4 of a Kind',
                'high' => '6s',
                'kicker' => '8s',
           ],
            'royal flush' => [
                'cards' => [
                    Card::fromRankSuit('Ac'),
                    Card::fromRankSuit('Tc'),
                    Card::fromRankSuit('Jc'),
                    Card::fromRankSuit('Qc'),
                    Card::fromRankSuit('Kc'),
                    Card::fromRankSuit('3s'),
                ],
                'point' => 'Royal Flush',
                'high' => 'Ac',
                'kicker' => '3s',
            ],
            '3 of 6s' => [
                'cards' => [
                    Card::fromRankSuit('6c'),
                    Card::fromRankSuit('6d'),
                    Card::fromRankSuit('6h'),
                    Card::fromRankSuit('Qc'),
                    Card::fromRankSuit('Kc'),
                ],
                'point' => '3 of a Kind',
                'high' => '6h',
                'kicker' => 'Kc',
            ],
            'pair' => [
                'cards' => [
                    Card::fromRankSuit('6c'),
                    Card::fromRankSuit('6d'),
                    Card::fromRankSuit('7h'),
                    Card::fromRankSuit('Qc'),
                    Card::fromRankSuit('Kc'),
                ],
                'point' => '1 Pair',
                'high' => '6d',
                'kicker' => 'Kc',
            ],
            'double pair' => [
                'cards' => [
                    Card::fromRankSuit('6c'),
                    Card::fromRankSuit('6d'),
                    Card::fromRankSuit('7h'),
                    Card::fromRankSuit('7c'),
                    Card::fromRankSuit('Kc'),
                ],
                'point' => '2 Pair',
                'high' => '7h',
                'kicker' => 'Kc',
            ],
            'full' => [
                'cards' => [
                    Card::fromRankSuit('6c'),
                    Card::fromRankSuit('6d'),
                    Card::fromRankSuit('7h'),
                    Card::fromRankSuit('7c'),
                    Card::fromRankSuit('6s'),
                    Card::fromRankSuit('Kc'),
                ],
                'point' => 'Full House',
                'high' => '7h',
                'kicker' => 'Kc',
            ],
            'straight' => [
                'cards' => [
                    Card::fromRankSuit('6c'),
                    Card::fromRankSuit('7d'),
                    Card::fromRankSuit('8h'),
                    Card::fromRankSuit('9c'),
                    Card::fromRankSuit('Ts'),
                    Card::fromRankSuit('Kc'),
                    Card::fromRankSuit('7c'),
                ],
                'point' => 'Straight',
                'high' => 'Ts',
                'kicker' => 'Kc',
            ],
            'straight with first ace' => [
                'cards' => [
                    Card::fromRankSuit('Ac'),
                    Card::fromRankSuit('2d'),
                    Card::fromRankSuit('3h'),
                    Card::fromRankSuit('4c'),
                    Card::fromRankSuit('5s'),
                    Card::fromRankSuit('Kc'),
                ],
                'point' => 'Straight',
                'high' => '5s',
                'kicker' => 'Kc',
            ],
            'straight with last ace' => [
                'cards' => [
                    Card::fromRankSuit('Ac'),
                    Card::fromRankSuit('Kd'),
                    Card::fromRankSuit('3s'),
                    Card::fromRankSuit('Qh'),
                    Card::fromRankSuit('Tc'),
                    Card::fromRankSuit('Js'),
                ],
                'point' => 'Straight',
                'high' => 'Ac',
                'kicker' => '3s',
            ],
            'flush' => [
                'cards' => [
                    Card::fromRankSuit('6d'),
                    Card::fromRankSuit('Qd'),
                    Card::fromRankSuit('8d'),
                    Card::fromRankSuit('Kd'),
                    Card::fromRankSuit('Td'),
                    Card::fromRankSuit('Kc'),
                ],
                'point' => 'Flush',
                'high' => 'Kd',
                'kicker' => 'Kc',
            ],
        ];
    }
}
