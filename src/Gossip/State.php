<?php

class State
{
    public $state;

    private $file;
    private $user;
    private $port;
    private $peerPort;
    private $sessions;

    public function __construct($user, $port = null, $peerPort = null)
    {
        $this->user = $user;
        $this->port = $port;
        $this->peerPort = $peerPort;
        $this->sessions = explode("\n", file_get_contents(__DIR__ . '/data/sessions.txt'));
        $this->file = __DIR__ . "/data/{$user}.json";

        if ($this->peerPort && !isset($this->state[$this->peerPort])) {
            $this->state[$this->peerPort] = ['user' => '', 'sessions' => '', 'version' => ''];
        }

        if ($this->port && !isset($this->state[$this->port])) {
            $this->updateMine();
        }

        $this->reload();
    }

    public function loop()
    {
        $i = 0;
        while (true) {
            printf("\033[31;40m Current state \033[39;49\n%s\n", $this);

            foreach ($this->state as $p => $data) {
                if ($p == $this->port) {
                    continue;
                }

                $data = json_encode($this->state);
                $peerState = @file_get_contents("http://localhost:{$p}/gossip", null, [
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-Type: application/json\r\nContent-length: ".strlen($data),
                        'content' => $data,
                    ]
                ]);

                if (!$peerState) {
                    unset($this->state[$p]);
                    $this->save();
                } else {
                    $this->update(json_decode($peerState, true));
                }

                $this->reload();
                usleep(rand(300000, 300000));
                if (++$i % 2) {
                    $this->updateMine();
                    printf("\033[37;40m Fav session updated \033[39;49m\n");
                }
            }
        }
    }

    public function reload()
    {
        $this->state = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
    }

    public function updateMine()
    {
        $session = $this->randomSession();
        $version = $this->incrementVersion();
        $this->state[$this->port] = ['user' => $this->user, 'session' => $session, 'version' => $version];
        $this->save();
    }

    public function update($state)
    {
        if (!$state) {
            return;
        }

        foreach ($state as $port => $data) {
            if ($port == $this->port) {
                continue;
            }

            if (!isset($data['user']) || !isset($data['version']) || !isset($data['session'])) {
                continue;
            }

            if (!isset($this->state[$port]) || $data['version'] > $this->state[$port]['version']) {
                $this->state[$port] = $data;
            }
        }

        $this->save();
    }

    public function __toString()
    {
        $data = [];
        foreach ($this->state as $port => $d) {
            $data[] = sprintf("%s/%s -- %d/", $port, $d['user'], $d['version']);
        }
    }

    public function randomSession()
    {

    }

    public function incrementVersion()
    {

    }

    public function save()
    {
        
    }
}