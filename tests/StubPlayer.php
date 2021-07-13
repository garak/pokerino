<?php

namespace Garak\Pokerino\Test;

use Garak\Pokerino\Player;

final class StubPlayer extends Player
{
    public function isEqual(Player $player): bool
    {
        return true;
    }
}
