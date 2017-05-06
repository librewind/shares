<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * One User has Many Portfolios.
     * @ORM\OneToMany(targetEntity="Portfolio", mappedBy="fos_user")
     */
    private $portfolios;

    public function __construct()
    {
        parent::__construct();

        $this->portfolios = new ArrayCollection();
    }
}