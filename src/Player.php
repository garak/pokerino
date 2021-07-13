<?php

namespace Garak\Pokerino;

abstract class Player
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isPlaying(Game $game): bool
    {
        return $game->hasPlayer($this);
    }

    abstract public function isEqual(self $player): bool;
}
