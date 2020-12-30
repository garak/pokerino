<?php

namespace Pokerino;

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

    /**
     * See https://www.codeproject.com/Articles/569271/A-Poker-hand-analyzer-in-JavaScript-using-bit-math
     */
    public function getPoint(): string
    {
        $points = [
            '4 of a Kind',
            'Straight Flush',
            'Straight',
            'Flush',
            'High Card',
            '1 Pair',
            '2 Pair',
            'Royal Flush',
            '3 of a Kind',
            'Full House',
        ];

        $values = [];
        $suits = [];

        $value = 0;

        foreach ($this->cards as $card) {
            $values[] = $card->getValue()->getIntValue();
            $suits[] = $card->getSuit()->getIntValue();
        }

        $bit = 0;
        $noc = \count($this->cards);
        for ($i = 0; $i < $noc; ++$i) {
            $bit |= 1 << $values[$i];
        }

        for ($i = 0; $i < $noc; ++$i) {
            $offset = 2 ** ($values[$i] * 4);
            $value += $offset * (($value / $offset & 15) + 1);
        }
        $value = $value % 15 - (($bit / ($bit & -$bit) === 31) || ($bit === 0x403c) ? 3 : 1);
        $suitsBit = 0;
        for ($i = 0; $i < $noc; ++$i) {
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
