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
    private $id;
    /**
     * @ORM\Column(type="decimal", precision=8, scale=4, nullable=false)
     */
    private $buyValue;
    /**
     * @ORM\Column(type="decimal", precision=8, scale=4, nullable=false)
     */
    private $saleValue;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currency;
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getBuyValue()
    {
        return $this->buyValue;
    }

    /**
     * @param mixed $buyValue
     */
    public function setBuyValue($buyValue)
    {
        $this->buyValue = $buyValue;
    }

    /**
     * @return mixed
     */
    public function getSaleValue()
    {
        return $this->saleValue;
    }

    /**
     * @param mixed $saleValue
     */
    public function setSaleValue($saleValue)
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
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
