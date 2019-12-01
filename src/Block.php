<?php

namespace Lz\PHP;

class Block
{
    public $index;
    public $timestamp;
    public $transactions;
    public $previousHash;
    public $hash;
    public $nonce;

    public function __construct($timestamp, $transactions, $previousHash = '')
    {
        $this->timestamp = $timestamp;
        $this->transactions = $transactions;
        $this->previousHash = $previousHash;
        $this->hash = 0;
        $this->nonce = 0;
    }

    public function calculateHash()
    {
        return hash_hmac("sha256", $this->previousHash . $this->timestamp . json_encode($this->transactions) . $this->nonce, $this->previousHash);
    }

    public function mineBlock($difficulty)
    {
        while (substr($this->hash, 0, $difficulty) !== $this->changeDifficulty($difficulty)) {
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