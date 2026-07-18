<?php

namespace Garak\Pokerino\Tests;

use Garak\Card\Card;
use Garak\Pokerino\PokerRank;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PokerRankTest extends TestCase
{
    public function testHandStrengthThrowsWhenCurrentPointIsUnknown(): void
    {
        $rank = new PokerRank([
            Card::fromRankSuit('Ac'),
            Card::fromRankSuit('Kc'),
            Card::fromRankSuit('Qc'),
            Card::fromRankSuit('Jc'),
            Card::fromRankSuit('Tc'),
            Card::fromRankSuit('2d'),
            Card::fromRankSuit('3h'),
        ]);

        $currentPoint = new \ReflectionProperty($rank, 'currentPoint');
        $currentPoint->setValue($rank, 'Not A Real Point');

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown hand point value.');

        $rank->getHandStrength();
    }

    public function testHandStrengthThrowsWhenStrengthMapDoesNotContainCurrentPoint(): void
    {
        $rank = new PokerRank([
            Card::fromRankSuit('Ac'),
            Card::fromRankSuit('Kc'),
            Card::fromRankSuit('Qc'),
            Card::fromRankSuit('Jc'),
            Card::fromRankSuit('Tc'),
            Card::fromRankSuit('2d'),
            Card::fromRankSuit('3h'),
        ]);

        $currentPoint = new \ReflectionProperty($rank, 'currentPoint');
        $currentPoint->setValue($rank, 'Royal Flush');

        $pointStrength = new \ReflectionProperty($rank, 'pointStrength');
        $pointStrength->setValue($rank, []);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown hand point value.');

        $rank->getHandStrength();
    }

    /**
     * @param string[] $ranks
     */
    #[DataProvider('strengthProvider')]
    public function testHandStrength(array $ranks, int $expected, string $message): void
    {
        $cards = [];
        foreach ($ranks as $r) {
            $cards[] = Card::fromRankSuit($r);
        }

        $rank = new PokerRank($cards);
        $strength = $rank->getHandStrength();

        self::assertSame($expected, $strength, $message);
    }

    /**
     * @return array<string, array{array<string>,int,string}>
     */
    public static function strengthProvider(): array
    {
        return [
            // Royal Flush (should be strength 1)
            'royal-flush' => [
                ['Ac', 'Kc', 'Qc', 'Jc', 'Tc', '2d', '3h'],
                1,
                'Royal Flush must map to strength 1',
            ],
            // Full House (should be strength 4)
            'full-house' => [
                ['9c', '9d', '9h', '2s', '2d', '3h', '4c'],
                4,
                'Full House must map to strength 4',
            ],
            // High Card (should be strength 10)
            'high-card' => [
                ['Ac', 'Kd', '9h', '7s', '4c', '2d', '6h'],
                10,
                'High Card must map to strength 10',
            ],
        ];
    }
}
