<?php

require_once 'vendor/autoload.php';

$deck = new Pokerino\Deck();
$deck->shuffle();

#foreach ($deck->getCards() as $card) {
#    echo PHP_EOL.$card;
#}

echo PHP_EOL.$deck->draw();
echo PHP_EOL.$deck->draw();
echo PHP_EOL.$deck->draw();
echo PHP_EOL.$deck->draw();
echo PHP_EOL.$deck->draw();

echo PHP_EOL;
