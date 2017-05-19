<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Portfolio;
use AppBundle\Entity\Share;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="portfolio_share")
 * @ORM\HasLifecycleCallbacks
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
     * @Assert\GreaterThan(0)
     *
     * @var float $ratio
     */
    private $ratio;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Get portfolio.
     *
     * @return Portfolio
     */
    public function getPortfolio() : Portfolio
    {
        return $this->portfolio;
    }

    /**
     * Set portfolio.
     *
     * @param Portfolio $portfolio
     */
    public function setPortfolio(Portfolio $portfolio)
    {
        $this->portfolio = $portfolio;
    }

    /**
     * Get share.
     *
     * @return Share
     */
    public function getShare() : ?Share
    {
        return $this->share;
    }

    /**
     * Set share.
     *
     * @param Share $share
     */
    public function setShare(Share $share)
    {
        $this->share = $share;
    }

    /**
     * Get ratio.
     *
     * @return float
     */
    public function getRatio() : ?float
    {
        return $this->ratio;
    }

    /**
     * Set ratio.
     *
     * @param float $ratio
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
    }

    /**
     * Get createdAt.
     *
     * @return DateTime
     */
    public function getCreatedAt() : ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt.
     *
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get updatedAt.
     *
     * @return DateTime
     */
    public function getUpdatedAt() : ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt.
     *
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Обновляет createdAt/updatedAt.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new DateTime('now'));

        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(new DateTime('now'));
        }
    }
}