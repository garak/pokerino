<?php

namespace Garak\Pokerino\Tests;

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
        self::assertCount(2, $hand1->getCards());
        self::assertCount(2, $hand2->getCards());
        self::assertCount(2, $hand3->getCards());
        self::assertCount(5, $hand4->getCards());
    }

    public function testCannotJoinTwice(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $game = new StubGame();
        $player1 = new StubPlayer('John Doe');
        $game->join($player1);
        $game->join($player1);
    }
}
