<?php

namespace Heyday\QueryBuilder\QueryModifiers;

use Heyday\QueryBuilder\Interfaces\QueryModifierInterface;
use Heyday\QueryBuilder\Interfaces\QueryBuilderInterface;

/**
 * @package Heyday\QueryBuilder\QueryModifiers
 */
class DistinctQueryModifier implements QueryModifierInterface
{
    /**
     * @param \SQLQuery $query
     * @param array $data
     * @return \SQLQuery
     */
    public function modify(\SQLQuery $query, array $data, QueryBuilderInterface $queryBuilder)
    {
        $query->setDistinct(true);

        return $query;
    }
}
