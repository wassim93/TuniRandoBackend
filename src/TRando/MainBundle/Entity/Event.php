<?php

namespace TRando\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="TRando\MainBundle\Repository\EventRepository")
 */
class Event
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
     * @ORM\Column(name="Title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="Contact", type="string", length=255)
     */
    private $contact;

    /**
     * @var string
     *
     * @ORM\Column(name="Date", type="string", length=255)
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="Prix", type="float")
     */
    private $prix;

    /**
     * @var string
     * @ORM\OneToMany(targetEntity="TRando\MainBundle\Entity\EventImage" , mappedBy="event")
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="Depart", type="string",length=255)
     */
    private $pointDepart;

    /**
     * @var string
     *
     * @ORM\Column(name="Arrive", type="string",length=255)
     */
    private $pointArrive;

    /**
     * @var string
     *
     * @ORM\Column(name="Type", type="string",length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="Niveau", type="string",length=255)
     */
    private $niveau;

    /**
     * @var integer
     *
     * @ORM\Column(name="NombreDePlace", type="integer")
     */
    private $NbrPlaces;
    /**
     * @ORM\ManyToOne(targetEntity="TRando\MainBundle\Entity\User")
     */
    private $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param string $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * @param float $prix
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getPointDepart()
    {
        return $this->pointDepart;
    }

    /**
     * @param string $pointDepart
     */
    public function setPointDepart($pointDepart)
    {
        $this->pointDepart = $pointDepart;
    }

    /**
     * @return string
     */
    public function getPointArrive()
    {
        return $this->pointArrive;
    }

    /**
     * @param string $pointArrive
     */
    public function setPointArrive($pointArrive)
    {
        $this->pointArrive = $pointArrive;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * @param string $niveau
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;
    }

    /**
     * @return int
     */
    public function getNbrPlaces()
    {
        return $this->NbrPlaces;
    }

    /**
     * @param int $NbrPlaces
     */
    public function setNbrPlaces($NbrPlaces)
    {
        $this->NbrPlaces = $NbrPlaces;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }





}

