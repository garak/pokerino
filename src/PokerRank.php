<?php

namespace Pokerino;

use Garak\Card\Card;

final class PokerRank
{
    /** @var array<Card> */
    private array $cards;

    private ?Card $high = null;

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

        if ($this->isFourOfAKind()) {
            return $points[0];
        }

        if ($this->isStraight()) {
            return $this->isFlush(false) ? $points[7] : $points[2];
        }

        if ($this->isFullHouse()) {
            return $points[9];
        }

        if ($this->isThreeOfAKind()) {
            return $points[8];
        }

        if ($this->isTwoPair()) {
            return $points[6];
        }

        if ($this->isFlush()) {
            return $points[3];
        }

        if ($this->isPair()) {
            return $points[5];
        }
        CardSorter::sort($this->cards);
        $this->high = $this->cards[0];
        $this->kicker = $this->cards[1];

        return $points[4];
    }

    /**
     * @return array<Card>
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function getHigh(): ?Card
    {
        return $this->high;
    }

    public function getKicker(): ?Card
    {
        return $this->kicker;
    }

    private function isFlush(bool $checkForHigh = true): bool
    {
        $counts = \array_count_values($this->getCardsSuits());
        if (!\in_array(5, $counts, true)) {
            return false;
        }
        if ($checkForHigh) {
            CardSorter::sort($this->cards);
            foreach ($this->cards as $card) {
                $suitVal = $card->getSuit()->getInt();
                if (5 === $counts[$suitVal]) {
                    $this->high = $card;
                    break;
                }
            }
            foreach ($this->cards as $card) {
                $suitVal = $card->getSuit()->getInt();
                if (5 !== $counts[$suitVal]) {
                    $this->kicker = $card;
                    break;
                }
            }
        }

        return true;
    }

    private function isFourOfAKind(): bool
    {
        $count = \array_count_values($this->getCardsValues());
        if (!\in_array(4, $count, true)) {
            return false;
        }
        $value = \array_search(4, $count, true);
        CardSorter::sort($this->cards);
        foreach ($this->cards as $card) {
            if ($card->getRank()->getInt() === $value) {
                $this->high = $card;
                break;
            }
        }
        foreach ($this->cards as $card) {
            if ($card->getRank()->getInt() !== $value) {
                $this->kicker = $card;
                break;
            }
        }

        return true;
    }

    /**
     * @param array<int, Card>|null $cards
     */
    private function isThreeOfAKind(?array $cards = null): bool
    {
        $cards = $cards ?? $this->cards;
        $counts = \array_count_values($this->getCardsValues($cards));
        if (!\in_array(3, $counts, true)) {
            return false;
        }
        CardSorter::sort($cards);
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (3 === $counts[$rank]) {
                $this->high = $card;
                break;
            }
        }
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (3 !== $counts[$rank]) {
                $this->kicker = $card;
                break;
            }
        }

        return true;
    }

    /**
     * @param array<int, Card> $cards
     */
    private function removeDuplicated(array &$cards): void
    {
        $counts = \array_count_values($this->getCardsValues($cards));
        foreach ($cards as $key => $card) {
            $rank = $card->getRank()->getInt();
            if ($counts[$rank] > 1) {
                unset($cards[$key]);
                break;
            }
        }
    }

    private function isStraight(): bool
    {
        $cards = $this->cards;
        // check if there's a three: in this case, we remove one card
        if ($this->isThreeOfAKind($cards)) {
            $this->removeDuplicated($cards);
        }
        // there's stil a pair: we need to remove one more card
        if ($this->isPair($cards)) {
            $this->removeDuplicated($cards);
        }
        // sorting is not working in case of pair
        CardSorter::sort($cards, true);
        $values = $this->getCardsValues($cards);
        $first = \current($values);
        $sequence = 0;
        $maxSequence = 0;
        $prev = null;
        $max = null;
        $value = null;
        $cardsInStraight = [];

        foreach ($values as $key => $value) {
            if (null !== $prev && 1 === $value - $prev) {
                $max = $value;
                ++$sequence;
            } else {
                if ($sequence > $maxSequence) {
                    $maxSequence = $sequence;
                }
                $sequence = 0;
                $cardsInStraight = [];
            }
            $prev = $value;
            $cardsInStraight[] = $cards[$key];
            if (\count($cardsInStraight) >= 5) {
                break;
            }
        }

        if (4 === \max($sequence, $maxSequence)) {
            $maxKey = \array_search($max, $values, true);
            foreach ($cards as $card) {
                if ($card->getRank()->getInt() === $values[$maxKey]) {
                    $this->high = $card;
                    break;
                }
            }
            CardSorter::sort($cards);
            foreach ($cards as $card) {
                if (!\in_array($card, $cardsInStraight, true)) {
                    $this->kicker = $card;
                    break;
                }
            }

            return true;
        }
        // straight with ace in first position
        if (14 === $value && 2 === $first && 3 === \max($sequence, $maxSequence) && \in_array(5, $values, true) && \in_array(4, $values, true) && \in_array(3, $values, true)) {
            foreach ($cards as $card) {
                if (5 === $card->getRank()->getInt()) { // A, 2, 3, 4, 5
                    $this->high = $card;
                    break;
                }
            }
            // kicker is the fifth card, because the ace is ranked as higher (so it's at the end)
            $this->kicker = $cards[4];

            return true;
        }

        return false;
    }

    /**
     * @param array<int, Card>|null $cards
     */
    private function isPair(?array $cards = null): bool
    {
        $cards = $cards ?? $this->cards;
        $counts = \array_count_values($this->getCardsValues($cards));
        if (!\in_array(2, $counts, true)) {
            return false;
        }
        CardSorter::sort($cards);
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (2 === $counts[$rank]) {
                $this->high = $card;
                break;
            }
        }
        foreach ($cards as $card) {
            $rank = $card->getRank()->getInt();
            if (2 !== $counts[$rank]) {
                $this->kicker = $card;
                break;
            }
        }

        return true;
    }

    private function isTwoPair(): bool
    {
        $counts = \array_count_values($this->getCardsValues());
        $suitNames = \array_keys($counts, 2);
        if (2 !== \count($suitNames)) {
            return false;
        }
        CardSorter::sort($this->cards);
        foreach ($this->cards as $card) {
            $rank = $card->getRank()->getInt();
            if (2 === $counts[$rank]) {
                $this->high = $card;
                break;
            }
        }
        foreach ($this->cards as $card) {
            $rank = $card->getRank()->getInt();
            if (2 !== $counts[$rank]) {
                $this->kicker = $card;
                break;
            }
        }

        return true;
    }

    private function isFullHouse(): bool
    {
        $values = $this->getCardsValues();
        $counts = \array_count_values($values);
        $three = \array_search(3, $counts, true);
        $two = \array_search(2, $counts, true);
        if (false === $three || false === $two) {
            return false;
        }
        CardSorter::sort($this->cards);
        foreach ($this->cards as $card) {
            $rank = $card->getRank()->getInt();
            if (3 === $counts[$rank] || 2 === $counts[$rank]) {
                $this->high = $card;
                break;
            }
        }
        foreach ($this->cards as $card) {
            $rank = $card->getRank()->getInt();
            if (2 !== $counts[$rank] && 3 !== $counts[$rank]) {
                $this->kicker = $card;
                break;
            }
        }

        return true;
    }

    /**
     * @return array<int, int>
     */
    private function getCardsSuits(): array
    {
        return \array_map(static fn (Card $card): int => $card->getSuit()->getInt(), $this->cards);
    }

    /**
     * @param array<int, Card>|null $cards
     *
     * @return array<int, int>
     */
    private function getCardsValues(?array $cards = null): array
    {
        return \array_map(static fn (Card $card): int => $card->getRank()->getInt(), $cards ?? $this->cards);
    }
}
