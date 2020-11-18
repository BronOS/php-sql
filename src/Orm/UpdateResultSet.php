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


use Aura\SqlQuery\Common\QuoterInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\Mysql\Update;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Exception\ResolvedException;
use BronOS\PhpSql\Exception\UpdateException;
use BronOS\PhpSql\Orm\Composition\AffectedRowsResultTrait;
use BronOS\PhpSql\Orm\Composition\ExecuteTrait;
use BronOS\PhpSql\Orm\Composition\ModelTrait;
use BronOS\PhpSql\Orm\Composition\PdoTrait;
use BronOS\PhpSql\Orm\Composition\ResolveTrait;
use BronOS\PhpSqlSchema\Exception\DuplicateColumnException;
use BronOS\PhpSqlSchema\Exception\DuplicateIndexException;
use BronOS\PhpSqlSchema\Exception\DuplicateRelationException;
use BronOS\PhpSqlSchema\Exception\SQLTableSchemaDeclarationException;
use PDO;

/**
 * Update result set.
 * Responsible for building query and execute it.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class UpdateResultSet extends Update
{
    use ResolveTrait;
    use ExecuteTrait;
    use AffectedRowsResultTrait;
    use ModelTrait {
        ModelTrait::__construct as modelConstruct;
    }
    use PdoTrait {
        PdoTrait::__construct as pdoConstruct;
    }

    /**
     * @param AbstractOrmModel $model
     * @param PDO              $pdo
     * @param QuoterInterface  $quoter
     * @param                  $builder
     *
     * @throws DuplicateColumnException
     * @throws DuplicateIndexException
     * @throws DuplicateRelationException
     * @throws SQLTableSchemaDeclarationException
     * @throws PhpSqlException
     */
    public function __construct(AbstractOrmModel $model, PDO $pdo, QuoterInterface $quoter, $builder)
    {
        parent::__construct($quoter, $builder);
        $this->modelConstruct($model);
        $this->pdoConstruct($pdo);

        $this->table($model->getSchema()->getName());
    }

    /**
     * Resolves result set (executes query).
     *
     * @return $this
     *
     * @throws UpdateException
     * @throws ResolvedException
     */
    public function exec(): self
    {
        if ($this->isResolved()) {
            throw new ResolvedException('Try to execute resolved result set.');
        }

        if (!$this->hasCols()) {
            throw new UpdateException('Nothing to update');
        }

        $this->rowCount = $this->execQuery($this);
        $this->isResolved = true;

        $this->model->undirty();

        return $this;
    }

    /**
     * Executes raw SQL update query and returns the number of affected rows.
     * Does NOT resolves result set.
     *
     * @param string $query
     * @param array  $binds
     *
     * @return int
     *
     * @throws UpdateException
     */
    public function execRaw(string $query, array $binds = []): int
    {
        try {
            return $this->executeRawQuery($query, $binds)->rowCount();
        } catch (PhpSqlException $e) {
            throw new UpdateException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Executes update query object and returns the number of affected rows.
     * Does NOT resolves result set.
     *
     * @param UpdateInterface $query
     *
     * @return int
     *
     * @throws UpdateException
     */
    public function execQuery(UpdateInterface $query): int
    {
        return $this->execRaw($query->getStatement(), $query->getBindValues());
    }

    /**
     * Unresolve the result set.
     * Sets resolved flag to false.
     * Clear number of affected rows.
     *
     * @return $this
     */
    public function unresolve(): self
    {
        $this->isResolved = false;
        $this->rowCount = null;
        return $this;
    }
}