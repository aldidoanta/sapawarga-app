<?php

namespace Jdsteam\Sapawarga\Rbac;

use app\models\Aspirasi;
use yii\rbac\Rule;

/**
 * Checks if status of an aspirasi is Pending
 */
class AspirasiDeleteRule extends Rule
{
    public $name = 'canDeleteAspirasi';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['aspirasi'])
            ? $params['aspirasi']->status == Aspirasi::STATUS_DRAFT
                || $params['aspirasi']->status == Aspirasi::STATUS_APPROVAL_REJECTED
            : false;
    }
}