<?php

namespace tests\unit\models;

use app\models\VideoFeatured;
use Codeception\Test\Unit;

class VideoFeaturedTest extends Unit
{
    public function testValidateShouldRequired()
    {
        $model = new VideoFeatured();

        $model->validate();

        $this->assertTrue($model->hasErrors('video_id'));
        $this->assertTrue($model->hasErrors('seq'));
        $this->assertFalse($model->hasErrors('kabkota_id'));
    }

    public function testValidateVideoIdShouldInteger()
    {
        $model = new VideoFeatured();
        $model->video_id = 1;
        $model->validate();

        $this->assertFalse($model->hasErrors('video_id'));

        $model = new VideoFeatured();
        $model->video_id = 'ok';
        $model->validate();

        $this->assertTrue($model->hasErrors('video_id'));
    }

    public function testValidateSequenceShouldInteger()
    {
        $model = new VideoFeatured();
        $model->seq = 1;
        $model->validate();

        $this->assertFalse($model->hasErrors('seq'));

        $model = new VideoFeatured();
        $model->seq = 'ok';
        $model->validate();

        $this->assertTrue($model->hasErrors('seq'));
    }

    public function testValidateKabkotaIdShouldInteger()
    {
        $model = new VideoFeatured();
        $model->kabkota_id = 1;
        $model->validate();

        $this->assertFalse($model->hasErrors('kabkota_id'));

        $model = new VideoFeatured();
        $model->kabkota_id = 'ok';
        $model->validate();

        $this->assertTrue($model->hasErrors('kabkota_id'));
    }
}
