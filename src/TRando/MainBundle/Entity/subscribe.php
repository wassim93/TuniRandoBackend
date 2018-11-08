<?php

namespace TRando\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * subscribe
 *
 * @ORM\Table(name="subscribe")
 * @ORM\Entity(repositoryClass="TRando\MainBundle\Repository\subscribeRepository")
 */
class subscribe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     *@ORM\ManyToOne(targetEntity="TRando\MainBundle\Entity\User")
     */
    private $subscribeTo;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="TRando\MainBundle\Entity\User")
     */
    private $user;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subscribeTo
     *
     * @param string $subscribeTo
     *
     * @return subscribe
     */
    public function setSubscribeTo($subscribeTo)
    {
        $this->subscribeTo = $subscribeTo;

        return $this;
    }

    /**
     * Get subscribeTo
     *
     * @return string
     */
    public function getSubscribeTo()
    {
        return $this->subscribeTo;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return subscribe
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }
}

