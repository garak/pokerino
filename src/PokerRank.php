<?php

namespace Garak\Pokerino;

use Garak\Card\Card;

final class PokerRank
{
    private ?Card $high = null;

    private ?Card $kicker = null;

    /**
     * @var array<int, string>
     */
    private array $points = [
        '4 of a Kind',
        'Royal Flush',
        'Straight Flush',
        'Straight',
        'Full House',
        '3 of a Kind',
        '2 Pair',
        'Flush',
        '1 Pair',
        'High Card',
    ];

    /**
     * @param array<Card> $cards
     */
    public function __construct(private array $cards)
    {
    }

    public function getPoint(): string
    {
        $callbacks = [
            static fn (array $cards): Rank\RankResult => Rank\FourOfAKindRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\RoyalFlushRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\StraightFlushRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\StraightRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\FullHouseRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\ThreeOfAKindRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\TwoPairRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\FlushRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\PairRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\HighCardRank::isPoint($cards),
        ];

        foreach ($this->points as $key => $rankName) {
            $rank = $callbacks[$key]($this->cards);
            if ($rank->isPoint()) {
                $this->high = $rank->getHigh();
                $this->kicker = $rank->getKicker();

                return $this->points[$key];
            }
        }

        throw new \UnexpectedValueException('This point should never be reached.');
    }

    public function getHigh(): ?Card
    {
        return $this->high;
    }

    public function getKicker(): ?Card
    {
        return $this->kicker;
    }
}
