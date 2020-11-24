<?php


namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DealVoteEvent extends Event
{
    public const NAME = 'deal.vote';
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
