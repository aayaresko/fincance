<?php

namespace App\Container\Finance;

use App\Service\OrganizationService;

class OrganizationContainer
{
    public $id;
    public $oldId;
    public $branch;
    public $title;
    public $regionId;
    public $cityId;
    public $phone;
    public $address;
    public $link;
    /**
     * @var string
     */
    private $type;
    /**
     * @var CurrencyContainer[]
     */
    private $currencies;

    /**
     * OrganizationContainer constructor.
     *
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        if ($data instanceof \stdClass) {
            $this->id      = $data->id;
            $this->oldId   = $data->oldId;
            $this->branch  = $data->branch;
            $this->title   = $data->title;
            $this->cityId  = $data->cityId;
            $this->phone   = $data->phone;
            $this->address = $data->address;
            $this->link    = $data->link;
            $this->setType($data->orgType);
            $this->setCurrencies((array) $data->currencies);
        }
        if (is_array($data)) {
            $this->id      = $data['id'];
            $this->oldId   = $data['oldId'];
            $this->branch  = $data['branch'];
            $this->title   = $data['title'];
            $this->cityId  = $data['cityId'];
            $this->phone   = $data['phone'];
            $this->address = $data['address'];
            $this->link    = $data['link'];
            $this->setType($data['orgType']);
            $this->setCurrencies($data['currencies']);
        }
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        if ($type === OrganizationService::TYPE_BANK || $type === OrganizationService::TYPE_EXCHANGER) {
            $this->type = $type;
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \stdClass[] $currencies
     */
    public function setCurrencies(array $currencies)
    {
        foreach ($currencies as $index => $currency) {
            $currency           = new CurrencyContainer($currency);
            $currency->code     = $index;
            $this->currencies[] = $currency;
        }
    }

    /**
     * @return CurrencyContainer[]
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }
}