<?php

namespace Garak\Pokerino\Tests;

use Garak\Card\Card;
use Garak\Pokerino\Test\StubGame;
use Garak\Pokerino\Test\StubPlayer;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testDeal(): void
    {
        $game = new StubGame();
        $player1 = new StubPlayer('John Doe');
        $player2 = new StubPlayer('Will Smith');
        $player3 = new StubPlayer('Bart Simpson');
        $game->join($player1);
        $game->join($player2);
        $game->join($player3);
        $game->deal();
        self::assertTrue($game->hasPlayer($player1));
        self::assertTrue($player1->isPlaying($game));
        [$hand1, $hand2, $hand3, $hand4] = $game->getHands();
        self::assertCount(2, $hand1->getCards());   // @phpstan-ignore-line method.nonObject
        self::assertCount(2, $hand2->getCards());   // @phpstan-ignore-line method.nonObject
        self::assertCount(2, $hand3->getCards());   // @phpstan-ignore-line method.nonObject
        self::assertCount(5, $hand4->getCards());   // @phpstan-ignore-line method.nonObject
    }

    public function testCannotJoinTwice(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $game = new StubGame();
        $player1 = new StubPlayer('John Doe');
        $game->join($player1);
        $game->join($player1);
    }

    public function testWinnerNullBeforeDeal(): void
    {
        $game = new StubGame();
        $player1 = new StubPlayer('Alice');
        $game->join($player1);
        self::assertNull($game->winner());
    }

    public function testWinnerReturnsPlayerAfterDeal(): void
    {
        $game = new StubGame();
        $player1 = new StubPlayer('Alice');
        $player2 = new StubPlayer('Bob');
        $game->join($player1);
        $game->join($player2);
        $game->deal();
        $winner = $game->winner();
        self::assertNotNull($winner);
        self::assertTrue($game->hasPlayer($winner));
    }

    public function testWinnerWithKnownCards(): void
    {
        // Alice: Ac Kc (hole) → with community forms A-K-Q-J-T = Royal Flush
        // Bob:   2s 3s (hole) → with community forms at best a Straight
        // Community: Qc Jc Tc 2d 3d
        $game = new StubGame();
        $alice = new StubPlayer('Alice');
        $bob = new StubPlayer('Bob');
        $game->join($alice);
        $game->join($bob);

        // Build a fixed deck: Alice's hole, Bob's hole, then community cards, rest can be anything
        $deck = [
            Card::fromRankSuit('Ac'), // Alice hole 1
            Card::fromRankSuit('Kc'), // Alice hole 2
            Card::fromRankSuit('2s'), // Bob hole 1
            Card::fromRankSuit('3s'), // Bob hole 2
            Card::fromRankSuit('Qc'), // community 1
            Card::fromRankSuit('Jc'), // community 2
            Card::fromRankSuit('Tc'), // community 3
            Card::fromRankSuit('2d'), // community 4
            Card::fromRankSuit('3d'), // community 5
        ];

        $game->deal(2, 5, $deck);
        $winner = $game->winner();
        self::assertSame($alice, $winner, 'Alice should win with a Royal Flush');
    }
}
