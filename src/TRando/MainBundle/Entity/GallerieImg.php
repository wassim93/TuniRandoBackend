<?php
/**
 * Created by PhpStorm.
 * User: wassim
 * Date: 23/12/2017
 * Time: 15:37
 */

namespace TRando\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
*
* @ORM\Table(name="Gallerie")
* @ORM\Entity()
*/
class GallerieImg
{    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id ;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255)
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string",length=255)
     */
    private $url;

}