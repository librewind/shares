<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="share")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineShareRepository")
 */
class Share
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @var string
     */
    private $symbol;

    /**
     * @ORM\OneToMany(targetEntity="PortfolioShare" , mappedBy="share")
     * */
    private $portfolioshare;

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param  integer  $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param  string  $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get symbol.
     *
     * @return string
     */
    public function getSymbol() : string
    {
        return $this->symbol;
    }

    /**
     * Set symbol.
     *
     * @param  string  $symbol
     */
    public function setSymbol(string $symbol)
    {
        $this->symbol = $symbol;
    }
}