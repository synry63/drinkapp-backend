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
// src/Factura.php
/**
 * @Entity @Table(name="facturas")
 **/
class Factura
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
    public $razon_social;
	/**
     * @var string
     */
	/** @Column(type="string") **/
    public $ruc;
	
}