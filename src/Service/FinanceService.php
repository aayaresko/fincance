<?php

namespace App\Service;

use App\Container\Finance\CurrencyContainer;
use App\Container\Finance\OrganizationContainer;
use GuzzleHttp\Client;

class FinanceService
{
    const API_URL = 'http://resources.finance.ua';

    const PATH_RATES = '/ru/public/currency-cash.json';

    /**
     * @param string $path
     * @param string $type
     * @param array $data
     * @return mixed
     */
    public function processRequest($path, $type = 'POST', array $data = [])
    {
        $client = new Client([
            'base_uri' => self::API_URL,
            'timeout'  => 2.0,
        ]);

        return $client->request($type, $path, $data);
    }

    /**
     * @return array
     */
    public function getRates()
    {
        $response      = $this->processRequest(self::PATH_RATES, 'GET');
        $body          = json_decode($response->getBody());
        $organizations = $body->organizations;
        $data          = [];
        foreach ($organizations as $item) {
            $organization = new OrganizationContainer($item);
            foreach ($organization->getCurrencies() as $currency) {
                $data[] = $currency;
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getAvailableOrganizations()
    {
        $response      = $this->processRequest(self::PATH_RATES, 'GET');
        $body          = json_decode($response->getBody());
        $organizations = $body->organizations;
        $data          = [];
        foreach ($organizations as $index => $item) {
            $data[] = new OrganizationContainer($item);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getAvailableCurrencies()
    {
        $response      = $this->processRequest(self::PATH_RATES, 'GET');
        $body          = json_decode($response->getBody());
        $currencies    = $body->currencies;
        $data          = [];
        foreach ($currencies as $index => $item) {
            $currency = new CurrencyContainer();
            $currency->code = $index;
            $currency->name = $item;
            $data[] = $currency;
        }

        return $data;
    }
}