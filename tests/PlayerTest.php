<?php

namespace Garak\Pokerino\Tests;

use Garak\Pokerino\Test\StubPlayer;
use PHPUnit\Framework\TestCase;

final class PlayerTest extends TestCase
{
    public function testName(): void
    {
        $player = new StubPlayer('John Doe');
        self::assertEquals('John Doe', $player->getName());
        self::assertEquals('John Doe', $player);
    }
}
