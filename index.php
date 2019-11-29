<?php

set_time_limit(0);

class Block
{
    public $index;
    public $timestamp;
    public $data;
    public $previousHash;
    public $hash;
    public $nonce;

    public function __construct($index, $timestamp, $data, $previousHash = '')
    {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->previousHash = $previousHash;
        $this->hash = 0;
        $this->nonce = 0;
    }

    public function calculateHash()
    {
        return hash_hmac("sha256", $this->index . $this->previousHash . $this->timestamp . json_encode($this->data) . $this->nonce, $this->previousHash);
    }

    public function mineBlock($difficulty)
    {
        $prefix = $this->changeDifficulty($difficulty);

        while (substr($this->hash, 0, $difficulty) !== $prefix) {
            $this->nonce++;
            $this->hash = $this->calculateHash();
        }
    }

    protected function changeDifficulty($level)
    {
        for ($i = 0; $i <= ($level); $i++) {
            $arr[] = '';
        }

        return implode('0', $arr);
    }
}

class Blockchain
{
    protected $chain;
    protected $difficulty = 6;

    public function __construct()
    {
        $this->chain[] = $this->createGenesisBlock();
    }

    public function createGenesisBlock()
    {
        return new Block(0, time(), "Genesis Block", "0");
    }

    public function getLatestBlock()
    {
        return $this->chain[sizeof($this->chain) - 1]->hash;
    }

    public function addBlock(Block $newBlock)
    {
        $newBlock->previousHash = $this->getLatestBlock();
        //$newBlock->hash = $newBlock->calculateHash();
        $newBlock->mineBlock($this->difficulty);
        $this->chain[] = $newBlock;
    }

    public function isChainValid()
    {
        for ($i = 1; $i <= sizeof($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i - 1];

            if ($currentBlock->hash !== $currentBlock->calculateHash()) {
                return false;
            }

            if ($currentBlock->previousHash !== $previousBlock->hash) {
                return false;
            }

            return true;
        }
    }
}

$coin = new Blockchain();
$coin->addBlock(new Block(1, time(), ['amount' => 1]));
$coin->addBlock(new Block(2, time(), ['amount' => 5]));
$coin->addBlock(new Block(3, time(), ['amount' => 8]));

echo "<pre>";

var_dump($coin);

var_dump($coin->isChainValid());