<?php

namespace Garak\Pokerino;

use Garak\Card\Card;

final class PokerRank
{
    private ?Card $high = null;

    private ?Card $kicker = null;

    /** @var int[] */
    private array $tieBreak = [];
    private ?string $currentPoint = null;

    /**
     * Standard poker hand ranking (index = strength, lower = stronger).
     *
     * @var array<int, string>
     */
    private array $points = [
        'Royal Flush',
        'Straight Flush',
        '4 of a Kind',
        'Full House',
        'Flush',
        'Straight',
        '3 of a Kind',
        '2 Pair',
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
            static fn (array $cards): Rank\RankResult => Rank\RoyalFlushRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\StraightFlushRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\FourOfAKindRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\FullHouseRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\FlushRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\StraightRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\ThreeOfAKindRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\TwoPairRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\PairRank::isPoint($cards),
            static fn (array $cards): Rank\RankResult => Rank\HighCardRank::isPoint($cards),
        ];

        foreach ($this->points as $key => $rankName) {
            $rank = $callbacks[$key]($this->cards);
            if ($rank->isPoint()) {
                $this->high = $rank->getHigh();
                $this->kicker = $rank->getKicker();
                $this->currentPoint = $this->points[$key];

                // compute tie-break vector for accurate comparisons
                $this->tieBreak = $this->computeTieBreak($this->currentPoint);

                return $this->currentPoint;
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

    /**
     * Returns the tie-break vector (ordered list of ranks used to compare hands
     * that have the same category). Lower indices are more significant.
     * Example: Two Pair => [higherPair, lowerPair, kicker].
     *
     * @return int[]
     */
    public function getTieBreak(): array
    {
        return $this->tieBreak;
    }

    /**
     * Compute tie-break vector based on currentPoint and the cards.
     * Uses CardSorter and rank integers.
     *
     * @return int[]
     */
    private function computeTieBreak(string $point): array
    {
        // helper closures
        $values = \array_map(static fn (Card $c): int => $c->getRank()->getInt(), $this->cards);
        // counts map rank => count
        $counts = [];
        foreach ($values as $v) {
            $counts[$v] = ($counts[$v] ?? 0) + 1;
        }
        // sort values desc unique
        $unique = \array_values(\array_unique($values));
        \rsort($unique);

        // group cards by suit
        $suits = [];
        foreach ($this->cards as $card) {
            $s = $card->getSuit()->getInt();
            $suits[$s][] = $card;
        }

        // helper to get highest straight from a set of ranks (ints)
        $getStraightHigh = static function (array $ranks): ?int {
            $ranks = \array_values(\array_unique($ranks));
            \sort($ranks);
            // normal straight detection
            $best = null;
            $seq = 1;
            $prev = null;
            for ($i = 0, $n = \count($ranks); $i < $n; ++$i) {
                $v = $ranks[$i];
                if ($i > 0 && $v === $prev + 1) {
                    ++$seq;
                } else {
                    $seq = 1;
                }
                if ($seq >= 5) {
                    $best = $v;
                }
                $prev = $v;
            }
            // check wheel A-2-3-4-5
            if (\in_array(14, $ranks, true) && \in_array(2, $ranks, true) && \in_array(3, $ranks, true) && \in_array(4, $ranks, true) && \in_array(5, $ranks, true)) {
                $best = \max($best ?? 0, 5);
            }

            return $best;
        };

        switch ($point) {
            case 'Royal Flush':
                return [14, 13, 12, 11, 10];

            case 'Straight Flush':
                // for each suit, find straight high; choose best
                $bestHigh = 0;
                foreach ($suits as $s => $cards) {
                    if (\count($cards) < 5) {
                        continue;
                    }
                    $r = \array_map(static fn (Card $c): int => $c->getRank()->getInt(), $cards);
                    $h = $getStraightHigh($r);
                    if (null !== $h && $h > $bestHigh) {
                        $bestHigh = $h;
                    }
                }
                if ($bestHigh > 0) {
                    return [$bestHigh];
                }

                return [];

            case '4 of a Kind':
                $quad = null;
                foreach ($counts as $rank => $c) {
                    if (4 === $c) {
                        $quad = $rank;
                        break;
                    }
                }
                // kicker is highest other
                $kicker = 0;
                foreach ($unique as $r) {
                    if ($r !== $quad) {
                        $kicker = $r;
                        break;
                    }
                }
                if (null !== $quad) {
                    return [$quad, $kicker];
                }

                return [];

            case 'Full House':
                $trip = 0;
                $pair = 0;
                // find highest trip
                foreach ($unique as $r) {
                    if (($counts[$r] ?? 0) >= 3) {
                        $trip = $r;
                        break;
                    }
                }
                // find highest pair not the trip
                foreach ($unique as $r) {
                    if ($r === $trip) {
                        continue;
                    }
                    if (($counts[$r] ?? 0) >= 2) {
                        $pair = $r;
                        break;
                    }
                }
                if ($trip > 0) {
                    return [$trip, $pair];
                }

                return [];

            case 'Flush':
                // for each suit with >=5, get top5 ranks and pick lexicographically best
                $best = [];
                foreach ($suits as $s => $cards) {
                    if (\count($cards) < 5) {
                        continue;
                    }
                    \usort($cards, static fn (Card $a, Card $b): int => $b->getRank()->getInt() <=> $a->getRank()->getInt());
                    $top5 = \array_map(static fn (Card $c): int => $c->getRank()->getInt(), \array_slice($cards, 0, 5));
                    if ([] === $best) {
                        $best = $top5;
                        continue;
                    }
                    // lexicographic compare
                    for ($i = 0; $i < 5; ++$i) {
                        if ($top5[$i] > $best[$i]) {
                            $best = $top5;
                            break;
                        }
                        if ($top5[$i] < $best[$i]) {
                            break;
                        }
                    }
                }

                return $best;

            case 'Straight':
                $h = $getStraightHigh($values);
                if (null !== $h && $h > 0) {
                    return [$h];
                }

                return [];

            case '3 of a Kind':
                $trip = 0;
                foreach ($unique as $r) {
                    if (($counts[$r] ?? 0) === 3) {
                        $trip = $r;
                        break;
                    }
                }
                $kickers = [];
                foreach ($unique as $r) {
                    if ($r === $trip) {
                        continue;
                    }
                    $kickers[] = $r;
                    if (\count($kickers) >= 2) {
                        break;
                    }
                }
                if ($trip > 0) {
                    return \array_merge([$trip], $kickers);
                }

                return [];

            case '2 Pair':
                $pairs = [];
                foreach ($unique as $r) {
                    if (($counts[$r] ?? 0) >= 2) {
                        $pairs[] = $r;
                    }
                }
                if (\count($pairs) < 2) {
                    return [];
                }
                $kicker = 0;
                foreach ($unique as $r) {
                    if (!\in_array($r, $pairs, true)) {
                        $kicker = $r;
                        break;
                    }
                }

                return [$pairs[0], $pairs[1], $kicker];

            case '1 Pair':
                // ensure single pair
                $pairRank = 0;
                foreach ($unique as $r) {
                    if (($counts[$r] ?? 0) >= 2) {
                        $pairRank = $r;
                        break;
                    }
                }
                $kickers = [];
                foreach ($unique as $r) {
                    if ($r === $pairRank) {
                        continue;
                    }
                    $kickers[] = $r;
                    if (\count($kickers) >= 3) {
                        break;
                    }
                }
                if ($pairRank > 0) {
                    return \array_merge([$pairRank], $kickers);
                }

                return [];

            case 'High Card':
            default:
                $top = \array_slice($unique, 0, 5);

                return $top;
        }
    }

    /**
     * Returns a numeric strength for the current hand (call getPoint() first, or it will be evaluated now).
     * Lower value = stronger hand: 1 = Royal Flush … 10 = High Card.
     */
    public function getHandStrength(): int
    {
        if (null === $this->currentPoint) {
            $this->getPoint();
        }

        return (int) \array_search($this->currentPoint, $this->points, true) + 1;
    }
}
