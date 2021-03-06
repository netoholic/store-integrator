<?php

namespace StoreIntegrator\eBay;

use DTS\eBaySDK\Trading\Types\ShippingServiceDetailsType;
use StoreIntegrator\ShippingService;

/**
 * Class EbayShippingService
 * @package StoreIntegrator\eBay
 */
class EbayShippingService extends ShippingService
{
    /**
     * A data object returned from the ebay sdk or array.
     * Corresponds directly to the xml response.
     *
     * @param array|ShippingServiceDetailsType $data
     */
    public function __construct($data)
    {
        if(is_array($data)) {
            // TODO: Map eBay specific data from array
            $additionalParams = $this->constructSpecificDataFromArray($data);
            parent::__construct($data['id'], $data['name'], $data['description'], $additionalParams);
        } else {
            // TODO: Map eBay specific data
            $additionalParams = $this->constructSpecificDataFromObject($data);
            parent::__construct($data->ShippingServiceID, $data->ShippingService, $data->Description, $additionalParams);
        }
    }

    protected function constructSpecificDataFromArray(array $data)
    {
        $result = [
            'cost' => $data['cost'],
            'additionalCost' => $data['additionalCost'],
            'international' => $data['international']
        ];

        if($data['international']) {
            $result['shipsTo'] = $data['shipsTo'];
        }

        return $result;
    }

    private function constructSpecificDataFromObject($data)
    {
        $result = [
            'international' => $data->InternationalService
        ];

        return $result;
    }
}