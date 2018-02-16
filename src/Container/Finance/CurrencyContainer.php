<?php

namespace App\Container\Finance;

class CurrencyContainer
{
    public $name;
    public $code;
    public $saleValue;
    public $buyValue;

    /**
     * CurrencyContainer constructor.
     *
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        if ($data instanceof \stdClass) {
            $this->code      = $data->currency;
            $this->saleValue = $data->ask;
            $this->buyValue  = $data->bid;
        }
        if (is_array($data)) {
            $this->code = $data['currency'];
            if (isset($data['name'])) {
                $this->name = $data['name'];
            }
            if (isset($data['ask'])) {
                $this->saleValue = $data['ask'];
            }
            if (isset($data['bid'])) {
                $this->buyValue = $data['bid'];
            }
        }
    }
}