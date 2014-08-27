<?php

namespace Heyday\QueryBuilder;

use Heyday\QueryBuilder\Interfaces\QueryBuilderInterface;
use Heyday\QueryBuilder\Interfaces\QueryModifierInterface;

/**
 * @package Heyday\QueryBuilder
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * @var \Heyday\QueryBuilder\Interfaces\QueryModifierInterface[]
     */
    protected $queryModifiers = [];
    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var bool
     */
    protected $listCache = false;
    /**
     * @var bool
     */
    protected $queryCache = false;
    /**
     * @var null
     */
    protected $dataClass;

    /**
     * @param null $dataClass
     * @param array $queryModifiers
     * @param array $modifierData
     */
    public function __construct($dataClass = null, array $queryModifiers = [], array $modifierData = [])
    {
        $this->dataClass = $dataClass;
        $this->setQueryModifiers($queryModifiers);
        $this->setData($modifierData);
    }

    /**
     *
     */
    protected function invalidateCache()
    {
        $this->listCache = false;
        $this->queryCache = false;
    }

    /**
     * @return \SQLQuery
     */
    public function getQuery()
    {
        if (!$this->queryCache) {
            if ($this->dataClass) {
                $dataQuery = new \DataQuery($this->dataClass);
                $this->queryCache = $dataQuery->getFinalisedQuery();
            } else {
                $this->queryCache = new \SQLQuery();
            }

            if (is_array($this->queryModifiers)) {
                foreach ($this->queryModifiers as $queryModifier) {
                    if ($queryModifier instanceof QueryModifierInterface) {
                        $queryModifier->modify($this->queryCache, $this->data);
                    } elseif (is_callable($queryModifier)) {
                        $queryModifier($this->queryCache, $this->data);
                    }
                }
            }
        }

        return $this->queryCache;
    }

    /**
     * @return \ArrayList
     */
    public function getList()
    {
        if ($this->listCache === false) {
            $rows = $this->getQuery()->execute();
            $results = [];

            foreach ($rows as $row) {
                if ($do = createDataObject($this->dataClass, $row)) {
                    $results[] = $do;
                }
            }

            $this->listCache = new \ArrayList($results);
        }

        return $this->listCache;
    }

    /**
     * @param callable|QueryModifierInterface $queryModifier
     * @return \Heyday\QueryBuilder\QueryBuilder
     * @throws \Exception
     */
    public function addQueryModifier($queryModifier)
    {
        if ($queryModifier instanceof QueryModifierInterface || is_callable($queryModifier)) {
            $this->queryModifiers[] = $queryModifier;
            $this->invalidateCache();
        } else {
            throw new \Exception(
                'a QueryModifier must implement the Heyday\ListQueryOrganiser\Interfaces\QueryModifierInterface interface'
            );
        }

        return $this;
    }

    /**
     * @param array $queryModifiers
     * @return \Heyday\QueryBuilder\QueryBuilder
     */
    public function setQueryModifiers(array $queryModifiers)
    {
        foreach ($queryModifiers as $queryModifier) {
            $this->addQueryModifier($queryModifier);
        }
        
        return $this;
    }

    /**
     * @return array of query modifiers
     */
    public function getQueryModifiers()
    {
        return $this->queryModifiers;
    }

    /**
     * @param array $data
     * @return \Heyday\QueryBuilder\QueryBuilder
     */
    public function setData(array $data)
    {
        $this->data = \Convert::raw2sql($data);
        $this->invalidateCache();
        
        return $this;
    }
} 