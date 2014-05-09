<?php

namespace Heyday\QueryBuilder\QueryModifiers;

use Heyday\QueryBuilder\Interfaces\QueryModifierInterface;

class PaginationQueryModifier implements QueryModifierInterface
{
    protected $limit;
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

    protected function getLimit()
    {
        return $this->start . ', ' . (int) $this->limit;
    }

    public function modify(\SQLQuery $query, array $data)
    {
        $query->setLimit($this->getLimit());

        return $query;
    }
}