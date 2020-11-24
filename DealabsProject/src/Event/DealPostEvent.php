<?php


namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DealPostEvent extends Event
{
    public const NAME = 'deal.post';
    private $user;
    private $nbpost = 0;

    public function __construct($user, $nbpost)
    {
        $this->user = $user;
        $this->nbpost = $nbpost;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getNbpost(): int
    {
        return $this->nbpost;
    }
}
