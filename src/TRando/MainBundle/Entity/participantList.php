<?php

namespace TRando\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * participantList
 *
 * @ORM\Table(name="participant_list")
 * @ORM\Entity(repositoryClass="TRando\MainBundle\Repository\participantListRepository")
 */
class participantList
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
     * @ORM\ManyToOne(targetEntity="TRando\MainBundle\Entity\Event")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="TRando\MainBundle\Entity\User")
     */
    private $participant;


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
     * Set event
     *
     * @param string $event
     *
     * @return participantList
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set participant
     *
     * @param string $participant
     *
     * @return participantList
     */
    public function setParticipant($participant)
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * Get participant
     *
     * @return string
     */
    public function getParticipant()
    {
        return $this->participant;
    }
}

