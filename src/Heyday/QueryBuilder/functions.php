<?php

namespace Heyday\QueryBuilder;

/**
 * @param string $dataClass
 * @param array $row
 * @return null|\DataObject
 */
function createDataObject($dataClass, $row) {
    $model = \DataModel::inst();

    if (empty($row['RecordClassName'])) {
        $row['RecordClassName'] = $row['ClassName'];
    }

    if (class_exists($row['RecordClassName'])) {
        $item = \Injector::inst()->create($row['RecordClassName'], $row, false, $model);
    } elseif ($this->dataClass) {
        $item = \Injector::inst()->create($dataClass, $row, false, $model);
    } else {
        return null;
    }

    return $item;
}