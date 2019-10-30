<?php

use app\components\CustomMigration;
use Jdsteam\Sapawarga\Rbac\Aspirasi;

/**
 * Class m191029_103454_edit_rbac_aspirasi */
class m191029_103454_edit_rbac_aspirasi extends CustomMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->removeOldPermissions();


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->revertRemoveOldPermissions();
    }

    private function removeOldPermissions()
    {
        $auth = Yii::$app->authManager;

        $aspirasiMobilePermission           = $auth->getPermission('aspirasiMobile');
        $aspirasiWebadminViewPermission     = $auth->getPermission('aspirasiWebadminView');
        $aspirasiWebadminManagePermission   = $auth->getPermission('aspirasiWebadminManage');

        $auth->removeChild($this->_roleUser, $aspirasiMobilePermission);
        $auth->removeChild($this->_roleStaffRW, $aspirasiMobilePermission);

        $auth->removeChild($this->_roleStaffKel, $aspirasiWebadminViewPermission);
        $auth->removeChild($this->_roleStaffKec, $aspirasiWebadminViewPermission);
        $auth->removeChild($this->_roleStaffKabkota, $aspirasiWebadminViewPermission);

        $auth->removeChild($this->_roleStaffProv, $aspirasiWebadminManagePermission);
        $auth->removeChild($this->_roleAdmin, $aspirasiWebadminManagePermission);

        $auth->remove($aspirasiWebadminManagePermission);
        $auth->remove($aspirasiWebadminViewPermission);
        $auth->remove($aspirasiMobilePermission);
    }

    private function revertRemoveOldPermissions()
    {
        $auth = Yii::$app->authManager;

        $aspirasiMobilePermission              = $auth->createPermission('aspirasiMobile');
        $aspirasiMobilePermission->description = 'View Published and My Aspirasi. Create, Update and Delete My Aspirasi Draft. Give Likes to Published Aspirasi.';
        $auth->add($aspirasiMobilePermission);

        $aspirasiWebadminViewPermission              = $auth->createPermission('aspirasiWebadminView');
        $aspirasiWebadminViewPermission->description = 'View Pending, Rejected, and Published Aspirasi.';
        $auth->add($aspirasiWebadminViewPermission);

        $aspirasiWebadminManagePermission              = $auth->createPermission('aspirasiWebadminManage');
        $aspirasiWebadminManagePermission->description = 'Manage Aspirasi (Full privileges)';
        $auth->add($aspirasiWebadminManagePermission);

        $auth->addChild($this->_roleAdmin, $aspirasiWebadminManagePermission);
        $auth->addChild($this->_roleStaffProv, $aspirasiWebadminManagePermission);

        $auth->addChild($this->_roleStaffKabkota, $aspirasiWebadminViewPermission);
        $auth->addChild($this->_roleStaffKec, $aspirasiWebadminViewPermission);
        $auth->addChild($this->_roleStaffKel, $aspirasiWebadminViewPermission);

        $auth->addChild($this->_roleStaffRW, $aspirasiMobilePermission);
        $auth->addChild($this->_roleUser, $aspirasiMobilePermission);
    }
}
