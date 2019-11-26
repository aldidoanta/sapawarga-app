<?php

class DashboardPollingCest
{

    public function _before(ApiTester $I)
    {
        Yii::$app->db->createCommand()->checkIntegrity(false)->execute();

        // polling
        Yii::$app->db->createCommand('TRUNCATE polling_votes')->execute();
        Yii::$app->db->createCommand('TRUNCATE polling_answers')->execute();
        Yii::$app->db->createCommand('TRUNCATE polling')->execute();
    }

    public function getPollingLatestTest(ApiTester $I)
    {
        $I->haveInDatabase('polling', [
            'name'        => 'Lorem Ipsum Dolor Sit Amet',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'excerpt'     => 'Lorem ipsum dolor sit amet',
            'start_date'  => '2019-06-01',
            'end_date'    => '2019-09-01',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 17,
            'created_by'  => 1,
            'updated_by'  => 1,
            'created_at'  => '1564617600', // 01/08/2019 @ 12:00am
            'updated_at'  => '1564617600', // 01/08/2019 @ 12:00am
        ]);

        $I->haveInDatabase('polling', [
            'name'        => 'Lorem Ipsum Dolor Sit Amet',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'excerpt'     => 'Lorem ipsum dolor sit amet',
            'start_date'  => '2019-06-01',
            'end_date'    => '2019-09-01',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 17,
            'created_by'  => 2,
            'updated_by'  => 2,
            'created_at'  => '1567296000', // 01/09/2019 @ 12:00am
            'updated_at'  => '1567296000', // 01/09/2019 @ 12:00am
        ]);

        $I->haveInDatabase('polling', [
            'name'        => 'Lorem Ipsum Dolor Sit Amet',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'excerpt'     => 'Lorem ipsum dolor sit amet',
            'start_date'  => '2019-06-01',
            'end_date'    => '2019-09-01',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 0,
            'category_id' => 17,
            'created_by'  => 1,
            'updated_by'  => 1,
            'created_at'  => '1564617600', // 01/08/2019 @ 12:00am
            'updated_at'  => '1564617600', // 01/08/2019 @ 12:00am
        ]);

        $I->amStaff('staffprov');

        $I->sendGET('/v1/dashboards/polling-latest');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data');

        $I->assertEquals(1, count($data[0]));

        $I->assertEquals(2, $data[0][0]['id']);
    }
}
