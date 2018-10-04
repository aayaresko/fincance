<?php

namespace App\Entity;

use App\Service\AverageRateService;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AverageRateRepository")
 */
class AverageRate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id = 0;
    /**
     * @ORM\Column(type="smallint")
     */
    private $type = 0;
    /**
     * @ORM\Column(type="decimal", precision=16, scale=8, nullable=false)
     */
    private $value = 0.00;
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return AverageRate
     */
    public function setType(int $type): AverageRate
    {
        if ($type === AverageRateService::TYPE_SALE || $type === AverageRateService::TYPE_BUY) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return AverageRate
     */
    public function setValue(float $value): AverageRate
    {
        $this->value = $value;

        return $this;
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
     * @return AverageRate
     */
    public function setCurrency(Currency $currency): AverageRate
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
