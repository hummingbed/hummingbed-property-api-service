<?php

namespace App\Repositories;

abstract class BaseRepository
{
    const PAGINATION = 10;

    abstract public function getModel();

    public function findById($id)
    {
        return $this->getModel()->find($id);
    }

    public function findByIdWith($id, array $with = [])
    {
        return $this->getModel()->with($with)->find($id);
    }

    public function findAll($conditions = [], $with = [])
    {
        return $this->getModel()->where($conditions)->with($with)->orderBy('id', 'DESC')->get();
    }

    public function insert($attributes)
    {
        return $this->getModel()->create($attributes);
    }
}
