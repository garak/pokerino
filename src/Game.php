<?php

namespace Garak\Pokerino;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Garak\Card\Card;

abstract class Game
{
    protected ?Player $currentPlayer;

    /** @var Collection<int, Player> */
    protected Collection $players;

    /** @var Collection<int, Hand> */
    private Collection $hands;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->hands = new ArrayCollection();
    }

    public function join(Player $player): void
    {
        if ($this->players->contains($player)) {
            throw new \InvalidArgumentException('Player already joined.');
        }
        $this->players->add($player);
        $this->currentPlayer = $player;
    }

    public function hasPlayer(Player $player): bool
    {
        return $this->players->contains($player);
    }

    /** @return Collection<int, Hand> */
    public function getHands(): Collection
    {
        return $this->hands;
    }

    public function deal(int $startingHandCount = 2, int $commonCount = 5): void
    {
        $cards = Card::getDeck(true);
        for ($i = 0; $i < $this->players->count(); ++$i) {
            $handCards = [];
            for ($j = 0; $j < $startingHandCount; ++$j) {
                if (null !== $card = \array_shift($cards)) {
                    $handCards[] = $card;
                }
            }
            $this->hands->add(new Hand($handCards));
        }
        if ($commonCount > 0) {
            $handCards = [];
            for ($i = 0; $i < $commonCount; ++$i) {
                if (null !== $card = \array_shift($cards)) {
                    $handCards[] = $card;
                }
            }
            $this->hands->add(new Hand($handCards));
        }
    }
}
