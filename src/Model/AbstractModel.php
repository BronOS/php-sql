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

namespace BronOS\PhpSql\Model;


use BronOS\PhpSql\Exception\FieldNotExistsException;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Field\AbstractField;
use BronOS\PhpSqlSchema\Column\Attribute\AutoincrementColumnAttributeInterface;
use BronOS\PhpSqlSchema\Column\ColumnInterface;
use BronOS\PhpSqlSchema\Exception\DuplicateColumnException;
use BronOS\PhpSqlSchema\Exception\DuplicateIndexException;
use BronOS\PhpSqlSchema\Exception\DuplicateRelationException;
use BronOS\PhpSqlSchema\Exception\PhpSqlSchemaException;
use BronOS\PhpSqlSchema\Exception\SQLTableSchemaDeclarationException;
use BronOS\PhpSqlSchema\Index\IndexInterface;
use BronOS\PhpSqlSchema\Index\PrimaryKeyInterface;
use BronOS\PhpSqlSchema\Relation\ForeignKeyInterface;
use BronOS\PhpSqlSchema\SQLTableSchema;
use ReflectionClass;
use ReflectionException;

/**
 * A representation of database record and table schema declaration.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
abstract class AbstractModel
{
    private static array $schemas = [];

    protected ?string $tableName = null;
    protected ?string $engine = null;
    protected ?string $charset = null;
    protected ?string $collation = null;

    public bool $isDirty = false;
    public bool $isNew = true;

    private static array $columns = [];
    private static array $columnNames = [];

    /**
     * Returns database table schema declaration.
     *
     * @return SQLTableSchema
     *
     * @throws DuplicateColumnException
     * @throws DuplicateIndexException
     * @throws DuplicateRelationException
     * @throws SQLTableSchemaDeclarationException
     * @throws PhpSqlException
     */
    public function getSchema(): SQLTableSchema
    {
        if (!isset(self::$schemas[static::class])) {
            self::$schemas[static::class] = new SQLTableSchema(
                $this->getTableName(),
                $this->getColumns(),
                static::getIndexes(),
                static::getRelations(),
                $this->engine,
                $this->charset,
                $this->collation
            );
        }

        return self::$schemas[static::class];
    }

    /**
     * @return string
     *
     * @throws PhpSqlException
     */
    private function getTableName(): string
    {
        if (is_null($this->tableName)) {
            try {
                $className = (new ReflectionClass($this))->getShortName();
            } catch (ReflectionException $e) {
                throw new PhpSqlException('Cannot extract table name', $e->getCode(), $e);
            }

            $this->tableName = $this->toUnderscore(str_ireplace('model', '', $className));
        }

        return $this->tableName;
    }

    /**
     * @param string $input
     *
     * @return string
     *
     * @throws PhpSqlException
     */
    private function toUnderscore(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);

        if (count($matches) == 0) {
            throw new PhpSqlException('Cannot convert string to "underscore"');
        }

        return implode('_', array_map('strtolower', $matches[0]));
    }

    /**
     * Returns list of model's columns.
     *
     * @return ColumnInterface[]
     */
    public function getColumns(): array
    {
        if (!isset(self::$columns[static::class])) {
            self::$columns[static::class] = array_map(function (AbstractField $field) {
                return $field->getColumn();
            }, $this->getFields());
        }

        return self::$columns[static::class];
    }

    /**
     * Returns list of all column's names.
     *
     * @return string[]
     */
    public function getColumnNames(): array
    {
        if (!isset(self::$columnNames[static::class])) {
            self::$columnNames[static::class] = array_map(function (ColumnInterface $column) {
                return $column->getName();
            }, array_values($this->getColumns()));
        }

        return self::$columnNames[static::class];
    }

    /**
     * Returns list of model's fields.
     *
     * @return AbstractField[]
     */
    public function getFields(): array
    {
        return array_filter(get_object_vars($this), function ($prop) {
            return $prop instanceof AbstractField;
        });
    }

    /**
     * Returns field by column name.
     *
     * @param string $columnName
     *
     * @return AbstractField
     *
     * @throws FieldNotExistsException
     */
    public function getField(string $columnName): AbstractField
    {
        foreach ($this->getFields() as $field) {
            if ($field->getColumn()->getName() == $columnName) {
                return $field;
            }
        }

        throw new FieldNotExistsException(sprintf('Field %s does not exists in mode %s', $columnName, static::class));
    }

    /**
     * Tries to find and returns primary key field.
     * Throws FieldNotExists exception if primary key field not found.
     *
     * @return AbstractField
     *
     * @throws FieldNotExistsException
     */
    public function getPk(): AbstractField
    {
        // try to find autoincrement field first
        foreach ($this->getFields() as $field) {
            $col = $field->getColumn();
            if ($col instanceof AutoincrementColumnAttributeInterface && $col->isAutoincrement()) {
                return $field;
            }
        }

        try {
            // try to find by primary key index
            foreach ($this->getSchema()->getIndexes() as $index) {
                if ($index instanceof PrimaryKeyInterface && count($index->getFields()) == 1) {
                    return $this->getField($index->getFields()[0]);
                }
            }
        } catch (PhpSqlSchemaException | PhpSqlException $e) {
            throw new FieldNotExistsException($e->getMessage(), $e->getCode(), $e);
        }

        throw new FieldNotExistsException(sprintf("Can not find primary key on model %s", static::class));
    }

    /**
     * Returns list of fields marked as dirty if any.
     *
     * @return AbstractField[]
     */
    public function getDirtyFields(): array
    {
        return array_filter($this->getFields(), function (AbstractField $field) {
            return $field->isDirty;
        });
    }

    /**
     * Returns a key ~> value map of dirty fields
     * where key is a column name and value is an arguments to be bound to placeholders
     *
     * @return array
     */
    public function dirtyFieldToQuery(): array
    {
       $map = [];

        foreach ($this->getDirtyFields() as $dirtyField) {
            $map = array_merge($map, $dirtyField->toQuery());
       }

       return $map;
    }

    /**
     * Returns database table indexes declaration.
     *
     * @return IndexInterface[]
     */
    public static function getIndexes(): array
    {
        return [];
    }

    /**
     * Returns database table foreign keys declaration.
     *
     * @return ForeignKeyInterface[]
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Instantiates single model from database single row (record).
     *
     * @param array $row
     *
     * @return $this
     */
    public function newFromRow(array $row): self
    {
        $model = new $this($row);
        $model->isNew = false;
        return $model;
    }

    /**
     * Instantiates multiple models from database multiple rows (records).
     *
     * @param array $rows
     *
     * @return $this[]
     */
    public function newFromRows(array $rows): array
    {
        return array_map([$this, 'newFromRow'], $rows);
    }

    /**
     * Marks itself and all internal fields as undirty.
     */
    public function undirty(): void
    {
        $this->isDirty = false;

        foreach ($this->getFields() as $field) {
            $field->isDirty = false;
        }
    }
}