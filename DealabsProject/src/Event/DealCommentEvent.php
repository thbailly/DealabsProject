<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DealCommentEvent extends Event
{
    public const NAME = 'deal.post';
    private $user;
    private $nbCom = 0;

    public function __construct($user, $nbCom)
    {
        $this->user = $user;
        $this->nbCom = $nbCom;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getNbCom(): int
    {
        return $this->nbCom;
    }
}
