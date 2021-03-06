<?php

/**
 * Php Sql
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace BronOS\PhpSql\Repository;


use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\QueryFactory;
use BronOS\PhpSql\Exception\CacheStorageException;
use BronOS\PhpSql\Exception\NotFoundException;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSql\Repository\Cache\CacheStorageInterface;
use PDO;

/**
 * Abstract cache repository.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
abstract class AbstractCacheRepository extends AbstractRepository implements CacheRepositoryInterface
{
    private CacheStorageInterface $cacheStorage;

    /**
     * AbstractRepository constructor.
     *
     * @param PDO                   $pdo
     * @param QueryFactory          $queryFactory
     * @param AbstractModel         $model
     * @param CacheStorageInterface $cacheStorage
     */
    public function __construct(
        PDO $pdo,
        QueryFactory $queryFactory,
        AbstractModel $model,
        CacheStorageInterface $cacheStorage
    ) {
        parent::__construct($pdo, $queryFactory, $model);
        $this->cacheStorage = $cacheStorage;
    }

    /**
     * Try to load result from the cache first if exists and executes query and fetch result otherwise.
     * Returns an array containing all of the result set rows.
     *
     * @param SelectInterface $select
     * @param bool            $force
     *
     * @return array
     *
     * @throws CacheStorageException
     * @throws PhpSqlException
     */
    public function fetchAllCache(SelectInterface $select, bool $force = false): array
    {
        $key = $this->generateCacheKey($select);

        if ($force == true || !$this->cacheStorage->isExists($key)) {
            $res = $this->fetchAll($select);
            $this->cacheStorage->save($key, $res);
            return $res;
        }

        return $this->cacheStorage->load($key);
    }

    /**
     * Try to load result from the cache first if exists and executes query
     * and fetch one row from a result set otherwise.
     *
     * @param SelectInterface $select
     * @param bool            $force
     *
     * @return array
     *
     * @throws CacheStorageException
     * @throws NotFoundException
     * @throws PhpSqlException
     */
    public function fetchOneCache(SelectInterface $select, bool $force = false): array
    {
        $key = $this->generateCacheKey($select);

        if ($force == true || !$this->cacheStorage->isExists($key)) {
            $res = $this->fetchOne($select);
            $this->cacheStorage->save($key, $res);
            return $res;
        }

        return $this->cacheStorage->load($key);
    }

    /**
     * Generate cache key based on select object.
     *
     * @param SelectInterface $select
     *
     * @return string
     */
    public function generateCacheKey(SelectInterface $select): string
    {
        return md5(implode('::', [
            $select->getStatement(),
            implode("::", $select->getBindValues())
        ]));
    }
}