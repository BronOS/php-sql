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


use Aura\SqlQuery\Common\AbstractBuilder;
use Aura\SqlQuery\Common\QuoterInterface;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSqlSchema\Exception\DuplicateColumnException;
use BronOS\PhpSqlSchema\Exception\DuplicateIndexException;
use BronOS\PhpSqlSchema\Exception\DuplicateRelationException;
use BronOS\PhpSqlSchema\Exception\SQLTableSchemaDeclarationException;
use PDO;

/**
 * Result set factory.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class ResultSetFactory implements ResultSetFactoryInterface
{
    private QuoterInterface $quoter;
    private AbstractBuilder $selectBuilder;
    private AbstractBuilder $insertBuilder;
    private AbstractBuilder $updateBuilder;
    private AbstractBuilder $deleteBuilder;
    private PDO $pdo;

    /**
     * ResultSetFactory constructor.
     *
     * @param PDO             $pdo
     * @param QuoterInterface $quoter
     * @param AbstractBuilder $selectBuilder
     * @param AbstractBuilder $insertBuilder
     * @param AbstractBuilder $updateBuilder
     * @param AbstractBuilder $deleteBuilder
     */
    public function __construct(
        PDO $pdo,
        QuoterInterface $quoter,
        AbstractBuilder $selectBuilder,
        AbstractBuilder $insertBuilder,
        AbstractBuilder $updateBuilder,
        AbstractBuilder $deleteBuilder
    ) {
        $this->pdo = $pdo;
        $this->quoter = $quoter;
        $this->selectBuilder = $selectBuilder;
        $this->insertBuilder = $insertBuilder;
        $this->updateBuilder = $updateBuilder;
        $this->deleteBuilder = $deleteBuilder;
    }

    /**
     * Returns new instance of select result set object.
     *
     * @param AbstractOrmModel $model
     *
     * @return SelectResultSet
     *
     * @throws DuplicateColumnException
     * @throws DuplicateIndexException
     * @throws DuplicateRelationException
     * @throws SQLTableSchemaDeclarationException
     * @throws PhpSqlException
     */
    public function newSelect(AbstractOrmModel $model): SelectResultSet
    {
        return new SelectResultSet($model, $this->pdo, $this->quoter, $this->selectBuilder);
    }

    /**
     * Returns new instance of insert result set object.
     *
     * @param AbstractOrmModel $model
     *
     * @return InsertResultSet
     *
     * @throws DuplicateColumnException
     * @throws DuplicateIndexException
     * @throws DuplicateRelationException
     * @throws SQLTableSchemaDeclarationException
     * @throws PhpSqlException
     */
    public function newInsert(AbstractOrmModel $model): InsertResultSet
    {
        return new InsertResultSet($model, $this->pdo, $this->quoter, $this->insertBuilder);
    }

    /**
     * Returns new update of insert result set object.
     *
     * @param AbstractOrmModel $model
     *
     * @return UpdateResultSet
     *
     * @throws DuplicateColumnException
     * @throws DuplicateIndexException
     * @throws DuplicateRelationException
     * @throws SQLTableSchemaDeclarationException
     * @throws PhpSqlException
     */
    public function newUpdate(AbstractOrmModel $model): UpdateResultSet
    {
        return new UpdateResultSet($model, $this->pdo, $this->quoter, $this->updateBuilder);
    }

    /**
     * Returns new delete of insert result set object.
     *
     * @param AbstractOrmModel $model
     *
     * @return DeleteResultSet
     *
     * @throws DuplicateColumnException
     * @throws DuplicateIndexException
     * @throws DuplicateRelationException
     * @throws SQLTableSchemaDeclarationException
     * @throws PhpSqlException
     */
    public function newDelete(AbstractOrmModel $model): DeleteResultSet
    {
        return new DeleteResultSet($model, $this->pdo, $this->quoter, $this->deleteBuilder);
    }
}