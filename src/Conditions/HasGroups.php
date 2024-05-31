<?php

namespace DT\Autolink\Conditions;

use DT\Autolink\CodeZone\Router\Conditions\Condition;

class HasGroups implements Condition
{
    public function test(): bool
    {
        $groups  = \DT_Posts::list_posts( 'groups', [
            'assigned_to' => [ get_current_user_id() ],
            'limit'       => 1
        ], false );

        return $groups['total'] >= 1;
    }
}
