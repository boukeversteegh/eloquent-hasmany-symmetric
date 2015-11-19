<?php

namespace EloquentRelations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HasManySymmetric extends Relation
{
    protected $foreignKeys = [];
    protected $localKey;

    public function __construct(Builder $query, Model $parent, $foreignKeys, $localKey)
    {
        $this->localKey = $localKey;
        $this->foreignKeys = (array)$foreignKeys;

        parent::__construct($query, $parent);
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    public function addConstraints()
    {
        $foreignKeys = $this->foreignKeys;
        $localKey = $this->localKey;
        $parentKey = $this->parent->getAttribute($this->localKey);

        if (static::$constraints) {
            $this->query->where(function($q) use ($foreignKeys, $parentKey) {
                $q->where($foreignKeys[0], $parentKey);
                $q->orWhere($foreignKeys[1], $parentKey);
            });
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $foreignKeys = $this->foreignKeys;
        $localKey = $this->localKey;

        $parent = $this->parent;

        $this->query->where(function($q) use ($models, $foreignKeys, $localKey, $parent) {
            $q->whereIn($foreignKeys[0], $this->getKeys($models, $localKey));
            $q->orWhereIn($foreignKeys[1], $this->getKeys($models, $localKey));
        });
    }

    public function getPlainForeignKeys()
    {   
        $foreignKeys = [];

        foreach($this->foreignKeys as $foreignKey) {
            $segments = explode('.', $foreignKey);
            $foreignKeys[] = $segments[count($segments) - 1];
        }
        return $foreignKeys;

    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models Models that should get their related models set
     * @param  \Illuminate\Database\Eloquent\Collection  $results Collection of related objects
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        // $dictionary stores per Model ids their related models from the result set
        $dictionary = [];
        foreach($results as $result) {
            // A result belongs to the model if any of its foreign keys matches the models local key
            foreach($this->getPlainForeignKeys() as $foreignKey) {
                $dictionary[$result->{$foreignKey}][] = $result;
            }
        }

        // Set the relation for all the models
        foreach($models as $model) {
            $relatedRecords = array_get($dictionary, $model->{$this->localKey}, []);
            $model->setRelation($relation, $this->related->newCollection($relatedRecords));
        }

        return $models;
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->get();
    }
}
