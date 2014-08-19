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
     * @return mixed
     */
    public function modify(\SQLQuery $query, array $data);
}