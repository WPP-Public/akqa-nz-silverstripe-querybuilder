<?php
namespace Heyday\QueryBuilder;

use Heyday\QueryBuilder\Interfaces\QueryBuilderInterface;
use Heyday\QueryBuilder\Interfaces\QueryModifierInterface;

class QueryBuilder implements QueryBuilderInterface
{
    protected $queryModifiers = [];
    protected $data = [];
    protected $listCache = false;
    protected $queryCache = false;
    protected $dataClass;

    public function __construct($dataClass = null, array $queryModifiers = [], array $modifierData = [])
    {
        $this->dataClass = $dataClass;
        $this->setQueryModifiers($queryModifiers);
        $this->setData($modifierData);
    }

    protected function createDataObject($row)
    {
        $model = \DataModel::inst();

        if (empty($row['RecordClassName'])) {
            $row['RecordClassName'] = $row['ClassName'];
        }

        if (class_exists($row['RecordClassName'])) {
            $item = \Injector::inst()->create($row['RecordClassName'], $row, false, $model);
        } else if ($this->dataClass) {
            $item = Injector::inst()->create($this->dataClass, $row, false, $model);
        } else {
            return null;
        }

        return $item;
    }

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
        if (!$this->listCache === false) {
            $rows = $this->getQuery()->execute();
            $results = array();

            foreach ($rows as $row) {
                if ($do = $this->createDataObject($row)) {
                    $results[] = $do;
                }
            }
            $this->listCache = count($results) ? new \ArrayList($results) : null;
        }

        return $this->listCache;
    }


    /**
     * @param callable|QueryModifierInterface $queryModifier
     * @return mixed|void
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
    }

    /**
     * @param array $queryModifiers
     * @return mixed
     */
    public function setQueryModifiers(array $queryModifiers)
    {
        foreach ($queryModifiers as $queryModifier) {
            $this->addQueryModifier($queryModifier);
        }
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
     */
    public function setData(array $data)
    {
        $this->data = \Convert::raw2sql($data);
        $this->invalidateCache();
    }

} 