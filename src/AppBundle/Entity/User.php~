<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message="用户名不能为空"
     * )
     * @Assert\Length(
     *     min=4,
     *     max=40,
     *     minMessage="用户名长度需大于3",
     *     maxMessage="用户名长度需小于40"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message="密码不能为空"
     * )
     * @Assert\Length(
     *     min=8,
     *     max=20,
     *     minMessage="密码长度至少8位",
     *     maxMessage="密码不得超过20位"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message="手机号码不能为空"
     * )
     * @Assert\Regex(
     *     pattern="/^1\d{10}$/",
     *     message="手机号码格式不正确"
     * )
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message="邮箱不能为空"
     * )
     * @Assert\Regex(
     *     pattern="/^[\w]+@[\w]+\.(com|net|cn)+$/",
     *     message="邮箱格式不正确"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     */
    private $sex;

    /**
     * @ORM\Column(type="datetime")
     */
    private $birthday;

    /**
     * @ORM\Column(type="string")
     */
    private $avatar;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * User constructor.
     * @param $username
     * @param $password
     * @param $phoneNumber
     * @param $email
     * @param $sex
     * @param $birthday
     * @param $avatar
     * @param $description
     */
    public function __construct($username, $password, $phoneNumber, $email, $sex, $birthday, $avatar, $description)
    {
        $this->username = $username;
        $this->password = $password;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->sex = $sex;
        $this->birthday = $birthday;
        $this->avatar = $avatar;
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
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
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getJson() {
        return array(
            "id"=>$this->getId(),
            "username"=>$this->getUsername(),
            "password"=>$this->getPassword(),
            "phoneNumber"=>$this->getPhoneNumber(),
            "email"=>$this->getEmail(),
            "sex"=>$this->getSex(),
            "birthday"=>$this->getBirthday()->format('Y-m-d'),
            "avatar"=>$this->getAvatar(),
            "description"=>$this->getDescription()
        );
    }

}
