<?php

namespace Lz\PHP;

class Transaction
{
    public $from;
    public $to;
    public $amount;

    public function __construct($from, $to, $amount)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
    }

    public function calculateHash()
    {
        return hash_hmac("sha256", $this->from . $this->to, $this->amount, "");
    }

    public function signTransaction($signingKey)
    {
        $hashTx = $this->calculateHash();
        return base64_encode($hashTx);
    }

    public function isValid()
    {
        if ($this->from === null) {
            return true;
        }

        if (empty($this->signature)) {
            throw new Exception("No signature in this transaction");
        }

    }
}