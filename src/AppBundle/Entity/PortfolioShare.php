<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Portfolio;
use AppBundle\Entity\Share;

/**
 * @ORM\Entity
 * @ORM\Table(name="portfolio_share")
 */
class PortfolioShare
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer $id
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Portfolio", inversedBy="portfolioshares")
     * @ORM\JoinColumn(name="portfolio_id", referencedColumnName="id")
     */
    private $portfolio;

    /**
     * @ORM\ManyToOne(targetEntity="Share", inversedBy="portfolioshares")
     * @ORM\JoinColumn(name="share_id", referencedColumnName="id")
     */
    private $share;

    /**
     * @ORM\Column(type="float")
     *
     * @var float $proportion
     */
    private $proportion;

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

    public function getPortfolio()
    {
        return $this->portfolio;
    }

    public function setPortfolio(Portfolio $portfolio)
    {
        $this->portfolio = $portfolio;
    }

    public function getShare()
    {
        return $this->share;
    }

    public function setShare(Share $share)
    {
        $this->share = $share;
    }

    public function getProportion()
    {
        return $this->proportion;
    }

    public function setProportion($proportion)
    {
        $this->proportion = $proportion;
    }
}