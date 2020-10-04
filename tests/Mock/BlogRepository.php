<?php

namespace BronOS\PhpSql\Tests\Mock;


use BronOS\PhpSql\Exception\CacheStorageException;
use BronOS\PhpSql\Exception\NotFoundException;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Repository\AbstractCacheRepository;

class BlogRepository extends AbstractCacheRepository
{
    /**
     * @param int $id
     *
     * @return BlogModel
     *
     * @throws CacheStorageException
     * @throws NotFoundException
     * @throws PhpSqlException
     */
    public function findById(int $id): BlogModel
    {
        return $this->getModel()->newFromRow($this->fetchOneCache(
            $this->newSelect()
                ->where('id = :id')
                ->bindValue('id', $id)
        ));
    }
}