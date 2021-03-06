<?php

namespace tests\unit\models;

use app\models\Message;

class MessageTest extends \Codeception\Test\Unit
{
    public function testValidateFillRequired()
    {
        $model = new Message();

        $this->assertFalse($model->validate());

        $model->title       = 'test';
        $model->description = 'test';

        $this->assertTrue($model->validate());
    }

    public function testValidateRequired()
    {
        $model = new Message();

        $model->title       = null;
        $model->description = null;

        $this->assertFalse($model->validate());

        $this->assertTrue($model->hasErrors('title'));
    }

    public function testTitleTooLong()
    {
        $model        = new Message();
        $model->title = 'l5WdQPtZ8J6pEf1vZl2eynOuFP4g8qHXXeyYvE4h0y72QCesFoSyQUGqmVKpbv3tqeqITczwX1DLIFKdX6CjrCoNbJiOGQXzaDhRB5Q45wtPl9OaZDa6dz4jzk58GsXQfAiihEGd9nKCudaoXSBqxf2AITfSlAJ0kH2KaikUBOLrMHniFBPbIs2byQTPVqQ8FbwfJoxjJyga4oSHxPQlDVdgb9DQ1LI3fIMklRHc4itLdlsmOWBxcEVw47nPoIQ6';

        $model->validate();

        $this->assertTrue($model->hasErrors('title'));
    }

    public function testTitleMaxCharactersValid()
    {
        $model        = new Message();
        $model->title = 'Message Title';
        $model->validate();

        $this->assertFalse($model->hasErrors('title'));

        $model->title = 'l5WdQPtZ8J6pEf1vZl2eynOuFP4g8qHXXeyYvE4h0y72QCesFoSyQUGqmVKpbv3tqeqITczwX1DLIFKdX6CjrCoNbJiOGQXzaDhRB5Q45wtPl9OaZDa6dz4jzk58GsXQfAiihEGd9nKCudaoXSBqxf2AITfSlAJ0kH2KaikUBOLrMHniFBPbIs2byQTPVqQ8FbwfJoxjJyga4oSHxPQlDVdgb9DQ1LI3fIMklRHc4itLdlsmOWBxcEVw47nPoIQ';
        $model->validate();

        $this->assertFalse($model->hasErrors('title'));
    }
}
