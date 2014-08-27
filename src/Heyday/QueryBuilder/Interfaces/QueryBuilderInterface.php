<?php

namespace Heyday\QueryBuilder\Interfaces;

/**
 * @package Heyday\QueryBuilder\Interfaces
 */
interface QueryBuilderInterface
{
    /**
     * @return \SQLQuery
     */
    public function getQuery();

    /**
     * @return \ArrayList
     */
    public function getList();

    /**
     * @param callable|QueryModifierInterface $queryModifier
     * @return mixed
     */
    public function addQueryModifier($queryModifier);

    /**
     * @param array $queryModifiers
     * @return mixed
     */
    public function setQueryModifiers(array $queryModifiers);

    /**
     * @return array of query modifiers
     */
    public function getQueryModifiers();

    /**
     * @param array $data
     */
    public function setData(array $data);
}