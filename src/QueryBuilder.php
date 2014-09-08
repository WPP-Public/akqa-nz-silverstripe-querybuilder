<?php

namespace Heyday\QueryBuilder;

use Heyday\QueryBuilder\Interfaces\QueryBuilderInterface;
use Heyday\QueryBuilder\Interfaces\QueryModifierInterface;

/**
 * @package Heyday\QueryBuilder
 */
class QueryBuilder implements QueryBuilderInterface
{
    /** @var \Heyday\QueryBuilder\Interfaces\QueryModifierInterface[] */
    protected $queryModifiers = [];

    /** @var array */
    protected $data = [];

    /** @var bool|\ArrayList */
    protected $listCache = false;

    /** @var bool|\SQLQuery */
    protected $queryCache = false;

    /** @var null */
    protected $dataClass;
    
    /** @var string */
    protected $stage;

    /**
     * @param string|void $dataClass
     * @param array $queryModifiers
     * @param array $modifierData
     */
    public function __construct(
        $dataClass = null,
        array $queryModifiers = [],
        array $modifierData = []
    )
    {
        $this->dataClass = $dataClass;
        $this->setQueryModifiers($queryModifiers);
        $this->setData($modifierData);
    }

    /**
     * @param string $stage
     * @return \Heyday\QueryBuilder\QueryBuilder
     */
    public function setStage($stage)
    {
        $this->stage = $stage;
        return $this;
    }

    /**
     * @return string
     */
    public function getStage()
    {
        return $this->stage;
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
                if ($this->stage) {
                    $dataQuery->setQueryParam('Versioned.mode', 'stage');
                    $dataQuery->setQueryParam('Versioned.stage', $this->stage);
                }
                $this->queryCache = $dataQuery->getFinalisedQuery();
            } else {
                $this->queryCache = new \SQLQuery();
            }

            if (is_array($this->queryModifiers)) {
                foreach ($this->queryModifiers as $queryModifier) {
                    if ($queryModifier instanceof QueryModifierInterface) {
                        $queryModifier->modify($this->queryCache, $this->data, $this);
                    } elseif (is_callable($queryModifier)) {
                        $queryModifier($this->queryCache, $this->data, $this);
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

    /** SS_List interface implementation */

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->getList()->getIterator();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->getList()->offsetExists($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->getList()->offsetGet($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->getList()->offsetSet($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->getList()->offsetUnset($offset);
    }

    /**
     * Returns all the items in the list in an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getList()->toArray();
    }

    /**
     * Returns the contents of the list as an array of maps.
     *
     * @return array
     */
    public function toNestedArray()
    {
        return $this->getList()->toNestedArray();
    }

    /**
     * Adds an item to the list, making no guarantees about where it will
     * appear.
     *
     * @param mixed $item
     */
    public function add($item)
    {
        $this->getList()->add($item);
    }

    /**
     * Removes an item from the list.
     *
     * @param mixed $item
     */
    public function remove($item)
    {
        $this->getList()->remove($item);
    }

    /**
     * Returns the first item in the list.
     *
     * @return mixed
     */
    public function first()
    {
        return $this->getList()->first();
    }

    /**
     * Returns the last item in the list.
     *
     * @return mixed
     */
    public function last()
    {
        return $this->getList()->last();
    }

    /**
     * Returns a map of a key field to a value field of all the items in the
     * list.
     *
     * @param  string $keyfield
     * @param  string $titlefield
     * @return array
     */
    public function map($keyfield = 'ID', $titlefield = 'Title')
    {
        return $this->getList()->map($keyfield, $titlefield);
    }

    /**
     * Returns the first item in the list where the key field is equal to the
     * value.
     *
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     */
    public function find($key, $value)
    {
        return $this->getList()->find($key, $value);
    }

    /**
     * Returns an array of a single field value for all items in the list.
     *
     * @param  string $colName
     * @return array
     */
    public function column($colName = "ID")
    {
        return $this->getList()->column($colName);
    }

    /**
     * Walks the list using the specified callback
     *
     * @param callable $callback
     * @return mixed
     */
    public function each($callback)
    {
        return $this->getList()->each($callback);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->getList()->count();
    }
}