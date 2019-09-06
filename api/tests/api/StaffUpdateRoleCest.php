<?php

class StaffUpdateRoleCest
{
    private $endpointStaff = '/v1/staff';

    public function _before(ApiTester $I)
    {
        $I->haveInDatabase('user', [
            'id'            => 10000,
            'username'      => 'tester.staffprov',
            'password_hash' => '$2y$13$UF1u00zQepfWOyhRcjrvIefJ5f6PI5tRTxyOP9Zw6OFBLfo8H8tIu',
            'name'          => 'tester.staffprov',
            'email'         => 'staffprov@test.org',
            'role'          => 90,
            'status'        => 10,
            'confirmed_at'  => 1553010000,
            'created_at'    => 1553010000,
            'updated_at'    => 1553010000,
        ]);

        $I->haveInDatabase('auth_assignment', [
            'user_id'    => 10000,
            'item_name'  => 'staffProv',
            'created_at' => 1553010000,
        ]);
    }

    public function staffProvinsiCannotChangeRoleStaffKabKotaToAdmin(ApiTester $I)
    {
        $I->haveInDatabase('user', [
            'id'          => 1000,
            'username'    => 'user1',
            'name'        => 'user1',
            'email'       => 'user1@test.org',
            'role'        => 80,
            'created_at'  => 1553010000,
            'updated_at'  => 1553010000,
        ]);

        $I->haveInDatabase('auth_assignment', [
            'user_id'    => 1000,
            'item_name'  => 'staffKabkota',
            'created_at' => 1553010000,
        ]);

        $I->amStaff('tester.staffprov');

        $I->sendPUT("{$this->endpointStaff}/1000", [
            'username' => 'user1',
            'name'     => 'user1',
            'email'    => 'user1@test.org',
            'role_id'  => 'admin',
        ]);

        $I->canSeeResponseCodeIs(422);
    }

    public function staffProvinsiCannotChangeRoleStaffKabKotaToStaffProv(ApiTester $I)
    {
        $I->haveInDatabase('user', [
            'id'          => 1000,
            'username'    => 'user1',
            'name'        => 'user1',
            'email'       => 'user1@test.org',
            'role'        => 80,
            'created_at'  => 1553010000,
            'updated_at'  => 1553010000,
        ]);

        $I->haveInDatabase('auth_assignment', [
            'user_id'    => 1000,
            'item_name'  => 'staffKabkota',
            'created_at' => 1553010000,
        ]);

        $I->amStaff('tester.staffprov');

        $I->sendPUT("{$this->endpointStaff}/1000", [
            'username' => 'user1',
            'name'     => 'user1',
            'email'    => 'user1@test.org',
            'role_id'  => 'staffProv',
        ]);

        $I->canSeeResponseCodeIs(422);
    }

    public function staffProvinsiCanChangeRoleStaffKabKotaToStaffKec(ApiTester $I)
    {
        $I->haveInDatabase('user', [
            'id'          => 1000,
            'username'    => 'user1',
            'name'        => 'user1',
            'email'       => 'user1@test.org',
            'role'        => 80,
            'created_at'  => 1553010000,
            'updated_at'  => 1553010000,
        ]);

        $I->haveInDatabase('auth_assignment', [
            'user_id'    => 1000,
            'item_name'  => 'staffKabkota',
            'created_at' => 1553010000,
        ]);

        $I->amStaff('tester.staffprov');

        $I->sendPUT("{$this->endpointStaff}/1000", [
            'username' => 'user1',
            'name'     => 'user1',
            'email'    => 'user1@test.org',
            'role_id'  => 'staffKec',
        ]);

        $I->canSeeResponseCodeIs(200);

        $I->seeInDatabase('user', [
            'id'       => 1000,
            'username' => 'user1',
            'name'     => 'user1',
            'role'     => 70,
        ]);

        $I->seeInDatabase('auth_assignment', [
            'user_id'   => 1000,
            'item_name' => 'staffKec',
        ]);
    }

    public function staffProvinsiCanChangeRoleStaffKabKotaToStaffKel(ApiTester $I)
    {
        $I->haveInDatabase('user', [
            'id'          => 1000,
            'username'    => 'user1',
            'name'        => 'user1',
            'email'       => 'user1@test.org',
            'role'        => 80,
            'created_at'  => 1553010000,
            'updated_at'  => 1553010000,
        ]);

        $I->haveInDatabase('auth_assignment', [
            'user_id'    => 1000,
            'item_name'  => 'staffKabkota',
            'created_at' => 1553010000,
        ]);


        $I->amStaff('tester.staffprov');

        $I->sendPUT("{$this->endpointStaff}/1000", [
            'username' => 'user1',
            'name'     => 'user1',
            'email'    => 'user1@test.org',
            'role_id'  => 'staffKel',
        ]);

        $I->canSeeResponseCodeIs(200);

        $I->seeInDatabase('user', [
            'id'       => 1000,
            'username' => 'user1',
            'name'     => 'user1',
            'role'     => 60,
        ]);

        $I->seeInDatabase('auth_assignment', [
            'user_id'   => 1000,
            'item_name' => 'staffKel',
        ]);
    }
}
