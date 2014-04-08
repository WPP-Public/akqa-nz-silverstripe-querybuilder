<?php

namespace Heyday\QueryBuilder\QueryModifiers;

use Heyday\QueryBuilder\Interfaces\QueryModifierInterface;

class DistinctQueryModifier implements QueryModifierInterface
{
    public function modify(\SQLQuery $query, array $data)
    {
        $query->setDistinct(true);

        return $query;
    }
}
