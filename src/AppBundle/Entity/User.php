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
     * @ORM\OneToMany(targetEntity="Portfolio", mappedBy="user")
     */
    private $portfolios;

    public function __construct()
    {
        parent::__construct();

        $this->portfolios = new ArrayCollection();
    }

    public function addPortfolio(Portfolio $portfolio)
    {
        if (!$this->portfolios->contains($portfolio)) {
            $portfolio->setUser($this);

            $this->portfolios->add($portfolio);
        }
    }

    public function getPortfolios()
    {
        return $this->portfolios;
    }
}