<?php

namespace Heyday\QueryBuilder\QueryModifiers;

use Heyday\QueryBuilder\Interfaces\QueryModifierInterface;
use Heyday\QueryBuilder\Interfaces\QueryBuilderInterface;

/**
 * @package Heyday\QueryBuilder\QueryModifiers
 */
class PaginationQueryModifier implements QueryModifierInterface
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $start;

    /**
     * @param $limit
     * @param $start
     */
    public function __construct($limit = 20, $start = 0)
    {
        $this->limit = $limit;
        $this->start = $start;
    }

    /**
     * @return string
     */
    protected function getLimit()
    {
        return $this->start . ', ' . (int) $this->limit;
    }

    /**
     * @param \SQLQuery $query
     * @param array $data
     * @return \SQLQuery
     */
    public function modify(\SQLQuery $query, array $data, QueryBuilderInterface $queryBuilder)
    {
        $query->setLimit($this->getLimit());

        return $query;
    }
}