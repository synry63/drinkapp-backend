<?php
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Annotations\AnnotationRegistry;
AnnotationRegistry::registerFile("vendor/jms/serializer/src/JMS/Serializer/Annotation/ExclusionPolicy.php");
AnnotationRegistry::registerFile("vendor/jms/serializer/src/JMS/Serializer/Annotation/Exclude.php");
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;



// src/User.php
/**
 * @Entity @Table(name="users")
 * @ExclusionPolicy("none")
 **/
class User
{
    /**
     * @var int
     *
     */
	/** @Id @Column(type="integer") @GeneratedValue **/
    public   $id;
	/**
     * @var string
     */
	/** @Column(type="string") **/
    public  $nombre;
	/**
    * @var string
    */
	/** @Column(type="string") **/
    public $apellidos;
	/**
     * @var string
     */
	/** @Column(type="string",unique=true,nullable=true) **/
    public $email;
	/**
	 * @var int
	 */
	/** @Column(type="string",unique=true,nullable=true) **/
    public $id_facebook;
	/**
     * @var string
     */
	/** @Column(type="string",nullable=true)
     * @Exclude
	**/
    public $password;
	/**
	 * @var string
	 */
	/** @Column(type="integer",nullable=true)
	 **/
    public $celular;
	/**
     * @var string
     */
	/** @Column(type="boolean") **/
    public $active;
	
	/**
     * @OneToMany(targetEntity="Direccion", mappedBy="user")
     **/
    public $direcciones;

    /**
     * @var integer
     */
    /** @Column(type="integer") **/
    public $fechaRegistro;

    /**
     * @var string
     */
    /** @Column(type="string",nullable=true) **/
    public $registerKey;

    /**
     * @var boolean
     */
    /** @Column(type="boolean",nullable=true) **/
    public $is_over_18;


    /**
     * @var string
     */
    /** @Column(type="string",nullable=true) **/
    public $come_from;

    /**
     * @var integer
     */
    /** @Column(type="integer",nullable=true,options={"default": 0}) **/
    public $puntos;

    /**
     * @var boolean
     */
    /** @Column(type="boolean",nullable=true) **/
    public $rate_us;
}