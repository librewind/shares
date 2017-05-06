<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="Portfolio", inversedBy="portfolioshare")
     * @ORM\JoinColumn(name="portfolio_id", referencedColumnName="id")
     * */
    private $portfolio;

    /**
     * @ORM\ManyToOne(targetEntity="Share", inversedBy="portfolioshare")
     * @ORM\JoinColumn(name="share_id", referencedColumnName="id")
     * */
    private $share;

    /**
     * @ORM\Column(type="float")
     *
     * @var float $proportion
     */
    private $proportion;
}