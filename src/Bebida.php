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
// src/Bebida.php
/**
 * @Entity @Table(name="bebidas")
 **/
class Bebida
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
     * @var float
     */
    /** @Column(type="decimal",precision=10,scale=2) **/
    public $price;
	/**
	 * @var boolean
	 */
	/** @Column(type="boolean",options={"default": true}) **/
	public $active;

    /**
     * @var boolean
     */
    /** @Column(type="boolean",options={"default": false}) **/
    public $freeDelivery;
	/**
     * @var Categoria
     */
	/** @ManyToOne(targetEntity="Categoria",inversedBy="bebidas")
		@JoinColumn(name="categoria_id", referencedColumnName="id")
	**/
    public $categoria;



}