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

namespace BronOS\PhpSql\Database;


use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSqlSchema\Exception\DuplicateColumnException;
use BronOS\PhpSqlSchema\Exception\DuplicateIndexException;
use BronOS\PhpSqlSchema\Exception\DuplicateRelationException;
use BronOS\PhpSqlSchema\Exception\DuplicateTableException;
use BronOS\PhpSqlSchema\Exception\SQLTableSchemaDeclarationException;
use BronOS\PhpSqlSchema\SQLDatabaseSchema;
use BronOS\PhpSqlSchema\SQLTableSchemaInterface;

/**
 * A representation of database schema declaration.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
abstract class AbstractDatabase
{
    private static array $schemas = [];

    protected string $databaseName;
    protected ?string $engine = null;
    protected ?string $charset = null;
    protected ?string $collation = null;

    /**
     * Returns database schema declaration.
     *
     * @return SQLDatabaseSchema
     *
     * @throws DuplicateColumnException
     * @throws DuplicateIndexException
     * @throws DuplicateRelationException
     * @throws DuplicateTableException
     * @throws PhpSqlException
     * @throws SQLTableSchemaDeclarationException
     */
    public function getSchema(): SQLDatabaseSchema
    {
        if (!isset(self::$schemas[static::class])) {
            self::$schemas[static::class] = new SQLDatabaseSchema(
                $this->databaseName,
                $this->getTables(),
                $this->engine,
                $this->charset,
                $this->collation
            );
        }

        return self::$schemas[static::class];
    }

    /**
     * Returns list of models.
     *
     * @return AbstractModel[]
     *
     * @throws DuplicateColumnException
     * @throws DuplicateIndexException
     * @throws DuplicateRelationException
     * @throws DuplicateTableException
     * @throws PhpSqlException
     * @throws SQLTableSchemaDeclarationException
     */
    protected function getModels(): array
    {
        if (!isset(self::$schemas[static::class])) {
            return array_filter(get_object_vars($this), function ($prop) {
                return $prop instanceof AbstractModel;
            });
        }

        return $this->getSchema()->getTables();
    }

    /**
     * Returns list of sql table schemas.
     *
     * @return SQLTableSchemaInterface[]
     *
     * @throws DuplicateColumnException
     * @throws DuplicateIndexException
     * @throws DuplicateRelationException
     * @throws DuplicateTableException
     * @throws PhpSqlException
     * @throws SQLTableSchemaDeclarationException
     */
    protected function getTables(): array
    {
        return array_map(function (AbstractModel $model) {
            return $model->getSchema();
        }, $this->getModels());
    }
}