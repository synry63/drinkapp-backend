<?php
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
// src/Distribuidor.php
/**
 * @Entity @Table(name="distribuidor")
 **/
 class Distribuidor
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
	/** @Column(type="string",unique=true) **/
    public $email;	
	/**
     * @var string
     */
	/** @Column(type="string") 
	**/
    public $password;	
	/**
    * @var string
    */
	/** @Column(type="text",nullable=true) **/
    public $descripcion;
	/**
    * @var string
    */
	/** @Column(type="string") **/
    public $tiempoDelivery;

    /**
     * @ManyToMany(targetEntity="Bebida")
     * @JoinTable(name="distribuidores_bebidas")
    **/
    public $bebidas;
	/**
     * @var string
     */
    /** @Column(type="string") **/
	public $registerKey;
	/**
     * @var boolean
     */
	/** @Column(type="boolean") **/
    public $active;
	/**
     * @var boolean
     */
	/** @Column(type="boolean") **/
    public $validateEmail;	
}