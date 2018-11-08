<?php
/**
 * Created by PhpStorm.
 * User: wassim
 * Date: 24/11/2017
 * Time: 23:40
 */

namespace TRando\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;



/**
 *
 * @ORM\Table(name="eventImage")
 * @ORM\Entity()

 */
class EventImage
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id ;
    /**
     *@ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Event" , inversedBy="image")
     */
    private $event;
    /**
     * @var string
     *
     * @ORM\Column(name="Image", type="text",length=65550)
     */
    private $image;

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
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
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


  

  


}