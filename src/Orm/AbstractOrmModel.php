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

namespace BronOS\PhpSql\Orm;


use Aura\SqlQuery\Common\WhereInterface;
use BronOS\PhpSql\Exception\DeleteException;
use BronOS\PhpSql\Exception\FieldNotExistsException;
use BronOS\PhpSql\Exception\InsertException;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Exception\ResolvedException;
use BronOS\PhpSql\Exception\UpdateException;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSql\QueryBuilder\Criteria;
use BronOS\PhpSqlSchema\Exception\DuplicateColumnException;
use BronOS\PhpSqlSchema\Exception\DuplicateIndexException;
use BronOS\PhpSqlSchema\Exception\DuplicateRelationException;
use BronOS\PhpSqlSchema\Exception\PhpSqlSchemaException;
use BronOS\PhpSqlSchema\Exception\SQLTableSchemaDeclarationException;

/**
 * A representation of database record and table schema declaration.
 * Provides methods to execute CRUD operations.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
abstract class AbstractOrmModel extends AbstractModel
{
    static public ResultSetFactoryInterface $resultSetFactory;

    public bool $isDeleted = false;

    /**
     * Returns new unresolved result set object preset with with table name ("from")
     * and passed criterias as WHERE statement.
     * If columns hasn't been set, all columns will be used in the SELECT query.
     *
     * @param Criteria ...$criterias
     *
     * @return SelectResultSet
     *
     * @throws PhpSqlException
     */
    public function find(Criteria ...$criterias): SelectResultSet
    {
        try {
            return $this->bindWhere($this::$resultSetFactory->newSelect($this), ...$criterias);
        } catch (PhpSqlSchemaException $e) {
            throw new PhpSqlException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param WhereInterface $query
     * @param Criteria       ...$criterias
     *
     * @return WhereInterface|SelectResultSet|InsertResultSet|UpdateResultSet|DeleteResultSet
     */
    private function bindWhere(WhereInterface $query, Criteria ...$criterias): WhereInterface
    {
        foreach ($criterias as $criteria) {
            if ($criteria->isAnd()) {
                $query->where($criteria->getCond(), $criteria->getBinds());
            } else {
                $query->orWhere($criteria->getCond(), $criteria->getBinds());
            }
        }

        return $query;
    }

    /**
     * Deletes records with passed criteria objects as a WHERE statement
     * and returns resolved result set object.
     *
     * @param Criteria ...$criterias
     *
     * @return DeleteResultSet
     *
     * @throws DeleteException
     */
    public function delete(Criteria ...$criterias): DeleteResultSet
    {
        try {
            return $this->newDelete(...$criterias)->exec();
        } catch (DeleteException $e) {
            throw $e;
        } catch (PhpSqlException $e) {
            throw new DeleteException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Deletes records with primary key field as a WHERE statement
     * and returns resolved result set object.
     *
     * @return DeleteResultSet
     *
     * @throws DeleteException
     * @throws FieldNotExistsException
     */
    public function deleteByPk(): DeleteResultSet
    {
        return $this->delete($this->getPk()->eq());
    }

    /**
     * Returns new unresolved result set object with preset table name
     * and criteria objects as a WHERE statement.
     *
     * @param Criteria ...$criterias
     *
     * @return DeleteResultSet
     *
     * @throws PhpSqlException
     */
    public function newDelete(Criteria ...$criterias): DeleteResultSet
    {
        try {
            return $this->bindWhere($this::$resultSetFactory->newDelete($this), ...$criterias);
        } catch (PhpSqlSchemaException $e) {
            throw new PhpSqlException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Inserts all dirty fields and returns resolved result set object.
     *
     * @return InsertResultSet
     *
     * @throws InsertException
     */
    public function insert(): InsertResultSet
    {
        try {
            return $this->newInsert(true)->exec();
        } catch (InsertException $e) {
            throw $e;
        } catch (PhpSqlException $e) {
            throw new InsertException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Returns new unresolved result set object with preset table name ("into").
     * Sets all dirty fields in case when $withDirtyFields is passed.
     *
     * @param bool $withDirtyFields
     *
     * @return InsertResultSet
     *
     * @throws PhpSqlException
     */
    public function newInsert(bool $withDirtyFields = true): InsertResultSet
    {
        try {
            $query = $this::$resultSetFactory->newInsert($this);
        } catch (PhpSqlSchemaException $e) {
            throw new PhpSqlException($e->getMessage(), $e->getCode(), $e);
        }

        if ($withDirtyFields) {
            $query->cols($this->dirtyFieldToQuery());
        }

        return $query;
    }

    /**
     * Returns new unresolved result set object with preset table name.
     * Sets all dirty fields in case when $withDirtyFields is passed.
     * Sets criteria objects as a WHERE statement.
     *
     * @param bool     $withDirtyFields
     * @param Criteria ...$criterias
     *
     * @return UpdateResultSet
     *
     * @throws PhpSqlException
     */
    public function newUpdate(bool $withDirtyFields = true, Criteria ...$criterias): UpdateResultSet
    {
        try {
            $query = $this::$resultSetFactory->newUpdate($this);
        } catch (PhpSqlSchemaException $e) {
            throw new PhpSqlException($e->getMessage(), $e->getCode(), $e);
        }

        if ($withDirtyFields) {
            $query->cols($this->dirtyFieldToQuery());
        }

        return $this->bindWhere($query, ...$criterias);
    }

    /**
     * Updates all dirty fields with criteria objects as a WHERE statement
     * and returns resolved result set object.
     *
     * @param Criteria ...$criterias
     *
     * @return UpdateResultSet
     *
     * @throws UpdateException
     */
    public function update(Criteria ...$criterias): UpdateResultSet
    {
        try {
            return $this->newUpdate(true, ...$criterias)->exec();
        } catch (UpdateException $e) {
            throw $e;
        } catch (PhpSqlException $e) {
            throw new UpdateException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Updates all dirty fields with primary key as a WHERE statement
     * and returns resolved result set object.
     *
     * @return UpdateResultSet
     *
     * @throws UpdateException
     * @throws FieldNotExistsException
     */
    public function updateByPk(): UpdateResultSet
    {
        return $this->update($this->getPk()->eq());
    }
}