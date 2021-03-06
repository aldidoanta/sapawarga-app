<?php

namespace tests\unit\models;

use app\models\Notification;

class NotificationTest extends \Codeception\Test\Unit
{
    public function testValidateFillRequired()
    {
        $model = new Notification();

        $this->assertFalse($model->validate());

        $model->title       = 'test';
        $model->description = 'test';

        $model->kabkota_id  = 1;
        $model->kec_id      = 1;
        $model->kel_id      = 1;
        $model->rw          = '001';
        $model->status      = 10;

        $this->assertTrue($model->validate());
    }

    public function testValidateRequired()
    {
        $model = new Notification();

        $model->title       = null;
        $model->description = null;

        $model->kabkota_id  = null;
        $model->kec_id      = null;
        $model->kel_id      = null;
        $model->rw          = null;
        $model->status      = null;

        $this->assertFalse($model->validate());

        $this->assertTrue($model->hasErrors('title'));
        $this->assertTrue($model->hasErrors('status'));

        $this->assertFalse($model->hasErrors('description'));
        $this->assertFalse($model->hasErrors('kabkota_id'));
        $this->assertFalse($model->hasErrors('kec_id'));
        $this->assertFalse($model->hasErrors('kel_id'));
        $this->assertFalse($model->hasErrors('rw'));
    }

    public function testTitleMaximumCharactersShouldSuccess()
    {
        $model        = new Notification();
        $model->title = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean m';

        $model->validate();

        $this->assertFalse($model->hasErrors('title'));
    }

    public function testTitleMaximumCharactersShouldFail()
    {
        $model        = new Notification();
        $model->title = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean mx';
        $model->validate();

        $this->assertTrue($model->hasErrors('title'));
    }

    public function testTitleNotSafeShouldFail()
    {
        $model = new Notification();

        $model->title = '<script>alert()</script>';

        $model->validate();

        $this->assertTrue($model->hasErrors('title'));
    }

    public function testDescriptionMaximumCharactersShouldSuccess()
    {
        $model              = new Notification();
        $model->description = file_get_contents(__DIR__ . '/../../data/1000chars.txt');
        $model->validate();

        $this->assertFalse($model->hasErrors('description'));
    }

    public function testDescriptionMaximumCharactersShouldFail()
    {
        $model              = new Notification();
        $model->description = file_get_contents(__DIR__ . '/../../data/1000chars.txt') . 'x';
        $model->validate();

        $this->assertTrue($model->hasErrors('description'));
    }

    public function testDescriptionNotSafeShouldFail()
    {
        $model = new Notification();

        $model->description = '<script>alert()</script>';

        $model->validate();

        $this->assertTrue($model->hasErrors('description'));
    }

    public function testRWValidation()
    {
        $model       = new Notification();
        $model->rw   = '1';
        $model->validate();
        $this->assertTrue($model->hasErrors('rw'));

        $model->rw   = '0001';
        $model->validate();
        $this->assertTrue($model->hasErrors('rw'));

        $model->rw   = '_a1A.';
        $model->validate();
        $this->assertTrue($model->hasErrors('rw'));

        $model->rw   = '001';
        $model->validate();
        $this->assertFalse($model->hasErrors('rw'));
    }

    public function testKabupatenValid()
    {
        $model             = new Notification();
        $model->kabkota_id = 22;
        $model->kec_id     = null;
        $model->kel_id     = null;
        $model->rw         = null;

        $model->category_id = 14;

        $model->save(false);

        $this->assertNotNull($model->kabkota_id);
        $this->assertNull($model->kec_id);
        $this->assertNull($model->kel_id);
        $this->assertNull($model->rw);
    }

    public function testKecamatanValid()
    {
        $model             = new Notification();
        $model->kabkota_id = 22;
        $model->kec_id     = 431;
        $model->kel_id     = null;
        $model->rw         = null;

        $model->category_id = 14;

        $model->save(false);

        $this->assertNotNull($model->kabkota_id);
        $this->assertNotNull($model->kec_id);
        $this->assertNull($model->kel_id);
        $this->assertNull($model->rw);
    }

    public function testKelurahanValid()
    {
        $model             = new Notification();
        $model->kabkota_id = 22;
        $model->kec_id     = 431;
        $model->kel_id     = 6093;
        $model->rw         = null;

        $model->category_id = 14;

        $model->save(false);

        $this->assertNotNull($model->kabkota_id);
        $this->assertNotNull($model->kec_id);
        $this->assertNotNull($model->kel_id);
        $this->assertNull($model->rw);
    }

    public function testRwValid()
    {
        $model             = new Notification();
        $model->kabkota_id = 22;
        $model->kec_id     = 431;
        $model->kel_id     = 6093;
        $model->rw         = '001';

        $model->category_id = 14;

        $model->save(false);

        $this->assertNotNull($model->kabkota_id);
        $this->assertNotNull($model->kec_id);
        $this->assertNotNull($model->kel_id);
        $this->assertNotNull($model->rw);
    }

    public function testKecamatanShouldNull()
    {
        $model             = new Notification();
        $model->kabkota_id = null;
        $model->kec_id     = 431;
        $model->kel_id     = 6093;
        $model->rw         = '001';

        $model->category_id = 14;

        $model->save(false);

        $this->assertNull($model->kabkota_id);
        $this->assertNull($model->kec_id);
        $this->assertNull($model->kel_id);
        $this->assertNull($model->rw);
    }

    public function testKelurahanShouldNull()
    {
        $model             = new Notification();
        $model->kabkota_id = 22;
        $model->kec_id     = null;
        $model->kel_id     = 6093;
        $model->rw         = '001';

        $model->category_id = 14;

        $model->save(false);

        $this->assertNotNull($model->kabkota_id);
        $this->assertNull($model->kec_id);
        $this->assertNull($model->kel_id);
        $this->assertNull($model->rw);
    }

    public function testRwShouldNull()
    {
        $model             = new Notification();
        $model->kabkota_id = 22;
        $model->kec_id     = 431;
        $model->kel_id     = null;
        $model->rw         = '001';

        $model->category_id = 14;

        $model->save(false);

        $this->assertNotNull($model->kabkota_id);
        $this->assertNotNull($model->kec_id);
        $this->assertNull($model->kel_id);
        $this->assertNull($model->rw);
    }
}
