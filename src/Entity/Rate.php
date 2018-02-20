<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RateRepository")
 */
class Rate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id = 0;
    /**
     * @ORM\Column(type="decimal", precision=8, scale=4, nullable=false)
     */
    private $buyValue = 0.00;
    /**
     * @ORM\Column(type="decimal", precision=8, scale=4, nullable=false)
     */
    private $saleValue = 0.00;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currency;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getBuyValue(): float
    {
        return $this->buyValue;
    }

    /**
     * @param float $buyValue
     */
    public function setBuyValue(float $buyValue)
    {
        $this->buyValue = $buyValue;
    }

    /**
     * @return float
     */
    public function getSaleValue(): float
    {
        return $this->saleValue;
    }

    /**
     * @param float $saleValue
     */
    public function setSaleValue(float $saleValue)
    {
        $this->saleValue = $saleValue;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
