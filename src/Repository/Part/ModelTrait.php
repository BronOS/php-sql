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

namespace BronOS\PhpSql\Repository\Part;


use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSqlSchema\SQLTableSchema;

/**
 * Model trait
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
trait ModelTrait
{
    protected AbstractModel $model;

    /**
     * @return AbstractModel
     */
    public function getModel(): AbstractModel
    {
        return $this->model;
    }

    /**
     * Returns name of database table.
     *
     * @return string
     *
     * @throws PhpSqlException
     */
    public function getTableName(): string
    {
        return $this->getSchema()->getName();
    }

    /**
     * Returns all column names.
     *
     * @return string[]
     */
    public function getColumnNames(): array
    {
        return $this->getModel()->getColumnNames();
    }

    /**
     * Returns database table schema declaration object.
     *
     * @return SQLTableSchema
     *
     * @throws PhpSqlException
     */
    public function getSchema(): SQLTableSchema
    {
        try {
            return $this->model->getSchema();
        } catch (\Throwable $e) {
            throw new PhpSqlException($e->getMessage(), $e->getCode(), $e);
        }
    }
}