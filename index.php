<?php

set_time_limit(0);

require_once __DIR__ . '/src/Block.php';
require_once __DIR__ . '/src/Blockchain.php';
require_once __DIR__ . '/src/Transaction.php';

use \Lz\PHP\Blockchain;
use \Lz\PHP\Transaction;

$coin = new Blockchain();
// $coin->addBlock(new Block(1, time(), ['amount' => 1]));
// $coin->addBlock(new Block(2, time(), ['amount' => 5]));
// $coin->addBlock(new Block(3, time(), ['amount' => 8]));

// echo "<pre>";

// var_dump($coin);

// var_dump($coin->isChainValid());

$coin->createTransaction(new Transaction('add1', 'add2', 100));
$coin->createTransaction(new Transaction('add2', 'add1', 50));

$coin->minePendingTransactions('miner-address');

var_dump($coin);