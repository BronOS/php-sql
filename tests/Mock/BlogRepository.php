<?php

namespace BronOS\PhpSql\Tests\Mock;


use BronOS\PhpSql\Exception\CacheStorageException;
use BronOS\PhpSql\Exception\DeleteException;
use BronOS\PhpSql\Exception\InsertException;
use BronOS\PhpSql\Exception\NotFoundException;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Exception\UpdateException;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSql\Repository\AbstractCacheRepository;

class BlogRepository extends AbstractCacheRepository
{
    /**
     * @return BlogModel
     */
    public function getModel(): BlogModel
    {
        return parent::getModel();
    }

    /**
     * @param int  $id
     * @param bool $force
     *
     * @return BlogModel
     *
     * @throws CacheStorageException
     * @throws NotFoundException
     * @throws PhpSqlException
     */
    public function findById(int $id, bool $force = false): BlogModel
    {
        $select = $this->newSelect();
        $this->getModel()->getId()->bindWhere($select, $id);
        return $this->getModel()->newFromRow($this->fetchOneCache($select, $force));
    }

    /**
     * @param BlogModel $model
     *
     * @return int
     *
     * @throws PhpSqlException
     * @throws InsertException
     */
    public function create(BlogModel $model): int
    {
        $id = (int)$this->executeInsert($this->newInsert($model));

        $model->getId()->setValue($id);
        $model->undirty();

        return $id;
    }

    /**
     * @param BlogModel $model
     *
     * @return bool
     *
     * @throws PhpSqlException
     * @throws UpdateException
     */
    public function update(BlogModel $model): bool
    {
        $this->executeUpdate($this->newUpdate($model, $model->getId()));
        $model->undirty();
        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     *
     * @throws PhpSqlException
     * @throws DeleteException
     */
    public function delete(int $id): bool
    {
        $delete = $this->newDelete();
        $this->getModel()->getId()->bindWhere($delete, $id);
        $this->executeDelete($delete);
        return true;
    }
}