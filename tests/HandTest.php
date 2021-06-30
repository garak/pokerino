<?php

namespace Pokerino\Tests;

use Garak\Card\Card;
use PHPUnit\Framework\TestCase;
use Pokerino\Hand;

final class HandTest extends TestCase
{
    /**
     * @dataProvider getPoints
     *
     * @param array<Card> $cards
     */
    public function testPoint(array $cards, string $point): void
    {
        $hand = new Hand($cards);
        #print_r(array_map('strval', $cards));
        self::assertEquals($point, $hand->getPoint());
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
                    // TODO it doesn't work with more than 5 cards... :-|
                    //Card::fromRankSuit('2s'),
                    //Card::fromRankSuit('Kd'),
                ],
                'point' => 'High Card',
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
           ],
            'royal flush' => [
                'cards' => [
                    Card::fromRankSuit('Ac'),
                    Card::fromRankSuit('Tc'),
                    Card::fromRankSuit('Jc'),
                    Card::fromRankSuit('Qc'),
                    Card::fromRankSuit('Kc'),
                ],
                'point' => 'Royal Flush',
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
            ],
            'full' => [
                'cards' => [
                    Card::fromRankSuit('6c'),
                    Card::fromRankSuit('6d'),
                    Card::fromRankSuit('7h'),
                    Card::fromRankSuit('7c'),
                    Card::fromRankSuit('6s'),
                ],
                'point' => 'Full House',
            ],
            'straight' => [
                'cards' => [
                    Card::fromRankSuit('6c'),
                    Card::fromRankSuit('7d'),
                    Card::fromRankSuit('8h'),
                    Card::fromRankSuit('9c'),
                    Card::fromRankSuit('Ts'),
                ],
                'point' => 'Straight',
            ],
            'straight with first ace' => [
                'cards' => [
                    Card::fromRankSuit('Ac'),
                    Card::fromRankSuit('2d'),
                    Card::fromRankSuit('3h'),
                    Card::fromRankSuit('4c'),
                    Card::fromRankSuit('5s'),
                ],
                'point' => 'Straight',
            ],
            'straight with last ace' => [
                'cards' => [
                    Card::fromRankSuit('Ac'),
                    Card::fromRankSuit('Kd'),
                    Card::fromRankSuit('Qh'),
                    Card::fromRankSuit('Tc'),
                    Card::fromRankSuit('Js'),
                ],
                'point' => 'Straight',
            ],
            'flush' => [
                'cards' => [
                    Card::fromRankSuit('6d'),
                    Card::fromRankSuit('Qd'),
                    Card::fromRankSuit('8d'),
                    Card::fromRankSuit('Kd'),
                    Card::fromRankSuit('Td'),
                ],
                'point' => 'Flush',
            ],
        ];
    }
}
