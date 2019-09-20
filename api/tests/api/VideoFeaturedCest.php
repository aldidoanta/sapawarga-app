<?php

class VideoFeaturedCest
{
    public function _before(ApiTester $I)
    {
        Yii::$app->db->createCommand()->checkIntegrity(false)->execute();

        Yii::$app->db->createCommand('TRUNCATE videos')->execute();
        Yii::$app->db->createCommand('TRUNCATE video_featured')->execute();

        // PROVINSI
        // ACTIVE
        $I->haveInDatabase('videos', [
            'id'          => 1,
            'category_id' => 22,
            'title'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'source'      => 'youtube',
            'video_url'   => 'https://google.com',
            'kabkota_id'  => null,
            'status'      => 10,
            'created_at'  => '1554706345',
            'updated_at'  => '1554706345',
            'created_by'  => 1,
            'updated_by'  => 1,
        ]);

        // DELETED
        $I->haveInDatabase('videos', [
            'id'          => 2,
            'category_id' => 22,
            'title'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'source'      => 'youtube',
            'video_url'   => 'https://google.com',
            'kabkota_id'  => null,
            'status'      => -1,
            'created_at'  => '1554706345',
            'updated_at'  => '1554706345',
            'created_by'  => 1,
            'updated_by'  => 1,
        ]);

        // DISABLED
        $I->haveInDatabase('videos', [
            'id'          => 3,
            'category_id' => 22,
            'title'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'source'      => 'youtube',
            'video_url'   => 'https://google.com',
            'kabkota_id'  => null,
            'status'      => 0,
            'created_at'  => '1554706345',
            'updated_at'  => '1554706345',
            'created_by'  => 1,
            'updated_by'  => 1,
        ]);

        // KABKOTA
        // ACTIVE
        $I->haveInDatabase('videos', [
            'id'          => 4,
            'category_id' => 22,
            'title'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'source'      => 'youtube',
            'video_url'   => 'https://google.com',
            'kabkota_id'  => 22,
            'status'      => 10,
            'created_at'  => '1554706345',
            'updated_at'  => '1554706345',
            'created_by'  => 1,
            'updated_by'  => 1,
        ]);

        // DELETED
        $I->haveInDatabase('videos', [
            'id'          => 5,
            'category_id' => 22,
            'title'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'source'      => 'youtube',
            'video_url'   => 'https://google.com',
            'kabkota_id'  => 22,
            'status'      => -1,
            'created_at'  => '1554706345',
            'updated_at'  => '1554706345',
            'created_by'  => 1,
            'updated_by'  => 1,
        ]);

        // DISABLED
        $I->haveInDatabase('videos', [
            'id'          => 6,
            'category_id' => 22,
            'title'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'source'      => 'youtube',
            'video_url'   => 'https://google.com',
            'kabkota_id'  => 22,
            'status'      => 0,
            'created_at'  => '1554706345',
            'updated_at'  => '1554706345',
            'created_by'  => 1,
            'updated_by'  => 1,
        ]);

        $I->haveInDatabase('video_featured', [
            'video_id' => 1,
            'seq'     => 1,
        ]);

        $I->haveInDatabase('video_featured', [
            'video_id'    => 4,
            'kabkota_id' => 22,
            'seq'        => 2,
        ]);
    }

    public function getUserListFeaturedProvinsiTest(ApiTester $I)
    {
        $I->amUser('staffrw');

        $I->sendGET('/v1/videos/featured');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data');

        $I->assertEquals(1, $data[0][0]['id']);
        $I->assertNull($data[0][1]);
        $I->assertNull($data[0][2]);
        $I->assertNull($data[0][3]);
        $I->assertNull($data[0][4]);
    }

    public function getUserListFeaturedKabkotaTest(ApiTester $I)
    {
        $I->amUser('staffrw');

        $I->sendGET('/v1/videos/featured?kabkota_id=22');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data');

        $I->assertEquals(4, $data[0][0]['id']);
        $I->assertNull($data[0][1]);
        $I->assertNull($data[0][2]);
        $I->assertNull($data[0][3]);
        $I->assertNull($data[0][4]);
    }
}
