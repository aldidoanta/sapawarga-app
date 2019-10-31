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

        $viewAllUsulanPermission              = $this->_auth->createPermission('viewAllUsulan');
        $viewAllUsulanPermission->description = 'Mengakses daftar dan detail semua Usulan';
        $this->_auth->add($viewAllUsulanPermission);

        $viewAddressedUsulanPermission              = $this->_auth->createPermission('viewAddressedUsulan');
        $viewAddressedUsulanPermission->description = 'Mengakses daftar dan detail Usulan yang dialamatkan kepada unitnya';
        $this->_auth->add($viewAddressedUsulanPermission);
        $this->_auth->addChild($this->_roleStaffProv, $viewAddressedUsulanPermission);
        $this->_auth->addChild($this->_roleStaffKabkota, $viewAddressedUsulanPermission);
        $this->_auth->addChild($this->_roleStaffKec, $viewAddressedUsulanPermission);
        $this->_auth->addChild($this->_roleStaffKel, $viewAddressedUsulanPermission);

        $viewAddressedCascadedUsulanPermission              = $this->_auth->createPermission('viewAddressedCascadedUsulan');
        $viewAddressedCascadedUsulanPermission->description = 'Mengakses daftar dan detail Usulan yang dialamatkan kepada unitnya dengan hirarki level di bawahnya';
        $this->_auth->add($viewAddressedCascadedUsulanPermission);
        $this->_auth->addChild($this->_roleStaffProv, $viewAddressedCascadedUsulanPermission);
        $this->_auth->addChild($this->_roleStaffKabkota, $viewAddressedCascadedUsulanPermission);
        $this->_auth->addChild($this->_roleStaffKec, $viewAddressedCascadedUsulanPermission);
        $this->_auth->addChild($this->_roleStaffKel, $viewAddressedCascadedUsulanPermission);

        $acceptRejectAllUsulanPermission              = $this->_auth->createPermission('acceptRejectAllUsulan');
        $acceptRejectAllUsulanPermission->description = 'Menerima/Menolak semua Usulan';
        $acceptRejectAllUsulanPermission->ruleName    = $acceptRejectAspirasiRule->name;
        $this->_auth->add($acceptRejectAllUsulanPermission);
        $this->_auth->addChild($this->_roleStaffProv, $acceptRejectAllUsulanPermission);

    }

    private function createNewPermissionsMobile()
    {
        $viewOwnAspirasiRule = new Aspirasi\ViewOwnAspirasiRule;
        $editOwnAspirasiRule = new Aspirasi\EditOwnAspirasiRule;
        $likeAspirasiRule = new Aspirasi\LikeAspirasiRule;
        $this->_auth->add($viewOwnAspirasiRule);
        $this->_auth->add($editOwnAspirasiRule);
        $this->_auth->add($likeAspirasiRule);

        $createUsulanPermission              = $this->_auth->createPermission('createUsulan');
        $createUsulanPermission->description = 'Membuat Usulan Baru';
        $this->_auth->add($createUsulanPermission);
        $this->_auth->addChild($this->_roleStaffRW, $createUsulanPermission);
        $this->_auth->addChild($this->_roleUser, $createUsulanPermission);

        $viewOwnUsulanPermission              = $this->_auth->createPermission('viewOwnUsulan');
        $viewOwnUsulanPermission->description = 'Mengakses daftar dan detail Usulan yang dibuat sendiri';
        $viewOwnUsulanPermission->ruleName    = $viewOwnAspirasiRule->name;
        $this->_auth->add($viewOwnUsulanPermission);
        $this->_auth->addChild($this->_roleStaffRW, $viewOwnUsulanPermission);
        $this->_auth->addChild($this->_roleUser, $viewOwnUsulanPermission);

        $editOwnUsulanPermission              = $this->_auth->createPermission('editOwnUsulan');
        $editOwnUsulanPermission->description = 'Mengedit Usulan yang dibuat sendiri dan berstatus Draft atau Ditolak';
        $editOwnUsulanPermission->ruleName    = $editOwnAspirasiRule->name;
        $this->_auth->add($editOwnUsulanPermission);
        $this->_auth->addChild($this->_roleStaffRW, $editOwnUsulanPermission);
        $this->_auth->addChild($this->_roleUser, $editOwnUsulanPermission);

        $deleteOwnUsulanPermission              = $this->_auth->createPermission('deleteOwnUsulan');
        $deleteOwnUsulanPermission->description = 'Menghapus Usulan yang dibuat sendiri dan berstatus Draft atau Ditolak';
        $deleteOwnUsulanPermission->ruleName    = $editOwnAspirasiRule->name;
        $this->_auth->add($deleteOwnUsulanPermission);
        $this->_auth->addChild($this->_roleStaffRW, $deleteOwnUsulanPermission);
        $this->_auth->addChild($this->_roleUser, $deleteOwnUsulanPermission);

        $likeUsulanPermission              = $this->_auth->createPermission('likeUsulan');
        $likeUsulanPermission->description = 'Memberikan Like terhadap Usulan yang berstatus Dipublikasikan';
        $likeUsulanPermission->ruleName    = $likeAspirasiRule->name;
        $this->_auth->add($likeUsulanPermission);
        $this->_auth->addChild($this->_roleStaffRW, $likeUsulanPermission);
        $this->_auth->addChild($this->_roleUser, $likeUsulanPermission);
    }

    private function revertcreateNewPermissionsMobile()
    {
        $likeUsulanPermission = $this->_auth->getPermission('likeUsulan');
        $this->_auth->removeChild($this->_roleUser, $likeUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $likeUsulanPermission);
        $this->_auth->remove($likeUsulanPermission);

        $deleteOwnUsulanPermission = $this->_auth->getPermission('deleteOwnUsulan');
        $this->_auth->removeChild($this->_roleUser, $deleteOwnUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $deleteOwnUsulanPermission);
        $this->_auth->remove($deleteOwnUsulanPermission);

        $editOwnUsulanPermission = $this->_auth->getPermission('editOwnUsulan');
        $this->_auth->removeChild($this->_roleUser, $editOwnUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $editOwnUsulanPermission);
        $this->_auth->remove($editOwnUsulanPermission);

        $viewOwnUsulanPermission = $this->_auth->getPermission('viewOwnUsulan');
        $this->_auth->removeChild($this->_roleUser, $viewOwnUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $viewOwnUsulanPermission);
        $this->_auth->remove($viewOwnUsulanPermission);

        $createUsulanPermission = $this->_auth->getPermission('createUsulan');
        $this->_auth->removeChild($this->_roleUser, $createUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffRW, $createUsulanPermission);
        $this->_auth->remove($createUsulanPermission);

        $likeAspirasiRule = $this->_auth->getRule('canLikeAspirasi');
        $editOwnAspirasiRule = $this->_auth->getRule('canEditOwnAspirasi');
        $viewOwnAspirasiRule = $this->_auth->getRule('canViewOwnAspirasi');
        $this->_auth->remove($likeAspirasiRule);
        $this->_auth->remove($editOwnAspirasiRule);
        $this->_auth->remove($viewOwnAspirasiRule);
    }

    private function revertcreateNewPermissionsWebadmin()
    {
        $acceptRejectAllUsulanPermission = $this->_auth->getPermission('acceptRejectAllUsulan');
        $this->_auth->removeChild($this->_roleStaffProv, $acceptRejectAllUsulanPermission);
        $this->_auth->remove($acceptRejectAllUsulanPermission);

        $viewAddressedCascadedUsulanPermission = $this->_auth->getPermission('viewAddressedCascadedUsulan');
        $this->_auth->removeChild($this->_roleStaffKel, $viewAddressedCascadedUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffKec, $viewAddressedCascadedUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffKabkota, $viewAddressedCascadedUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffProv, $viewAddressedCascadedUsulanPermission);
        $this->_auth->remove($viewAddressedCascadedUsulanPermission);

        $viewAddressedUsulanPermission = $this->_auth->getPermission('viewAddressedUsulan');
        $this->_auth->removeChild($this->_roleStaffKel, $viewAddressedUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffKec, $viewAddressedUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffKabkota, $viewAddressedUsulanPermission);
        $this->_auth->removeChild($this->_roleStaffProv, $viewAddressedUsulanPermission);
        $this->_auth->remove($viewAddressedUsulanPermission);

        $viewAllUsulanPermission = $this->_auth->getPermission('viewAllUsulan');
        $this->_auth->remove($viewAllUsulanPermission);

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
