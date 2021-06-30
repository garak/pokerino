<?php

namespace Pokerino\Tests;

use PHPUnit\Framework\TestCase;
use Pokerino\Card;
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
                    Card::card('6', 'c'),
                    Card::card('T', 'd'),
                    Card::card('J', 'h'),
                    Card::card('8', 's'),
                    Card::card('3', 's'),
                    // TODO it doesn't work with more than 5 cards... :-|
                    //Card::card('2', 's'),
                    //Card::card('K', 'd'),
                ],
                'point' => 'High Card',
            ],
            'poker of 6s' => [
                'cards' => [
                    Card::card('6', 'c'),
                    Card::card('6', 'd'),
                    Card::card('6', 'h'),
                    Card::card('8', 's'),
                    Card::card('6', 's'),
                ],
                'point' => '4 of a Kind',
           ],
            'royal flush' => [
                'cards' => [
                    Card::card('A', 'c'),
                    Card::card('T', 'c'),
                    Card::card('J', 'c'),
                    Card::card('Q', 'c'),
                    Card::card('K', 'c'),
                ],
                'point' => 'Royal Flush',
            ],
            '3 of 6s' => [
                'cards' => [
                    Card::card('6', 'c'),
                    Card::card('6', 'd'),
                    Card::card('6', 'h'),
                    Card::card('Q', 'c'),
                    Card::card('K', 'c'),
                ],
                'point' => '3 of a Kind',
            ],
            'pair' => [
                'cards' => [
                    Card::card('6', 'c'),
                    Card::card('6', 'd'),
                    Card::card('7', 'h'),
                    Card::card('Q', 'c'),
                    Card::card('K', 'c'),
                ],
                'point' => '1 Pair',
            ],
            'double pair' => [
                'cards' => [
                    Card::card('6', 'c'),
                    Card::card('6', 'd'),
                    Card::card('7', 'h'),
                    Card::card('7', 'c'),
                    Card::card('K', 'c'),
                ],
                'point' => '2 Pair',
            ],
            'full' => [
                'cards' => [
                    Card::card('6', 'c'),
                    Card::card('6', 'd'),
                    Card::card('7', 'h'),
                    Card::card('7', 'c'),
                    Card::card('6', 's'),
                ],
                'point' => 'Full House',
            ],
            'straight' => [
                'cards' => [
                    Card::card('6', 'c'),
                    Card::card('7', 'd'),
                    Card::card('8', 'h'),
                    Card::card('9', 'c'),
                    Card::card('T', 's'),
                ],
                'point' => 'Straight',
            ],
            'straight with first ace' => [
                'cards' => [
                    Card::card('A', 'c'),
                    Card::card('2', 'd'),
                    Card::card('3', 'h'),
                    Card::card('4', 'c'),
                    Card::card('5', 's'),
                ],
                'point' => 'Straight',
            ],
            'straight with last ace' => [
                'cards' => [
                    Card::card('A', 'c'),
                    Card::card('K', 'd'),
                    Card::card('Q', 'h'),
                    Card::card('T', 'c'),
                    Card::card('J', 's'),
                ],
                'point' => 'Straight',
            ],
            'flush' => [
                'cards' => [
                    Card::card('6', 'd'),
                    Card::card('Q', 'd'),
                    Card::card('8', 'd'),
                    Card::card('K', 'd'),
                    Card::card('T', 'd'),
                ],
                'point' => 'Flush',
            ],
        ];
    }
}
