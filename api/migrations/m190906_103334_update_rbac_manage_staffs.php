<?php

use app\components\CustomMigration;

/**
 * Class m190906_103334_update_rbac_manage_staffs */
class m190906_103334_update_rbac_manage_staffs extends CustomMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $manageStaffsPermission = $auth->getPermission('manageStaffs');
        $manageUsersPermission = $auth->getPermission('manageUsers');

        $role = $auth->getRole('staffProv');
        $auth->addChild($role, $manageStaffsPermission);
        $auth->addChild($role, $manageUsersPermission);

        $role = $auth->getRole('staffKabkota');
        $auth->addChild($role, $manageStaffsPermission);
        $auth->addChild($role, $manageUsersPermission);

        $role = $auth->getRole('staffKec');
        $auth->addChild($role, $manageStaffsPermission);
        $auth->addChild($role, $manageUsersPermission);

        $role = $auth->getRole('staffKel');
        $auth->addChild($role, $manageStaffsPermission);
        $auth->addChild($role, $manageUsersPermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190906_103334_update_rbac_manage_staffs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190906_103334_update_rbac_manage_staffs cannot be reverted.\n";

        return false;
    }
    */
}
