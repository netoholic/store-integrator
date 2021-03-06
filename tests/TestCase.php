<?php

namespace StoreIntegrator\tests;


use DOMDocument;
use DTS\eBaySDK\Constants\SiteIds;
use DTS\eBaySDK\Trading\Services\TradingService;
use Exception;
use Guzzle\Http\Client;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use PHPUnit_Framework_TestCase;
use StoreIntegrator\Product;
use StoreIntegrator\Store;
use StoreIntegrator\tests\eBay\MockHttpClient;

/**
 * Class TestCase
 * @package StoreIntegrator\tests
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var TradingService
     */
    protected $tradingService;
    /**
     * @var MockHttpClient
     */
    protected $mockHttpClient;
    /**
     * @var Client
     */
    protected $guzzle;

    /**
     * @param $schema
     * @param $actual
     * @param $message
     */
    public function assertValidXML($schema, $actual, $message = 'The provided XML does not validate against the provided schema')
    {
        $xml = new DOMDocument();

        if(file_exists($actual)) {
            $xml->load($actual);
        } else {
            $xml->loadXML($actual);
        }

        try{
            $this->assertTrue($xml->schemaValidate($schema), $message);
        } catch(Exception $e) {
            $this->fail($message . "\n" . $e->getMessage());
        }

    }

    /**
     *
     */
    public function setUpEbayServiceMocks()
    {
        $this->guzzle = new Client();

        $this->mockHttpClient = new MockHttpClient($this->guzzle);

        $this->tradingService = new TradingService([
            'siteId' => SiteIds::US,
            'sandbox' => true
        ], $this->mockHttpClient);
    }

    /**
     * @param $mockReponse
     * @return \DTS\eBaySDK\Trading\Types\GeteBayOfficialTimeRequestType
     */
    public function attachMockedEbayResponse($mockReponse)
    {
        $plugin = new MockPlugin();
        $plugin->addResponse($mockReponse);

        $this->guzzle->addSubscriber($plugin);
    }

    /**
     * @param $xml
     * @return Response
     */
    public function generateEbaySuccessResponse($xml)
    {
        $mockReponse = new Response(200);
        if(file_exists($xml)) {
            $mockReponse->setBody(EntityBody::factory(file_get_contents($xml)));
        } else {
            $mockReponse->setBody(EntityBody::factory($xml));
        }

        return $mockReponse;
    }

    /**
     * @param array $additionalData
     * @return Product
     */
    public function sampleProduct($additionalData = [])
    {
        $product = new Product(array_merge([
            'name' => 'Apple MacBook Pro MB990LL/A 13.3 in. Notebook NEW',
            'description' => 'Brand New Apple MacBook Pro MB990LL/A 13.3 in. Notebook!',
            'sku' => 'a12345',
            'category' => '111422',
            'brand' => 'Apple',
            'price' => '1000',
            'currency' => 'USD',
            'weight' => '2000',
            'quantity' => 150
        ], $additionalData));

        return $product;
    }

    /**
     * @return Store
     */
    public function sampleStore()
    {
        $store = new Store('test_email@mail.dev', ['location' => 'Varna', 'country' => 'Bulgaria', 'postCode' => '9000']);

        return $store;
    }

    public function createErrorResponseForOperation($operation)
    {
        $responseXML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
            <{$operation}Response xmlns="urn:ebay:apis:eBLBaseComponents">
                <Timestamp>2015-10-27T15:16:03.205Z</Timestamp>
                <Ack>Failure</Ack>
                <Errors>
                    <ShortMessage>Small warning</ShortMessage>
                    <LongMessage>Very small warning</LongMessage>
                    <ErrorCode>1</ErrorCode>
                    <SeverityCode>Warning</SeverityCode>
                    <ErrorClassification>RequestWarning</ErrorClassification>
                </Errors>
                <Errors>
                    <ShortMessage>Big Time Error</ShortMessage>
                    <LongMessage>Very Big Error</LongMessage>
                    <ErrorCode>1234</ErrorCode>
                    <SeverityCode>Error</SeverityCode>
                    <ErrorClassification>RequestError</ErrorClassification>
                </Errors>
                <Version>921</Version>
                <Build>E921_CORE_API_17506731_R1</Build>
            </{$operation}Response>​
XML;

        $mockRespone = $this->generateEbaySuccessResponse($responseXML);
        $this->attachMockedEbayResponse($mockRespone);
    }
}