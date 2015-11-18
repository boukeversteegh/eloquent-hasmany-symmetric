<?php

namespace Boukeversteegh\EloquentHasManySymmetric;

trait HasManySymmetricTrait
{
    public function hasManySymmetric($related, array $foreignKeys, $localKey = null)
    {
        $instance = new $related;

        $foreignKeys = array_map(function($foreignKey) use ($instance) {
            return $instance->getTable() . '.' . $foreignKey;
        }, $foreignKeys);

        $localKey = $localKey ?: $this->getKeyName();

        return new HasManySymmetric($instance->newQuery(), $this, $foreignKeys, $localKey);
    }
}