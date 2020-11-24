<?php

namespace App\Entity;

use App\Repository\DealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DealRepository::class)
 * @ORM\Table(name="deal")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap( {"plan" = "Plan", "promocode" = "PromoCode"} )
 */
abstract class Deal
{
    public const TYPE_NAME = 'Deal';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $rateValue;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, cascade={"persist","remove"}, mappedBy="deal")
     */
    private $comments;

    /**
     * @ORM\Column(type="integer")
     */
    private $commentCount;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, inversedBy="deals")
     */
    private $groups;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="savedDeals")
     */
    private $userSave;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $author;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->userSave = new ArrayCollection();
        $this->setCommentCount(0);
        $this->setRateValue(0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link): void
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getRateValue()
    {
        return $this->rateValue;
    }

    /**
     * @param mixed $rateValue
     */
    public function setRateValue($rateValue): void
    {
        $this->rateValue = $rateValue;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    }

    /**
     * @param mixed $commentCount
     */
    public function setCommentCount($commentCount): void
    {
        $this->commentCount = $commentCount;
    }

    public function addComments(Comment $comment)
    {
        $this->comments->add($comment);
        $comment->setDeal($this);
        $this->setCommentCount($this->getCommentCount()+1);
    }

    public function incrementRateValue()
    {
        $this->setRateValue($this->getRateValue() +1);
    }

    public function decrementRateValue()
    {
        $this->setRateValue($this->getRateValue() -1);
    }

    public function addGroup(Group $group)
    {
        $this->groups->add($group);
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function getGroupsId()
    {
        $res = new ArrayCollection();
        foreach ($this->groups as $group) {
            $res->add($group->getId());
        }
        return $res;
    }

    /**
     * @param ArrayCollection $groups
     */
    public function setGroups(ArrayCollection $groups)
    {
        $this->groups = $groups;
    }

    public function addToUserSave(User $user)
    {
        $this->userSave->add($user);
    }

    public function getUsersSaveId()
    {
        $res = new ArrayCollection();
        foreach ($this->userSave as $user) {
            $res->add($user->getId());
        }
        return $res;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
