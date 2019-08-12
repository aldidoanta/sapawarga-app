<?php

class UserCest
{
    private $endpointLogin = '/v1/user/login';
    private $endpointProfile = '/v1/user/me';

    protected function login(ApiTester $I)
    {
        $I->sendPOST($this->endpointLogin, [
            'LoginForm' => [
                'username' => 'user',
                'password' => '123456',
            ]
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status' => 200,
        ]);

        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'access_token' => 'string',
        ], '$.data');

        $token = $I->grabDataFromResponseByJsonPath('$..data.access_token');
        $token = $token[0];

        $I->amBearerAuthenticated($token);
    }

    public function userLoginInvalidFields(ApiTester $I)
    {
        $I->sendPOST($this->endpointLogin);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status' => 422,
        ]);

        $I->sendPOST($this->endpointLogin, [
            'LoginForm' => [
                'username' => 'user',
            ]
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status' => 422,
        ]);

        $I->sendPOST($this->endpointLogin, [
            'LoginForm' => [
                'password' => '123456',
            ]
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status' => 422,
        ]);
    }

    public function userLoginInvalidCredentials(ApiTester $I)
    {
        $I->sendPOST($this->endpointLogin, [
            'LoginForm' => [
                'username' => 'user',
                'password' => '1234567',
            ]
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status' => 422,
        ]);
    }

    public function userLoginInactiveUsername(ApiTester $I)
    {
        $I->sendPOST($this->endpointLogin, [
            'LoginForm' => [
                'username' => 'user.inactive',
                'password' => '123456',
            ]
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => false,
            'status' => 422,
            'data' => [
                'username' => []
            ]
        ]);
    }

    /**
     * @before login
     */
    public function userLogin(ApiTester $I)
    {
    }

    /**
     * @before login
     */
    public function userGetProfile(ApiTester $I)
    {
        $I->sendGET($this->endpointProfile);
        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);

        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'username' => 'string|null',
            'email' => 'string|null',
            'photo_url' => 'string|null',
            'name' => 'string|null',
            'phone' => 'string|null',
            'address' => 'string|null',
            'rw' => 'string|null',
            'kelurahan' => 'array',
            'kecamatan' => 'array',
            'kabkota' => 'array',
            'facebook' => 'string|null',
            'twitter' => 'string|null',
            'instagram' => 'string|null',
        ], '$.data');
    }

    public function userUpdateProfile(ApiTester $I)
    {
        $I->amUser('staffrw2');

        $I->sendPOST("{$this->endpointProfile}", [
            'username' => 'staffrw2',
            'name' => 'Name Edited',
        ]);

        $I->canSeeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
            'status'  => 200,
        ]);
    }
}
