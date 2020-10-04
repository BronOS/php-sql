<?php

namespace BronOS\PhpSql\Tests\Mock;


use Aura\SqlQuery\QueryFactory;
use BronOS\PhpSql\Exception\CacheStorageException;
use BronOS\PhpSql\Exception\NotFoundException;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Field\BigIntField;
use BronOS\PhpSql\Field\IntField;
use BronOS\PhpSql\Field\VarCharField;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSql\Repository\AbstractCacheRepository;
use BronOS\PhpSql\Repository\Cache\CacheStorageInterface;
use BronOS\PhpSqlSchema\Exception\ColumnDeclarationException;
use PDO;

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