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
use Aura\SqlQuery\Mysql\Select;
use BronOS\PhpSql\Exception\NotFoundException;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Exception\ResolvedException;
use BronOS\PhpSql\Exception\UnresolvedException;
use BronOS\PhpSql\Orm\Composition\FetchTrait;
use BronOS\PhpSql\Orm\Composition\ModelTrait;
use BronOS\PhpSql\Orm\Composition\PdoTrait;
use BronOS\PhpSql\Orm\Composition\ResolveTrait;
use BronOS\PhpSqlSchema\Exception\DuplicateColumnException;
use BronOS\PhpSqlSchema\Exception\DuplicateIndexException;
use BronOS\PhpSqlSchema\Exception\DuplicateRelationException;
use BronOS\PhpSqlSchema\Exception\SQLTableSchemaDeclarationException;
use PDO;

/**
 * Select result set.
 * Responsible for building query and execute it.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class SelectResultSet extends Select
{
    use FetchTrait;
    use ResolveTrait;
    use ModelTrait {
        ModelTrait::__construct as modelConstruct;
    }
    use PdoTrait {
        PdoTrait::__construct as pdoConstruct;
    }

    /**
     * @var AbstractOrmModel[]|null
     */
    private ?array $result = null;

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

        $this->from($model->getSchema()->getName());
    }

    /**
     * Returns first result of resolved result set.
     *
     * @return AbstractOrmModel
     *
     * @throws NotFoundException
     * @throws UnresolvedException
     */
    public function resultFirst(): AbstractOrmModel
    {
        if (!$this->isResolved()) {
            throw new UnresolvedException('Try to get result on unresolved result set');
        }

        if (count($this->result) == 0) {
            throw new NotFoundException('Database record not found');
        }

        return $this->result[0];
    }

    /**
     * Returns all results of resolved result set.
     *
     * @return AbstractOrmModel[]
     *
     * @throws UnresolvedException
     */
    public function resultAll(): array
    {
        if (!$this->isResolved()) {
            throw new UnresolvedException('Try to get result on unresolved result set');
        }

        return $this->result;
    }

    /**
     * Resolves result set (executes query) and returns single model as a result.
     * Raises an exception in case when result set is already resolved,
     * If NotFoundException has been occurs in first call, same exception will be raised as well.
     *
     * @return AbstractOrmModel
     *
     * @throws PhpSqlException
     * @throws NotFoundException
     * @throws ResolvedException
     */
    public function first(): AbstractOrmModel
    {
        if ($this->isResolved()) {
            throw new ResolvedException('Try to execute resolved result set');
        }

        if (count($this->getCols()) == 0) {
            $this->cols($this->model->getColumnNames());
        }

        $this->result = [$this->model->newFromRow($this->fetchQuery($this))];
        $this->isResolved = true;

        return $this->result[0];
    }

    /**
     * Resolves result set (executes query) and returns all models as an array.
     * Raises an exception in case when result set is already resolved.
     *
     * @return AbstractOrmModel[]
     *
     * @throws PhpSqlException
     * @throws ResolvedException
     */
    public function all(): array
    {
        if ($this->isResolved()) {
            throw new ResolvedException('Try to execute resolved result set');
        }

        if (count($this->getCols()) == 0) {
            $this->cols($this->model->getColumnNames());
        }

        $this->result = $this->model->newFromRows($this->fetchAllQuery($this));
        $this->isResolved = true;

        return $this->result;
    }

    /**
     * Unresolve the result set.
     * Sets resolved flag to false.
     *
     * @return $this
     */
    public function unresolve(): self
    {
        $this->result = null;
        $this->isResolved = false;
        return $this;
    }
}