<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="share")
 */
class Share
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer $id
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @var string $name
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @var string $symbol
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
    public function getId() : integer
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param  integer  $id
     * @return void
     */
    public function setId($id) : void
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
     * @return void
     */
    public function setName(string $name) : void
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
     * @return void
     */
    public function setSymbol(string $symbol) : void
    {
        $this->symbol = $symbol;
    }
}