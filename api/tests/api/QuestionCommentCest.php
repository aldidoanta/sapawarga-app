<?php

use app\commands\SeederController;

class QuestionCommentCest
{
    private $endpointComment = '/v1/questions/1/comments';

    public function init(ApiTester $I)
    {
        $seeder = new SeederController(null, null);
        $seeder->actionQuestionComment();
    }

    public function getCommentListAll(ApiTester $I)
    {
        $I->amStaff('staffprov');

        $I->sendGET($this->endpointComment);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeHttpHeader('X-Pagination-Total-Count', 3);

        $data = $I->grabDataFromResponseByJsonPath('$.data.items');
        $I->assertEquals(1, $data[0][0]['id']);
        $I->assertEquals(2, $data[0][1]['id']);
        $I->assertEquals(3, $data[0][2]['id']);
    }

    // This test case will be revised when staffRWs are allowed to post comment
    public function postCreateUnauthorizedTest(ApiTester $I)
    {
        $I->amUser('staffrw');

        $data = [];

        $I->sendPOST($this->endpointComment, $data);
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status' => 403,
        ]);
    }

    public function postCreateTest(ApiTester $I)
    {
        $I->amStaff('staffprov');

        $data = [
            'text' => 'lorem ipsum',
        ];

        $I->sendPOST($this->endpointComment, $data);
        $I->canSeeResponseCodeIs(201);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 201,
        ]);

        $I->seeInDatabase('question_comments', [
            'question_id' => 1,
            'text'        => 'lorem ipsum',
            'status'      => 10,
            'created_by'  => 2,
        ]);
    }
}
