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
// src/CuponUser.php
/**
 * @Entity @Table(name="cupones_users")
 **/
class CuponUser
{
    /**
     * @var int
     */
	/** @Id @Column(type="integer") @GeneratedValue **/
    public $id;

    /**
     * @var User
     */
    /** @ManyToOne(targetEntity="User")
    @JoinColumn(nullable=false)
     **/
    public $user;
    /**
     * @var Cupon
     */
    /** @ManyToOne(targetEntity="Cupon")
    @JoinColumn(nullable=false)
     **/
    public $cupon;

    /**
     * @var DateTime
     */
    /** @Column(type="datetime") **/
    public $addedAt;

}