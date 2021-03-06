<?php
namespace Keepa\tests;

use Keepa\API\Request;
use Keepa\API\ResponseStatus;
use Keepa\helper\KeepaTime;
use Keepa\KeepaAPI;
use Keepa\objects\AmazonLocale;

class KeepaApiTest extends AbstractTest
{
    public function testFail()
    {
        $request = new Request();
        $request->path = "product";
        $response = $this->api->sendRequestWithRetry($request);
        self::assertEquals("invalidParameter", ($response->error->type));
    }

    public function testWrongKey()
    {
        $this->api = new KeepaAPI("ahbc3r2l9ctolh9kdj83g9e9cmf6jgaqngokm31lul633mkm06o8a56honiu63mn");
        $request = Request::getProductRequest(AmazonLocale::DE, 20, null, null, 0, false, ['B001G73S50']);

        $response = $this->api->sendRequestWithRetry($request);

        self::assertNotNull($response->error);
        self::assertEquals("unauthorized", ($response->error->type));
        self::assertEquals(ResponseStatus::PAYMENT_REQUIRED, $response->status);
    }

    public function testConsumeInformation()
    {
        $request = Request::getProductRequest(AmazonLocale::DE, 0, null, null, 0, true, ['B00F8JDCO4'], ["rating" => 1]);

        $response = $this->api->sendRequestWithRetry($request);
        self::assertEquals($response->status, "OK");
        self::assertGreaterThan(-1, $response->tokenFlowReduction);
        self::assertGreaterThan(0, $response->requestTime);
        self::assertGreaterThan(0, $response->processingTimeInMs);
        self::assertGreaterThan(0, $response->tokensConsumed);

    }
}