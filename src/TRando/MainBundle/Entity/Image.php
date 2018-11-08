<?php
/**
 * Created by PhpStorm.
 * User: wassim
 * Date: 02/12/2017
 * Time: 15:30
 */

namespace TRando\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="Image")
 * @ORM\Entity()
 */
class Image
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
     * @ORM\ManyToOne(targetEntity="TRando\MainBundle\Entity\Produit" , inversedBy="images")
     */
    private $produit;
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
    public function getProduit()
    {
        return $this->produit;
    }

    /**
     * @param mixed $produit
     */
    public function setProduit($produit)
    {
        $this->produit = $produit;
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