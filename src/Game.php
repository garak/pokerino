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

    /**
     * @param array<int, Card>|null $deck Pre-arranged deck (useful for testing). If null, a shuffled deck is used.
     */
    public function deal(int $startingHandCount = 2, int $commonCount = 5, ?array $deck = null): void
    {
        $cards = $deck ?? Card::getDeck(true);
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

    /**
     * Determines the winner by combining each player's hole cards with the community cards.
     * Returns null if no hands have been dealt yet or there are no community cards.
     */
    public function winner(): ?Player
    {
        $handsCount = $this->hands->count();
        $playersCount = $this->players->count();
        if ($handsCount < 2 || 0 === $playersCount) {
            return null;
        }

        // Ensure a community hand exists. In the expected setup each player
        // has a hand and there's one additional community hand (playersCount + 1).
        // If no community cards are present (e.g. commonCount = 0) or hands were
        // populated differently, do not attempt to compute a winner here.
        if ($handsCount !== $playersCount + 1) {
            return null;
        }

        // Determine community hand robustly: pick the hand with the largest
        // number of cards if it is a unique maximum. This avoids assuming the
        // community hand is always the last element in the collection which
        // may not hold after merges or external modifications to $this->hands.
        $handCounts = [];
        for ($h = 0; $h < $handsCount; ++$h) {
            $hObj = $this->hands->get($h);
            $handCounts[$h] = null === $hObj ? 0 :
                \count($hObj->getCards());
        }

        if ([] === $handCounts) {
            return null;
        }
        $maxCount = \max($handCounts);
        // find indices that have the max count
        $maxIndices = [];
        foreach ($handCounts as $idx => $c) {
            if ($c === $maxCount) {
                $maxIndices[] = $idx;
            }
        }

        // require a unique community hand (unique maximum). If ambiguous,
        // bail out to avoid making wrong assumptions.
        if (1 !== \count($maxIndices)) {
            return null;
        }

        $communityIndex = $maxIndices[0];
        $communityHand = $this->hands->get($communityIndex);
        if (null === $communityHand) {
            return null;
        }
        $communityCards = $communityHand->getCards();
        if ([] === $communityCards) {
            return null;
        }

        $bestPlayer = null;
        $bestStrength = \PHP_INT_MAX; // lower is stronger
        $bestTie = [];

        // Build ordered list of player hands by iterating all hands and
        // skipping the community hand. This preserves the relative order of
        // non-community hands as stored in the collection.
        $playerHands = [];
        for ($j = 0; $j < $handsCount; ++$j) {
            if ($j === $communityIndex) {
                continue;
            }
            $hObj = $this->hands->get($j);
            if (null === $hObj) {
                continue;
            }
            $playerHands[] = $hObj;
        }

        if (\count($playerHands) < $playersCount) {
            // Not enough player hands available
            return null;
        }

        for ($i = 0; $i < $playersCount; ++$i) {
            $playerHand = $playerHands[$i];
            // Combine hole cards with community cards to form a 7-card hand
            $combinedCards = \array_merge($playerHand->getCards(), $communityCards);
            $combined = new Hand($combinedCards);
            $combined->getPoint();

            $strength = $combined->getHandStrength();
            $tie = $combined->getTieBreak();

            // Compare: lower strength wins; on tie use lexicographic tie-break vector
            if ($strength < $bestStrength) {
                $bestStrength = $strength;
                $bestTie = $tie;
                $bestPlayer = $this->players->get($i);
            } elseif ($strength === $bestStrength) {
                // lexicographic compare
                // $bestTie is always initialized as an array
                $max = \max(\count($tie), \count($bestTie));
                $cmp = 0;
                for ($k = 0; $k < $max; ++$k) {
                    $v1 = $tie[$k] ?? 0;
                    $v2 = $bestTie[$k] ?? 0;
                    if ($v1 > $v2) {
                        $cmp = 1;
                        break;
                    }
                    if ($v1 < $v2) {
                        $cmp = -1;
                        break;
                    }
                }
                if (1 === $cmp) {
                    $bestTie = $tie;
                    $bestPlayer = $this->players->get($i);
                }
            }
        }

        return $bestPlayer;
    }
}
