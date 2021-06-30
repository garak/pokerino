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
        $value = 0;

        foreach ($this->cards as $card) {
            $values[] = $card->getRank()->getInt();
            $suits[] = $card->getSuit()->getInt();
        }

        $bit = 0;
        $numberOfCards = \count($this->cards);
        for ($i = 0; $i < $numberOfCards; ++$i) {
            $bit |= 1 << $values[$i];
        }

        for ($i = 0; $i < $numberOfCards; ++$i) {
            $offset = 2 ** ($values[$i] * 4);
            $value += $offset * (($value / $offset & 15) + 1);
        }
        $value = $value % 15 - (($bit / ($bit & -$bit) === 31) || ($bit === 0x403c) ? 3 : 1);
        $suitsBit = 0;
        for ($i = 0; $i < $numberOfCards; ++$i) {
            $suitsBit |= $suits[$i];
        }

        $value -= ($suits[0] === $suitsBit) * (($bit === 0x7c00) ? -5 : 1);

        return $points[$value];
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
}
