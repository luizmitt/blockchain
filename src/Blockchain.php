<?php

namespace Lz\PHP;

use \Lz\PHP\Block;

class Blockchain
{
    protected $chain;
    protected $difficulty;

    public function __construct()
    {
        $this->chain[] = $this->createGenesisBlock();
        $this->difficulty = 2;
        $this->pendingTransactions = [];
        $this->miningReward = 100;
    }

    public function createGenesisBlock()
    {
        return new Block(0, time(), "Genesis Block", "0");
    }

    public function getLatestBlock()
    {
        return $this->chain[sizeof($this->chain) - 1]->hash;
    }

    public function minePendingTransactions($miningRewardAddress)
    {
        $block = new Block(time(), $this->pendingTransactions);
        $block->mineBlock($this->difficulty);

        $this->chain[] = $block;
        $this->pendingTransactions = [
            new Transaction(null, $miningRewardAddress, $this->miningReward)
        ];
    }

    public function createTransaction($transaction)
    {
        $this->pendingTransactions[] = $transaction;
    }

    public function getBalanceOfAddress($address)
    {
        $balance = 0;

        foreach ($this->chain as $index => $chain) {
            foreach ($chain->transaction as $index => $trans) {
                if ($trans->fromAddress == $address) {
                    $balance -= $trans->amount;
                }

                if ($trans->toAddress == $address) {
                    $balance += $trans->amout;
                }
            }
        }

        return $balance;
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