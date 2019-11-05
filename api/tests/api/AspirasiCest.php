<?php

use app\models\Aspirasi;

/**
 * Cest class to test Aspirasi endpoints, grouped by permissions in Aspirasi
 */
class AspirasiCest
{
    // viewAddressedCascadedAspirasi
    public function viewAddressedCascadedAspirasiTest(ApiTester $I)
    {
        Yii::$app->db->createCommand()->checkIntegrity(false)->execute();

        Yii::$app->db->createCommand('TRUNCATE aspirasi_likes')->execute();
        Yii::$app->db->createCommand('TRUNCATE aspirasi')->execute();

        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'kabkota_id 22, kec_id 431, kel_id 6093',
            'description' => 'description',
            'kabkota_id'  => 22,
            'kec_id'      => 431,
            'kel_id'      => 6093,
            'status'      => 10,
            'category_id' => 9,
            'author_id'   => 1,
            'created_at'  => 1,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 2,
            'title'       => 'kabkota_id 23, kec_id 450, kel_id 6214',
            'description' => 'description',
            'kabkota_id'  => 23,
            'kec_id'      => 450,
            'kel_id'      => 6214,
            'status'      => 10,
            'category_id' => 9,
            'author_id'   => 1,
            'created_at'  => 2,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 3,
            'title'       => 'kabkota_id 22, kec_id 432, kel_id 6101',
            'description' => 'description',
            'kabkota_id'  => 22,
            'kec_id'      => 432,
            'kel_id'      => 6101,
            'status'      => 10,
            'category_id' => 9,
            'author_id'   => 1,
            'created_at'  => 3,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 4,
            'title'       => 'kabkota_id 22, kec_id 431, kel_id 6094',
            'description' => 'description',
            'kabkota_id'  => 22,
            'kec_id'      => 431,
            'kel_id'      => 6094,
            'status'      => 10,
            'category_id' => 9,
            'author_id'   => 1,
            'created_at'  => 4,
        ]);

        // Login as Staff Provinsi
        $I->amStaff('staffprov');

        $I->sendGET('/v1/aspirasi');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->assertEquals(4, count($data[0]));
        $I->assertEquals(4, $data[0][0]['id']);
        $I->assertEquals(3, $data[0][1]['id']);

        // Get Detail
        $I->sendGET('/v1/aspirasi/4');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        // Login as Staff Kab/Kota
        $I->amStaff('staffkabkota');

        $I->sendGET('/v1/aspirasi');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->assertEquals(3, count($data[0]));
        $I->assertEquals(3, $data[0][1]['id']);
        $I->assertEquals(1, $data[0][2]['id']);

        // Login as Staff Kecamatan
        $I->amStaff('staffkec');

        $I->sendGET('/v1/aspirasi');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->assertEquals(2, count($data[0]));
        $I->assertEquals(4, $data[0][0]['id']);
        $I->assertEquals(1, $data[0][1]['id']);

        // Login as Staff Kelurahan
        $I->amStaff('staffkel');

        $I->sendGET('/v1/aspirasi');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->assertEquals(1, count($data[0]));
        $I->assertEquals(1, $data[0][0]['id']);
    }

    public function viewAddressedCascadedAspirasiOrderByCategoryNameAscendingTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 9, // INFRASTRUKTUR
            'author_id'   => 36,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 2,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 10, // SUMBER DAYA MANUSIA
            'author_id'   => 36,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 3,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 11, // EKONOMI
            'author_id'   => 36,
        ]);

        $I->amStaff('staffprov');

        $I->sendGET('/v1/aspirasi?sort_by=category.name&sort_order=ascending');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->assertEquals(3, $data[0][0]['id']);
        $I->assertEquals(1, $data[0][1]['id']);
        $I->assertEquals(2, $data[0][2]['id']);
    }

    public function viewAddressedCascadedAspirasiOrderByCategoryNameDescendingTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 9, // INFRASTRUKTUR
            'author_id'   => 36,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 2,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 10, // SUMBER DAYA MANUSIA
            'author_id'   => 36,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 3,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 11, // EKONOMI
            'author_id'   => 36,
        ]);

        $I->amStaff('staffprov');

        $I->sendGET('/v1/aspirasi?sort_by=category.name&sort_order=descending');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->assertEquals(2, $data[0][0]['id']);
        $I->assertEquals(1, $data[0][1]['id']);
        $I->assertEquals(3, $data[0][2]['id']);
    }


    // viewPublishedAspirasi
    public function viewPublishedAspirasiTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Aspirasi Deleted',
            'description' => 'Desc',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => Aspirasi::STATUS_DELETED,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 2,
            'title'       => 'Aspirasi Draft',
            'description' => 'Desc',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => Aspirasi::STATUS_DRAFT,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 3,
            'title'       => 'Aspirasi Pending',
            'description' => 'Desc',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => Aspirasi::STATUS_APPROVAL_PENDING,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 4,
            'title'       => 'Aspirasi Rejected',
            'description' => 'Desc',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => Aspirasi::STATUS_APPROVAL_REJECTED,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 5,
            'title'       => 'Aspirasi Published',
            'description' => 'Desc',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => Aspirasi::STATUS_PUBLISHED,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $I->sendGET('/v1/aspirasi');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->assertEquals(5, $data[0][0]['id']);

        // Get Detail
        $I->sendGET('/v1/aspirasi/5');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }

    public function viewPublishedAspirasiDefaultSortTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 9, // INFRASTRUKTUR
            'author_id'   => 36,
            'created_at'  => 1553010000,
            'updated_at'  => 1553010000,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 2,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 10, // SUMBER DAYA MANUSIA
            'author_id'   => 36,
            'created_at'  => 1553020000,
            'updated_at'  => 1553020000,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 3,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 11, // EKONOMI
            'author_id'   => 36,
            'created_at'  => 1553030000,
            'updated_at'  => 1553030000,
        ]);

        $I->amUser('user');

        $I->sendGET('/v1/aspirasi');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->assertEquals(3, $data[0][0]['id']);
        $I->assertEquals(2, $data[0][1]['id']);
        $I->assertEquals(1, $data[0][2]['id']);
    }

    // viewOwnAspirasi
    public function viewOwnAspirasiTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Aspirasi staffRW Bandung',
            'description' => 'Desc',
            'kabkota_id'  => 22,
            'kec_id'      => 431,
            'kel_id'      => 6093,
            'rw'          => '001',
            'status'      => Aspirasi::STATUS_DRAFT,
            'category_id' => 9,
            'author_id'   => 17,
        ]);

        $I->haveInDatabase('aspirasi', [
            'id'          => 2,
            'title'       => 'Aspirasi staffRW Bekasi',
            'description' => 'Desc',
            'kabkota_id'  => 23,
            'kec_id'      => 449,
            'kel_id'      => 6197,
            'rw'          => '001',
            'status'      => Aspirasi::STATUS_DRAFT,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('staffrw');

        $I->sendGET('/v1/aspirasi/me');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');

        $I->assertEquals(1, $data[0][0]['id']);

        // Get Detail
        $I->sendGET('/v1/aspirasi/1');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }

    // Aspirasi Create
    public function postCreateTest(ApiTester $I)
    {
        $I->amUser('user');

        $data = [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 0,
            'category_id' => 9,
            'author_id'   => 36,
        ];

        $I->sendPOST('/v1/aspirasi', $data);
        $I->canSeeResponseCodeIs(201);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 201,
        ]);
    }

    // Aspirasi Edit
    public function postUpdateTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 0,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $data = [
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 0,
            'category_id' => 9,
            'author_id'   => 36,
        ];

        $I->sendPUT('/v1/aspirasi/1', $data);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }

    public function userCanUpdateIfStatusDraft(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 0,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $data = [
            'title' => 'Lorem ipsum',
        ];

        $I->sendPUT('/v1/aspirasi/1', $data);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }

    public function userCanUpdateIfStatusRejected(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 3,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $data = [
            'title' => 'Lorem ipsum',
            'status' => 1,
        ];

        $I->sendPUT('/v1/aspirasi/1', $data);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }

    public function userCannotUpdateIfStatusPending(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 5,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $data = [
            'title' => 'Lorem ipsum',
        ];

        $I->sendPUT('/v1/aspirasi/1', $data);
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 403,
        ]);
    }

    public function userCannotUpdateIfStatusPublished(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $data = [
            'title' => 'Lorem ipsum',
        ];

        $I->sendPUT('/v1/aspirasi/1', $data);
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 403,
        ]);
    }

    // Aspirasi Delete
    public function userCanDeleteIfDraftTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 0,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $I->sendDELETE('/v1/aspirasi/1');
        $I->canSeeResponseCodeIs(204);
    }

    public function userCanDeleteIfRejectedTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 3,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $I->sendDELETE('/v1/aspirasi/1');
        $I->canSeeResponseCodeIs(204);
    }

    public function userCannotDeleteIfPendingTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 5,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $I->sendDELETE('/v1/aspirasi/1');
        $I->canSeeResponseCodeIs(403);
    }

    public function userCannotDeleteIfPublishedTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $I->sendDELETE('/v1/aspirasi/1');
        $I->canSeeResponseCodeIs(403);
    }

    public function userCannotDeleteIfUnauthorizedTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 9,
            'author_id'   => 1,
        ]);

        $I->amUser('user');

        $I->sendDELETE('/v1/aspirasi/1');
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 403,
        ]);
    }

    // Aspirasi Like
    public function postLikeAspirasi(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->amUser('user');

        $I->sendPOST('/v1/aspirasi/likes/1');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $I->seeInDatabase('aspirasi_likes', ['user_id' => 36, 'aspirasi_id' => 1]);
    }

    public function postDislikeAspirasi(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'kabkota_id'  => 22,
            'kec_id'      => 446,
            'kel_id'      => 6082,
            'status'      => 10,
            'category_id' => 9,
            'author_id'   => 36,
        ]);

        $I->haveInDatabase('aspirasi_likes', [
            'aspirasi_id' => 1,
            'user_id'     => 36,
        ]);

        $I->amUser('user');

        $I->sendPOST('/v1/aspirasi/likes/1');
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $I->dontSeeInDatabase('aspirasi_likes', ['user_id' => 36, 'aspirasi_id' => 1]);
    }

    // Aspirasi Approval
    public function staffKabkotaKecKelApproveUnauthorizedTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'title',
            'description' => 'description',
            'kabkota_id'  => 22,
            'kec_id'      => 431,
            'kel_id'      => 6093,
            'status'      => 5,
            'category_id' => 9,
            'author_id'   => 1,
            'created_at'  => 1,
        ]);

        $I->amStaff('staffkabkota');
        $I->sendPOST('/v1/aspirasi/approval/1');
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 403,
        ]);

        $I->amStaff('staffkec');
        $I->sendPOST('/v1/aspirasi/approval/1');
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 403,
        ]);

        $I->amStaff('staffkel');
        $I->sendPOST('/v1/aspirasi/approval/1');
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status'  => 403,
        ]);
    }

    public function staffProvRejectTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'title',
            'description' => 'description',
            'kabkota_id'  => 22,
            'kec_id'      => 431,
            'kel_id'      => 6093,
            'status'      => Aspirasi::STATUS_APPROVAL_PENDING,
            'category_id' => 9,
            'author_id'   => 1,
            'created_at'  => 1,
        ]);

        $data = [
            'action' => Aspirasi::ACTION_REJECT,
            'note'   => 'note',
        ];

        $I->amStaff('staffprov');
        $I->sendPOST('/v1/aspirasi/approval/1', $data);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeInDatabase('aspirasi', [
            'id'            => 1,
            'status'        => Aspirasi::STATUS_APPROVAL_REJECTED,
            'approved_by'   => 2,
            'approval_note' => 'note',
        ]);
    }

    public function staffProvPublishTest(ApiTester $I)
    {
        $I->haveInDatabase('aspirasi', [
            'id'          => 1,
            'title'       => 'title',
            'description' => 'description',
            'kabkota_id'  => 22,
            'kec_id'      => 431,
            'kel_id'      => 6093,
            'status'      => Aspirasi::STATUS_APPROVAL_PENDING,
            'category_id' => 9,
            'author_id'   => 1,
            'created_at'  => 1,
        ]);

        $data = [
            'action' => Aspirasi::ACTION_APPROVE,
            'note'   => 'note',
        ];

        $I->amStaff('staffprov');
        $I->sendPOST('/v1/aspirasi/approval/1', $data);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeInDatabase('aspirasi', [
            'id'            => 1,
            'status'        => Aspirasi::STATUS_PUBLISHED,
            'approved_by'   => 2,
            'approval_note' => 'note',
        ]);
    }
}
