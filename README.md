# Pokerino: a PHP poker library

[![License](http://poser.pugx.org/garak/pokerino/license)](https://packagist.org/packages/garak/pokerino) 
[![PHP Version Require](http://poser.pugx.org/garak/pokerino/require/php)](https://packagist.org/packages/garak/pokerino)
[![Maintainability](https://qlty.sh/gh/garak/projects/pokerino/maintainability.svg)](https://qlty.sh/gh/garak/projects/pokerino)
[![Code Coverage](https://qlty.sh/gh/garak/projects/pokerino/coverage.svg)](https://qlty.sh/gh/garak/projects/pokerino)

## Introduction

This library offers PHP classes for building a Poker card game:

* `Game` _(abstract, needs to be extended)_
* `Player` _(abstract, needs to be extended)_
* `Hand` — evaluates a poker hand
* `PokerRank` — identifies the best hand from a set of cards

`Hand` and `PokerRank` work with any number of cards and are not tied to a specific poker variant. `Game::deal()` is configurable for different dealing styles (see Usage). `Game::winner()` assumes a community-card structure (e.g. Texas Hold'em, Omaha) and should be overridden for variants without community cards (e.g. 5-Card Draw, 7-Card Stud).

### Supported hand rankings (highest to lowest)

| Rank | Name            |
|------|-----------------|
| 1    | Royal Flush     |
| 2    | Straight Flush  |
| 3    | Four of a Kind  |
| 4    | Full House      |
| 5    | Flush           |
| 6    | Straight        |
| 7    | Three of a Kind |
| 8    | Two Pair        |
| 9    | Pair            |
| 10   | High Card       |

## Installation

Run `composer require garak/pokerino`.

## Usage

### Texas Hold'em example

```php
<?php

require 'vendor/autoload.php';

use App\Game;    // your Game class, extending \Garak\Pokerino\Game
use App\Player;  // your Player class, extending \Garak\Pokerino\Player

$game = new Game();
$game->join(new Player('Marty McFly'));
$game->join(new Player('Biff Tannen'));
$game->join(new Player('Emmett Brown'));

// Deal 2 hole cards to each player + 5 community cards (Texas Hold'em defaults)
$game->deal();

// Retrieve all hands (player hands first, community hand last)
$hands = $game->getHands();

// Evaluate a single hand
$hand = $hands[0];
echo $hand->getPoint();          // e.g. "Flush"
echo $hand->getHigh();           // highest relevant card
echo $hand->getKicker();         // best side card
echo $hand->getHandStrength();   // numeric rank: 1 (Royal Flush) … 10 (High Card)

// Determine the winner (combines each player's hole cards with community cards)
$winner = $game->winner();
echo $winner; // player name
```

### Other variants

`deal()` accepts custom parameters to support different dealing styles:

```php
$game->deal(5, 0);  // 5-Card Draw: 5 hole cards, no community cards
$game->deal(4, 5);  // Omaha: 4 hole cards + 5 community cards
$game->deal(2, 5);  // Texas Hold'em (default)
```

> **Note:** `winner()` assumes a community-card structure. For variants without community cards, override it in your `Game` subclass.

### Evaluating a hand directly

```php
use Garak\Card\Card;
use Garak\Pokerino\Hand;

$cards = [
    Card::fromRankSuit('Ac'),
    Card::fromRankSuit('Kc'),
    Card::fromRankSuit('Qc'),
    Card::fromRankSuit('Jc'),
    Card::fromRankSuit('Tc'),
    Card::fromRankSuit('2s'),
    Card::fromRankSuit('7d'),
];

$hand = new Hand($cards);
echo $hand->getPoint();        // "Royal Flush"
echo $hand->getHandStrength(); // 1
```

## Credits

The original idea was developed with [davidino](https://github.com/davidino).
