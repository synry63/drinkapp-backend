<?php
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
// src/Direccion.php
/**
 * @Entity @Table(name="direcciones")
 **/
class Direccion
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
	/** @Column(type="boolean") **/
	public $is_other_name;
	/**
	 * @var string
	 */
	/** @Column(type="string",nullable=true) **/
	public $custom_name;
    /**
     * @var string
     */
    /** @Column(type="string") **/
    public $slug;
	
	/**
     * @var string
     */
	/** @Column(type="string") **/
    public $calle;
	/**
     * @var string
     */
	/** @Column(type="string",nullable=true) **/
    public $referencias;	
	/**
     * @var string
     */
	/** @Column(type="string",nullable=true) **/
    public $numero;		
	/**
     * @var string
     */
	/** @Column(type="string",nullable=true) **/
    public $piso_apt;		
	/**
     * @var string
     */
	/** @Column(type="string") **/
    public $distrito;	
	/**
     * @var string
     */
	/** @Column(type="string",nullable=true) **/
    public $telefono;	
	/**
     * @var User
     */
	/** @ManyToOne(targetEntity="User", inversedBy="direcciones") 
		@JoinColumn(name="user_id", referencedColumnName="id")
	**/
    public $user;
	/**
     * @var Distribuidor
     */
	/** @ManyToOne(targetEntity="Distribuidor") 
		@JoinColumn(name="distribuidor_id", referencedColumnName="id")
	**/
    public $distribuidor;

    /**
     * @var string
     */
    /** @Column(type="string",nullable=true) **/
    public $latitude;
    /**
     * @var string
     */
    /** @Column(type="string",nullable=true) **/
    public $longitude;

}