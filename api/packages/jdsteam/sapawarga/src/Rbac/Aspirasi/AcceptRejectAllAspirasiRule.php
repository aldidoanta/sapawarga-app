<?php

namespace Jdsteam\Sapawarga\Rbac\Aspirasi;

use app\models\Aspirasi;
use yii\rbac\Rule;

/**
 * Rule containing logic to accept/reject all Usulan/Aspirasi with pending status
 */
class AcceptRejectAllAspirasiRule extends Rule
{
    public $name = 'canAcceptRejectAllAspirasi';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['aspirasi'])
            ? $params['aspirasi']->status == Aspirasi::STATUS_APPROVAL_PENDING
            : false;
    }
}