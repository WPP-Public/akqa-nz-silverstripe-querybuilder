<?php

namespace Heyday\QueryBuilder;

/**
 * @package Heyday\QueryBuilder
 */
class UnionQueryBuilder extends QueryBuilder
{
    /** @var \Heyday\QueryBuilder\QueryBuilder[] */
    protected $queryBuilders = [];

    /**
     * @param null $dataClass
     * @param \Heyday\QueryBuilder\QueryBuilder[] $queryBuilders
     * @param \Heyday\QueryBuilder\Interfaces\QueryModifierInterface[] $queryModifiers
     * @param array $modifierData
     */
    public function __construct(
        $dataClass = null,
        array $queryBuilders = [],
        array $queryModifiers = [],
        array $modifierData = []
    )
    {
        $this->dataClass = $dataClass;
        $this->setQueryBuilders($queryBuilders);
        $this->setQueryModifiers($queryModifiers);
        $this->setData($modifierData);
    }

    /**
     * @return \SQLQuery
     */
    public function getQuery()
    {
        $query = parent::getQuery();

        $query->setWhere("");
        $query->setSelect("*");
        $query->setFrom(sprintf(
            "(%s) x",
            $this->getUnionedQuery()
        ));
        
        return $query;
    }

    /**
     * @return string
     */
    protected function getUnionedQuery()
    {
        return sprintf(
            "(%s)",
            implode(
                ") UNION (",
                $this->getQueriesSql()
            )
        );
    }

    /**
     * @return array
     */
    protected function getQueriesSql()
    {
        return array_map(function (QueryBuilder $queryBuilder) {
            return $queryBuilder->setData($this->data)->getQuery()->sql();
        }, $this->queryBuilders);
    }

    /**
     * @return \Heyday\QueryBuilder\QueryBuilder[]
     */
    public function getQueryBuilders()
    {
        return $this->queryBuilders;
    }

    /**
     * @param \Heyday\QueryBuilder\QueryBuilder[] $queryBuilders
     * @return \Heyday\QueryBuilder\UnionQueryBuilder
     */
    public function setQueryBuilders($queryBuilders)
    {
        $this->queryBuilders = [];

        foreach ($queryBuilders as $queryBuilder) {
            $this->addQueryBuilder($queryBuilder);
        }
        
        return $this;
    }

    /**
     * @param \Heyday\QueryBuilder\QueryBuilder $queryBuilder
     * @return \Heyday\QueryBuilder\UnionQueryBuilder
     */
    public function addQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilders[] = $queryBuilder;

        return $this;
    }
}