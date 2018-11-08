<?php

namespace TRando\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * likes
 *
 * @ORM\Table(name="likes")
 * @ORM\Entity(repositoryClass="TRando\MainBundle\Repository\likesRepository")
 */
class likes
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
     *@ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="TRando\MainBundle\Entity\Posts")
     */
    private $posts;

    /**
     *
     *@ORM\JoinColumn(onDelete="CASCADE")
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
     * Set posts
     *
     * @param string $posts
     *
     * @return likes
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;

        return $this;
    }

    /**
     * Get posts
     *
     * @return string
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return likes
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

