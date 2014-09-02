<?php

namespace Heyday\QueryBuilder\Interfaces;

/**
 * @package Heyday\QueryBuilder\Interfaces
 */
interface QueryModifierInterface
{
    /**
     * @param \SQLQuery $query
     * @param array $data
     * @param \Heyday\QueryBuilder\Interfaces\QueryBuilderInterface $queryBuilder
     * @return mixed
     */
    public function modify(\SQLQuery $query, array $data, QueryBuilderInterface $queryBuilder);
}