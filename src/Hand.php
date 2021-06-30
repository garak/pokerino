<?php

namespace Pokerino;

use Garak\Card\Card;

final class Hand
{
    /** @var array<Card> */
    private array $cards;

    private ?int $high = null;

    private ?Card $kicker = null;

    /**
     * @param array<Card> $cards
     */
    public function __construct(array $cards)
    {
        $this->cards = $cards;
    }

    public function getPoint(): string
    {
        $points = [
            0 => '4 of a Kind',
            1 => 'Straight Flush',
            2 => 'Straight',
            3 => 'Flush',
            4 => 'High Card',
            5 => '1 Pair',
            6 => '2 Pair',
            7 => 'Royal Flush',
            8 => '3 of a Kind',
            9 => 'Full House',
        ];

        $values = [];
        $suits = [];

        foreach ($this->cards as $card) {
            $values[] = $card->getRank()->getInt();
            $suits[] = $card->getSuit()->getInt();
        }

        if (\in_array(4, \array_count_values($values), true)) {
            return $points[0];
        }

        if ($this->isStraight($values) && \in_array(5, \array_count_values($suits), true)) {
            return $points[7];
        }

        if ($this->isStraight($values)) {
            return $points[2];
        }

        if ($this->isFullHouse($values)) {
            return $points[9];
        }

        if (\in_array(3, \array_count_values($values), true)) {
            return $points[8];
        }

        if ($this->isTwoPair($suits, $values)) {
            return $points[6];
        }

        if (\in_array(2, \array_count_values($values), true)) {
            return $points[5];
        }

        if (\in_array(5, \array_count_values($suits), true)) {
            return $points[3];
        }

        return $points[4];
    }

    /**
     * @return array<Card>
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function getHigh(): ?int
    {
        return $this->high;
    }

    public function getKicker(): ?Card
    {
        return $this->kicker;
    }

    /**
     * @param array<int> $values
     */
    private function isStraight(array $values): bool
    {
        \asort($values);
        $first = \current($values);
        $sequence = 0;
        $maxSequence = 0;
        $prev = null;
        $max = null;
        $value = null;
        foreach ($values as $value) {
            if (null !== $prev && 1 === $value - $prev) {
                $max = $value;
                ++$sequence;
            } else {
                if ($sequence > $maxSequence) {
                    $maxSequence = $sequence;
                }
                $sequence = 0;
            }
            $prev = $value;
        }

        $ok = false;
        if (4 === \max($sequence, $maxSequence)) {
            //$maxKey = array_search($max, $values, true);
            //$this->pointSuit = $this->names[$maxKey];
            $ok = true;
        } elseif (14 === $value && 2 === $first && 3 === \max($sequence, $maxSequence) && \in_array(5, $values, true) && \in_array(4, $values, true) && \in_array(3, $values, true)) {
            //$this->pointSuit = '5';
            $ok = true;
        }

        return $ok;
    }

    /**
     * @param array<int> $suits
     * @param array<int> $values
     */
    private function isTwoPair(array $suits, array $values): bool
    {
        $suitNames = \array_keys(\array_count_values($values), 2);
        /*if (count($suitNames) === 2) {
            $n1 = array_search($suits[0], $values);
            $n2 = array_search($suits[1], $values);
            $max = max($values[$n1], $values[$n2]);
            $maxKey = array_search($max, $values);
            $this->pointSuit = $values[$maxKey];
        }*/

        return 2 === \count($suitNames);
    }

    /**
     * @param array<int> $values
     */
    private function isFullHouse(array $values): bool
    {
        $count = \array_count_values($values);
        $three = \array_search(3, $count, true);
        $two = \array_search(2, $count, true);
        //$this->pointSuit = $three;

        return false !== $three && false !== $two;
    }
}
