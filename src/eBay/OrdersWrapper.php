<?php

namespace StoreIntegrator\eBay;

use DTS\eBaySDK\Trading\Enums\OrderStatusCodeType;
use DTS\eBaySDK\Trading\Enums\TradingRoleCodeType;
use DTS\eBaySDK\Trading\Types\GetOrdersRequestType;
use DTS\eBaySDK\Trading\Types\PaginationType;

/**
 * Class OrdersWrapper
 * @package StoreIntegrator\eBay
 */
class OrdersWrapper extends EbayWrapper
{
    /**
     * @param int $page
     * @param int $perPage
     * @return \DTS\eBaySDK\Trading\Types\GetOrdersResponseType
     */
    public function getAll($page = 1, $perPage = 10)
    {
        $request = new GetOrdersRequestType();

        $request->OrderRole = TradingRoleCodeType::C_SELLER;
        $request->OrderStatus = OrderStatusCodeType::C_COMPLETED;

        // TODO: Don't hard-code those
        $request->CreateTimeFrom = date_create('2015-10-01');
        $request->CreateTimeTo = date_create();

        $request->Pagination = new PaginationType();
        $request->Pagination->EntriesPerPage = $perPage;
        $request->Pagination->PageNumber = $page;

        $this->addAuthToRequest($request);

        $response = $this->service->getOrders($request);

        // TODO: Don't return raw response
        return $response;
    }
}