<?php

class BroadcastCest
{
    private $endpointBroadcast = '/v1/broadcasts';

    public function _before(ApiTester $I)
    {
        Yii::$app->db->createCommand()->checkIntegrity(false)->execute();

        Yii::$app->db->createCommand('TRUNCATE broadcasts')->execute();
    }

    // Test cases for users
    public function getBroadcastListBandung(ApiTester $I)
    {
        $I->amUser('user.bandung');

        $I->sendGET($this->endpointBroadcast);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }

    public function getBroadcastListBekasi(ApiTester $I)
    {
        $I->amUser('user.bekasi');

        $I->sendGET($this->endpointBroadcast);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }

    public function getBroadcastStaffKabkotaList(ApiTester $I)
    {
        $I->haveInDatabase('broadcasts', [
            'id'          => 1,
            'category_id' => 5,
            'author_id'   => 1,
            'title'       => 'Lorem.',
            'description' => 'Lorem ipsum.',
            'kabkota_id'  => 22,
            'status'      => 10,
            'created_at'  => '1554706345',
            'updated_at'  => '1554706345',
        ]);

        $I->haveInDatabase('broadcasts', [
            'id'          => 2,
            'category_id' => 5,
            'author_id'   => 1,
            'title'       => 'Lorem.',
            'description' => 'Lorem ipsum.',
            'kabkota_id'  => 23,
            'status'      => 10,
            'created_at'  => '1554706345',
            'updated_at'  => '1554706345',
        ]);

        $I->amStaff('staffkabkota');

        $I->sendGET($this->endpointBroadcast);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $I->seeHttpHeader('X-Pagination-Total-Count', 1);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items[0]');

        $I->assertEquals(1, $data[0]['id']);
    }

    public function userCannotCreateNewTest(ApiTester $I)
    {
        $I->amUser();

        $I->sendPOST($this->endpointBroadcast);
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 403,
        ]);
    }

    public function userCannotUpdateTest(ApiTester $I)
    {
        $I->amUser();

        $I->sendPUT("{$this->endpointBroadcast}/0");
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 403,
        ]);
    }

    public function userCannotDeleteTest(ApiTester $I)
    {
        $I->amUser();

        $I->sendDELETE("{$this->endpointBroadcast}/0");
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 403,
        ]);
    }


    // Test cases for admins

    public function createNewBroadcastCategoryInvalid(ApiTester $I)
    {
        $I->amStaff();

        $I->sendPOST($this->endpointBroadcast, [
            'author_id' => 1,
            'category_id' => 0,
            'title' => 'Broadcast Title',
            'description' => 'Broadcast Description',
            'kabkota_id' => null,
            'kec_id' => null,
            'kel_id' => null,
            'rw' => null,
            'status' => 10,
        ]);

        $I->canSeeResponseCodeIs(422);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 422,
        ]);
    }

    public function getBroadcastListAll(ApiTester $I)
    {
        $I->amStaff();

        $I->sendGET($this->endpointBroadcast);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $I->seeResponseContainsJson([
            'kabkota_id' => 22,
        ]);

        $I->seeResponseContainsJson([
            'kabkota_id' => 23,
        ]);
    }

    public function getBroadcastListFilterCategory(ApiTester $I)
    {
        $I->amStaff();

        $I->sendGET("{$this->endpointBroadcast}?category_id=5");
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $I->seeResponseContainsJson([
            'category_id' => 5,
        ]);

        $I->cantSeeResponseContainsJson([
            'category_id' => 6,
        ]);

        $I->cantSeeResponseContainsJson([
            'category_id' => 7,
        ]);
    }

    public function getBroadcastListFilterStatus(ApiTester $I)
    {
        $I->amStaff();

        $I->sendGET("{$this->endpointBroadcast}?status=10");
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $accessToken = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->seeResponseContainsJson([
            'data' => [
                'items' => [
                    [
                        'status' => 10,
                    ],
                ],
            ],
        ]);

        $I->cantSeeResponseContainsJson([
            'data' => [
                'items' => [
                    [
                        'status' => 0,
                    ],
                ],
            ],
        ]);
    }

    public function getBroadcastListSearchTitle(ApiTester $I)
    {
        $I->amStaff();

        $I->sendGET("{$this->endpointBroadcast}?search=Kegiatan");
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }

    public function getBroadcastItemNotFound(ApiTester $I)
    {
        $I->amStaff();

        $I->sendGET("{$this->endpointBroadcast}/0");
        $I->canSeeResponseCodeIs(404);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 404,
        ]);
    }

    public function getBroadcastItem(ApiTester $I)
    {
        $I->amStaff();

        $I->sendGET("{$this->endpointBroadcast}/1");
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success'   => true,
            'status'    => 200,
            'data'      => [
                'id' => 1,
            ]
        ]);
    }

    public function updateBroadcast(ApiTester $I)
    {
        $I->amStaff();

        $I->sendPUT("{$this->endpointBroadcast}/1", [
            'title' => 'Edited',
        ]);

        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }

    public function deleteBroadcast(ApiTester $I)
    {
        $I->amStaff();

        $I->sendDELETE("{$this->endpointBroadcast}/1");
        $I->canSeeResponseCodeIs(204);
    }
}
