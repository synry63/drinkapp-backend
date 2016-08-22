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
// src/Pedido.php
/**
 * @Entity @Table(name="pedidos")
 **/
class Pedido
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
    public $estado;

	/** @Column(type="integer") **/
    public $fechaPendente;
	/**
     * @var integer
     */
	/** @Column(type="integer",nullable=true) **/
    public $fechaAnulado;
	/**
     * @var integer
     */
	/** @Column(type="integer",nullable=true) **/
    public $fechaEntregado;
		/**
     * @var integer
     */
	/** @Column(type="integer",nullable=true) **/
    public $fechaDeEntrega;
	
    /**
     * @var User
     */
	/** @ManyToOne(targetEntity="User") 
		@JoinColumn(name="user_id", referencedColumnName="id",nullable=false)
	**/
    public $user;
	/**
     * @var PagoTipo
     */
	/** @ManyToOne(targetEntity="PagoTipo")
		@JoinColumn(name="pagotipo_id", referencedColumnName="id",nullable=false)
	**/
    public $pagoTipo;

    /**
     * @var Distribuidor
     */
    /** @ManyToOne(targetEntity="Distribuidor")
        @JoinColumn(name="distribuidor_id", referencedColumnName="id")
     **/
    public $distribuidor;


    /** @ManyToOne(targetEntity="Direccion")
        @JoinColumn(name="direccion_id", referencedColumnName="id",nullable=false)
     **/
    public $direccion;
	
	/**
     * @var Factura
     */
	/** @OneToOne(targetEntity="Factura",cascade={"persist"})
		@JoinColumn(name="factura_id", referencedColumnName="id")
	**/
    public $factura;
	
	/**
     * @var boolean
     */
	/** @Column(type="boolean",nullable=true) **/
    public $favorito;
	/**
    * @var string
    */
	/** @Column(type="text",nullable=true) **/
    public $nota;
	
	/**
    * @var string
    */
	/** @Column(type="string",nullable=true) **/
    public $recibo;
	/**
    * @var float
    */
	/** @Column(type="float",nullable=true) **/
    public $pagoEffectivoCantidad;	
	/**
    * @var float
    */
	/** @Column(type="float",nullable=true) **/
    public $precioDelivery;

}