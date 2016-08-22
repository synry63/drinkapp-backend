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
// src/Bebida.php
/**
 * @Entity @Table(name="bebida_pedido")
 **/
class PedidoBebida
{
    /**
     * @var int
     */
	/** @Id @Column(type="integer") @GeneratedValue **/
    public $id;
	/**
     * @var Bebida
     */
	/** @ManyToOne(targetEntity="Bebida") 
		@JoinColumn(nullable=false)
	**/
    public $bebida;
	/**
     * @var Pedido
     */
	/** @ManyToOne(targetEntity="Pedido") 
		@JoinColumn(nullable=false)
	**/
    public $pedido;	
	
	/**
     * @var int
     */
	/** @Column(type="integer") **/
    public $cantidad;	
	
}