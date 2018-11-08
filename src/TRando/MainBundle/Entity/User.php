<?php
namespace  TRando\MainBundle\Entity ;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table("users")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected  $activationToken="default" ;
    /**
     * @ORM\Column(type="string",name="fisrt_name")
     */
    protected $FisrtName="default";
    /**
     * @ORM\Column(type="string",name="last_name")
     */
    protected $LastName="default";
    /**
     * @ORM\Column(type="string",name="$PhoneNumber")
     */
    protected $PhoneNumber="default";
    /**
     * @ORM\Column(type="integer",name="$subscribenumber")
     */
    protected $subscribenumber=0;
    /**
     * @ORM\Column(type="string",name="address")
     */
    protected $Address="default";
    /**
 * @ORM\Column(type="string",name="profile_pic_url")
 */
    protected $ProfilePicUrl="default";
    /**
     * @ORM\Column(type="string",name="background_pic_url")
     */
    protected $BackgroundPicUrl="default";
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getActivationToken()
    {
        return $this->activationToken;
    }

    /**
     * @param mixed $activationToken
     */
    public function setActivationToken($activationToken)
    {
        $this->activationToken = $activationToken;
    }

    /**
     * @return mixed
     */
    public function getFisrtName()
    {
        return $this->FisrtName;
    }

    /**
     * @param mixed $FisrtName
     */
    public function setFisrtName($FisrtName)
    {
        $this->FisrtName = $FisrtName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->LastName;
    }

    /**
     * @param mixed $LastName
     */
    public function setLastName($LastName)
    {
        $this->LastName = $LastName;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->Address;
    }

    /**
     * @param mixed $Address
     */
    public function setAddress($Address)
    {
        $this->Address = $Address;
    }

    /**
     * @return mixed
     */
    public function getProfilePicUrl()
    {
        return $this->ProfilePicUrl;
    }

    /**
     * @param mixed $ProfilePicUrl
     */
    public function setProfilePicUrl($ProfilePicUrl)
    {
        $this->ProfilePicUrl = $ProfilePicUrl;
    }

    /**
     * @return mixed
     */
    public function getBackgroundPicUrl()
    {
        return $this->BackgroundPicUrl;
    }

    /**
     * @param mixed $BackgroundPicUrl
     */
    public function setBackgroundPicUrl($BackgroundPicUrl)
    {
        $this->BackgroundPicUrl = $BackgroundPicUrl;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->PhoneNumber;
    }

    /**
     * @param mixed $PhoneNumber
     */
    public function setPhoneNumber($PhoneNumber)
    {
        $this->PhoneNumber = $PhoneNumber;
    }

    /**
     * @return mixed
     */
    public function getSubscribenumber()
    {
        return $this->subscribenumber;
    }

    /**
     * @param mixed $subscribenumber
     */
    public function setSubscribenumber($subscribenumber)
    {
        $this->subscribenumber = $subscribenumber;
    }

}