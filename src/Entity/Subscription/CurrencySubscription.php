<?php

namespace App\Entity\Subscription;

use App\Entity\Currency;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurrencySubscriptionRepository")
 */
class CurrencySubscription extends BaseSubscription
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currency;

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
}
