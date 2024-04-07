# Pokerino: a PHP poker library

[![License](http://poser.pugx.org/garak/pokerino/license)](https://packagist.org/packages/garak/pokerino) 
[![PHP Version Require](http://poser.pugx.org/garak/pokerino/require/php)](https://packagist.org/packages/garak/pokerino)
[![Maintainability](https://api.codeclimate.com/v1/badges/42795bd10fe0986138b5/maintainability)](https://codeclimate.com/github/garak/pokerino/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/42795bd10fe0986138b5/test_coverage)](https://codeclimate.com/github/garak/pokerino/test_coverage)

## Introduction

This library offers some objects useful for creating a Poker card game:

* Game _(to be extended)_
* Player _(to be extended)_
* Hand
* PokerRank

## Installation

Run `composer require garak/pokerino`.

## Usage

Here is an example of a game:

```php
<?php

require 'vendor/autoload.php';

use App\Game;   // this is your Game class, extending \Garak\Pokerino\Game
use App\Player;   // this is your Player class, extending \Garak\Pokerino\Player

$game = new Game();
$game->addPlayer(new Player('Marty McFly'));
$game->addPlayer(new Player('Biff Tannen'));
$game->addPlayer(new Player('Emmett Brown'));
$game->addPlayer(new Player('Jennifer Parker'));
$game->deal();  // deal 2 cards to each player
$game->hands(); // return an array of \Garak\Pokerino\Hand
```

## Credits

The original idea was developed with [davidino](https://github.com/davidino).
