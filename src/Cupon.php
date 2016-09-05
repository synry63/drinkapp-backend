<?php
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
// src/Cupon.php
/**
 * @Entity @Table(name="cupones")
 **/
class Cupon
{
    /**
     * @var int
     */
	/** @Id @Column(type="integer") @GeneratedValue **/
    public $id;
	 /**
     * @var string
     */
	/** @Column(type="string") **/
    public $nombre;
	/**
    * @var string
    */
	/** @Column(type="text") **/
    public $descripcion;	
	/**
     * @var string
     */
	/** @Column(type="string") **/
    public $imgURL;

    /**
     * @var string
     */
    /** @Column(type="string",unique=true) **/
    public $code;
    /**
     * @var int
     */
    /** @Column(type="integer") **/
    public $percent;
	
}