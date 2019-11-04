<?php

use app\components\CustomMigration;
use Jdsteam\Sapawarga\Rbac\Aspirasi;

/**
 * Class m191029_103454_edit_rbac_aspirasi
 * Create new permissions for Aspirasi module
 * Based on https://jabardigitalservice.gitbook.io/sapawarga/documentation/program-specification/aspirasi#role-and-permission
*/
class m191029_103454_edit_rbac_aspirasi extends CustomMigration
{
    private $_auth;

    private $_roleAdmin;
    private $_roleStaffProv;
    private $_roleStaffKabkota;
    private $_roleStaffKec;
    private $_roleStaffKel;
    private $_roleStaffRW;
    private $_roleUser;

    public function init()
    {
        $this->_auth = Yii::$app->authManager;

        $this->_roleAdmin = $this->_auth->getRole('admin');
        $this->_roleStaffProv = $this->_auth->getRole('staffProv');
        $this->_roleStaffKabkota = $this->_auth->getRole('staffKabkota');
        $this->_roleStaffKec = $this->_auth->getRole('staffKec');
        $this->_roleStaffKel = $this->_auth->getRole('staffKel');
        $this->_roleStaffRW = $this->_auth->getRole('staffRW');
        $this->_roleUser = $this->_auth->getRole('user');

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->removeOldPermissions();
        $this->createNewPermissionsWebadmin();
        $this->createNewPermissionsMobile();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->revertcreateNewPermissionsMobile();
        $this->revertcreateNewPermissionsWebadmin();
        $this->revertRemoveOldPermissions();
    }

    private function removeOldPermissions()
    {
        $aspirasiMobilePermission           = $this->_auth->getPermission('aspirasiMobile');
        $aspirasiWebadminViewPermission     = $this->_auth->getPermission('aspirasiWebadminView');
        $aspirasiWebadminManagePermission   = $this->_auth->getPermission('aspirasiWebadminManage');

        $this->_auth->removeChild($this->_roleUser, $aspirasiMobilePermission);
        $this->_auth->removeChild($this->_roleStaffRW, $aspirasiMobilePermission);

        $this->_auth->removeChild($this->_roleStaffKel, $aspirasiWebadminViewPermission);
        $this->_auth->removeChild($this->_roleStaffKec, $aspirasiWebadminViewPermission);
        $this->_auth->removeChild($this->_roleStaffKabkota, $aspirasiWebadminViewPermission);

        $this->_auth->removeChild($this->_roleStaffProv, $aspirasiWebadminManagePermission);
        $this->_auth->removeChild($this->_roleAdmin, $aspirasiWebadminManagePermission);

        $this->_auth->remove($aspirasiWebadminManagePermission);
        $this->_auth->remove($aspirasiWebadminViewPermission);
        $this->_auth->remove($aspirasiMobilePermission);
    }

    private function createNewPermissionsWebadmin()
    {
        $acceptRejectAspirasiRule = new Aspirasi\AcceptRejectAllAspirasiRule;
        $this->_auth->add($acceptRejectAspirasiRule);

        $viewAllAspirasiPermission              = $this->_auth->createPermission('viewAllAspirasi');
        $viewAllAspirasiPermission->description = 'Mengakses daftar dan detail semua Usulan';
        $this->_auth->add($viewAllAspirasiPermission);

        $viewAddressedAspirasiPermission              = $this->_auth->createPermission('viewAddressedAspirasi');
        $viewAddressedAspirasiPermission->description = 'Mengakses daftar dan detail Usulan yang dialamatkan kepada unitnya';
        $this->_auth->add($viewAddressedAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffProv, $viewAddressedAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffKabkota, $viewAddressedAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffKec, $viewAddressedAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffKel, $viewAddressedAspirasiPermission);

        $viewAddressedCascadedAspirasiPermission              = $this->_auth->createPermission('viewAddressedCascadedAspirasi');
        $viewAddressedCascadedAspirasiPermission->description = 'Mengakses daftar dan detail Usulan yang dialamatkan kepada unitnya dengan hirarki level di bawahnya';
        $this->_auth->add($viewAddressedCascadedAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffProv, $viewAddressedCascadedAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffKabkota, $viewAddressedCascadedAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffKec, $viewAddressedCascadedAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffKel, $viewAddressedCascadedAspirasiPermission);

        $acceptRejectAllAspirasiPermission              = $this->_auth->createPermission('acceptRejectAllAspirasi');
        $acceptRejectAllAspirasiPermission->description = 'Menerima/Menolak semua Usulan';
        $acceptRejectAllAspirasiPermission->ruleName    = $acceptRejectAspirasiRule->name;
        $this->_auth->add($acceptRejectAllAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffProv, $acceptRejectAllAspirasiPermission);
    }

    private function createNewPermissionsMobile()
    {
        $editOwnAspirasiRule = new Aspirasi\EditOwnAspirasiRule;
        $likeAspirasiRule = new Aspirasi\LikeAspirasiRule;
        $this->_auth->add($editOwnAspirasiRule);
        $this->_auth->add($likeAspirasiRule);

        $createAspirasiPermission              = $this->_auth->createPermission('createAspirasi');
        $createAspirasiPermission->description = 'Membuat Usulan Baru';
        $this->_auth->add($createAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffRW, $createAspirasiPermission);
        $this->_auth->addChild($this->_roleUser, $createAspirasiPermission);

        $viewOwnAspirasiPermission              = $this->_auth->createPermission('viewOwnAspirasi');
        $viewOwnAspirasiPermission->description = 'Mengakses daftar dan detail Usulan yang dibuat sendiri';
        $this->_auth->add($viewOwnAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffRW, $viewOwnAspirasiPermission);
        $this->_auth->addChild($this->_roleUser, $viewOwnAspirasiPermission);

        $editOwnAspirasiPermission              = $this->_auth->createPermission('editOwnAspirasi');
        $editOwnAspirasiPermission->description = 'Mengedit Usulan yang dibuat sendiri dan berstatus Draft atau Ditolak';
        $editOwnAspirasiPermission->ruleName    = $editOwnAspirasiRule->name;
        $this->_auth->add($editOwnAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffRW, $editOwnAspirasiPermission);
        $this->_auth->addChild($this->_roleUser, $editOwnAspirasiPermission);

        $deleteOwnAspirasiPermission              = $this->_auth->createPermission('deleteOwnAspirasi');
        $deleteOwnAspirasiPermission->description = 'Menghapus Usulan yang dibuat sendiri dan berstatus Draft atau Ditolak';
        $deleteOwnAspirasiPermission->ruleName    = $editOwnAspirasiRule->name;
        $this->_auth->add($deleteOwnAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffRW, $deleteOwnAspirasiPermission);
        $this->_auth->addChild($this->_roleUser, $deleteOwnAspirasiPermission);

        $likeAspirasiPermission              = $this->_auth->createPermission('likeAspirasi');
        $likeAspirasiPermission->description = 'Memberikan Like terhadap Usulan yang berstatus Dipublikasikan';
        $likeAspirasiPermission->ruleName    = $likeAspirasiRule->name;
        $this->_auth->add($likeAspirasiPermission);
        $this->_auth->addChild($this->_roleStaffRW, $likeAspirasiPermission);
        $this->_auth->addChild($this->_roleUser, $likeAspirasiPermission);
    }

    private function revertcreateNewPermissionsMobile()
    {
        $likeAspirasiPermission = $this->_auth->getPermission('likeAspirasi');
        $this->_auth->removeChild($this->_roleUser, $likeAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $likeAspirasiPermission);
        $this->_auth->remove($likeAspirasiPermission);

        $deleteOwnAspirasiPermission = $this->_auth->getPermission('deleteOwnAspirasi');
        $this->_auth->removeChild($this->_roleUser, $deleteOwnAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $deleteOwnAspirasiPermission);
        $this->_auth->remove($deleteOwnAspirasiPermission);

        $editOwnAspirasiPermission = $this->_auth->getPermission('editOwnAspirasi');
        $this->_auth->removeChild($this->_roleUser, $editOwnAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $editOwnAspirasiPermission);
        $this->_auth->remove($editOwnAspirasiPermission);

        $viewOwnAspirasiPermission = $this->_auth->getPermission('viewOwnAspirasi');
        $this->_auth->removeChild($this->_roleUser, $viewOwnAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $viewOwnAspirasiPermission);
        $this->_auth->remove($viewOwnAspirasiPermission);

        $createAspirasiPermission = $this->_auth->getPermission('createAspirasi');
        $this->_auth->removeChild($this->_roleUser, $createAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $createAspirasiPermission);
        $this->_auth->remove($createAspirasiPermission);

        $likeAspirasiRule = $this->_auth->getRule('canLikeAspirasi');
        $editOwnAspirasiRule = $this->_auth->getRule('canEditOwnAspirasi');
        $this->_auth->remove($likeAspirasiRule);
        $this->_auth->remove($editOwnAspirasiRule);
    }

    private function revertcreateNewPermissionsWebadmin()
    {
        $acceptRejectAllAspirasiPermission = $this->_auth->getPermission('acceptRejectAllAspirasi');
        $this->_auth->removeChild($this->_roleStaffProv, $acceptRejectAllAspirasiPermission);
        $this->_auth->remove($acceptRejectAllAspirasiPermission);

        $viewAddressedCascadedAspirasiPermission = $this->_auth->getPermission('viewAddressedCascadedAspirasi');
        $this->_auth->removeChild($this->_roleStaffKel, $viewAddressedCascadedAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffKec, $viewAddressedCascadedAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffKabkota, $viewAddressedCascadedAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffProv, $viewAddressedCascadedAspirasiPermission);
        $this->_auth->remove($viewAddressedCascadedAspirasiPermission);

        $viewAddressedAspirasiPermission = $this->_auth->getPermission('viewAddressedAspirasi');
        $this->_auth->removeChild($this->_roleStaffKel, $viewAddressedAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffKec, $viewAddressedAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffKabkota, $viewAddressedAspirasiPermission);
        $this->_auth->removeChild($this->_roleStaffProv, $viewAddressedAspirasiPermission);
        $this->_auth->remove($viewAddressedAspirasiPermission);

        $viewAllAspirasiPermission = $this->_auth->getPermission('viewAllAspirasi');
        $this->_auth->remove($viewAllAspirasiPermission);

        $acceptRejectAspirasiRule = $this->_auth->getRule('canAcceptRejectAllAspirasi');
        $this->_auth->remove($acceptRejectAspirasiRule);
    }

    private function revertRemoveOldPermissions()
    {
        $aspirasiMobilePermission              = $this->_auth->createPermission('aspirasiMobile');
        $aspirasiMobilePermission->description = 'View Published and My Aspirasi. Create, Update and Delete My Aspirasi Draft. Give Likes to Published Aspirasi.';
        $this->_auth->add($aspirasiMobilePermission);

        $aspirasiWebadminViewPermission              = $this->_auth->createPermission('aspirasiWebadminView');
        $aspirasiWebadminViewPermission->description = 'View Pending, Rejected, and Published Aspirasi.';
        $this->_auth->add($aspirasiWebadminViewPermission);

        $aspirasiWebadminManagePermission              = $this->_auth->createPermission('aspirasiWebadminManage');
        $aspirasiWebadminManagePermission->description = 'Manage Aspirasi (Full privileges)';
        $this->_auth->add($aspirasiWebadminManagePermission);

        $this->_auth->addChild($this->_roleAdmin, $aspirasiWebadminManagePermission);
        $this->_auth->addChild($this->_roleStaffProv, $aspirasiWebadminManagePermission);

        $this->_auth->addChild($this->_roleStaffKabkota, $aspirasiWebadminViewPermission);
        $this->_auth->addChild($this->_roleStaffKec, $aspirasiWebadminViewPermission);
        $this->_auth->addChild($this->_roleStaffKel, $aspirasiWebadminViewPermission);

        $this->_auth->addChild($this->_roleStaffRW, $aspirasiMobilePermission);
        $this->_auth->addChild($this->_roleUser, $aspirasiMobilePermission);
    }
}
