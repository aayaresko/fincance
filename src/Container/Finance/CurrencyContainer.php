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
            $this->saleValue = (double) $data->ask;
            $this->buyValue  = (double) $data->bid;
        }
        if (is_array($data)) {
            if (isset($data['code'])) {
                $this->code = $data['code'];
            }
            if (isset($data['name'])) {
                $this->name = $data['name'];
            }
            if (isset($data['ask'])) {
                $this->saleValue = (double) $data['ask'];
            }
            if (isset($data['bid'])) {
                $this->buyValue = (double) $data['bid'];
            }
        }
    }
}