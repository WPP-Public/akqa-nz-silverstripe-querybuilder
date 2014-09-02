<?php

namespace Heyday\QueryBuilder\QueryModifiers;

use Heyday\QueryBuilder\Interfaces\QueryModifierInterface;

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
    public function modify(\SQLQuery $query, array $data, \Heyday\QueryBuilder\Interfaces\QueryBuilderInterface $queryBuilder)
    {
        $query->setDistinct(true);

        return $query;
    }
}
