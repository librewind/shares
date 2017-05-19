<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use \DateTime;

/**
 * @ORM\Table(name="share")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShareRepository")
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\Column(type="string", length=100, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 100,
     *      minMessage = "Name must be at least {{ limit }} characters long",
     *      maxMessage = "Name cannot be longer than {{ limit }} characters"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 20,
     *      minMessage = "Symbol must be at least {{ limit }} characters long",
     *      maxMessage = "Symbol cannot be longer than {{ limit }} characters"
     * )
     *
     * @var string
     */
    private $symbol;

    /**
     * @ORM\OneToMany(targetEntity="PortfolioShare" , mappedBy="share")
     */
    private $portfolioshare;

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
    public function getId() : int
    {
        return $this->id;
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
     * @param string $name
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
     * @param string $symbol
     */
    public function setSymbol(string $symbol)
    {
        $this->symbol = $symbol;
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