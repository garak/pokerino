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
        self::assertCount(2, $game->getHands()[0]->getCards());
        self::assertCount(2, $game->getHands()[1]->getCards());
        self::assertCount(2, $game->getHands()[2]->getCards());
        self::assertCount(5, $game->getHands()[3]->getCards());
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
