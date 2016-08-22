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
// src/Categoria.php
/**
 * @Entity @Table(name="categorias")
 **/
class Categoria
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
     * @OneToMany(targetEntity="Bebida", mappedBy="categoria")
     **/
    public $bebidas;

    /**
     * @var int
     */
    /** @Column(type="integer") **/
    public $order;


    /**
     * @var string
     */
    /** @Column(type="text",nullable=true) **/
    public $promocional_img_slider;
	
}