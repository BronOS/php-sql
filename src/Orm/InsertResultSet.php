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


use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\QuoterInterface;
use Aura\SqlQuery\Mysql\Insert;
use BronOS\PhpSql\Exception\FieldNotExistsException;
use BronOS\PhpSql\Exception\InsertException;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Exception\ResolvedException;
use BronOS\PhpSql\Exception\UnresolvedException;
use BronOS\PhpSql\Field\AbstractBoolField;
use BronOS\PhpSql\Field\AbstractFloatField;
use BronOS\PhpSql\Field\AbstractIntField;
use BronOS\PhpSql\Field\AbstractStringField;
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
 * Insert result set.
 * Responsible for building query and execute it.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class InsertResultSet extends Insert
{
    use ResolveTrait;
    use ExecuteTrait;
    use ModelTrait {
        ModelTrait::__construct as modelConstruct;
    }
    use PdoTrait {
        PdoTrait::__construct as pdoConstruct;
    }

    private ?string $lastInsertedId = null;

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

        $this->into($model->getSchema()->getName());
    }

    /**
     * Returns last inserted id.
     * Raises UnresolvedException if result set is not resolved.
     *
     * @return string
     *
     * @throws UnresolvedException
     */
    public function lastInsertedId(): string
    {
        if (!$this->isResolved()) {
            throw new UnresolvedException('Try to get last inserted id on unresolved result set');
        }

        return $this->lastInsertedId;
    }

    /**
     * Executes raw SQL insert query and returns last inserted id.
     * Does NOT resolves result set.
     *
     * @param string $query
     * @param array  $binds
     *
     * @return string
     *
     * @throws InsertException
     */
    public function execRaw(string $query, array $binds = []): string
    {
        try {
            $this->executeRawQuery($query, $binds);
            return $this->getPdo()->lastInsertId();
        } catch (PhpSqlException $e) {
            throw new InsertException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Executes insert query object and returns last inserted id.
     * Does NOT resolves result set.
     *
     * @param InsertInterface $query
     *
     * @return $this
     *
     * @throws InsertException
     */
    public function execQuery(InsertInterface $query): string
    {
        return $this->execRaw($query->getStatement(), $query->getBindValues());
    }

    /**
     * Resolves result set (executes query).
     *
     * @return $this
     *
     * @throws InsertException
     * @throws ResolvedException
     */
    public function exec(): self
    {
        if ($this->isResolved()) {
            throw new ResolvedException('Try to execute resolved result set.');
        }

        $this->lastInsertedId = $this->execQuery($this);
        $this->isResolved = true;

        try {
            // try to set last inserted id
            $pk = $this->model->getPk();

            if ($pk instanceof AbstractIntField) {
                $pk->setValue((int)$this->lastInsertedId);
            } elseif ($pk instanceof AbstractBoolField) {
                $pk->setValue((bool)$this->lastInsertedId);
            } elseif ($pk instanceof AbstractFloatField) {
                $pk->setValue((float)$this->lastInsertedId);
            } elseif ($pk instanceof AbstractStringField) {
                $pk->setValue($this->lastInsertedId);
            }
        } catch (FieldNotExistsException $e) {
            // primary key not found, so, just skip
        }

        $this->model->undirty();

        return $this;
    }

    /**
     * Unresolve the result set.
     * Sets resolved flag to false.
     * Clear last inserted id.
     *
     * @return $this
     */
    public function unresolve(): self
    {
        $this->isResolved = false;
        $this->lastInsertedId = null;
        return $this;
    }
}