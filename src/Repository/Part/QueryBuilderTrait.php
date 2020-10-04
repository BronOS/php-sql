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


use Aura\SqlQuery\Common\DeleteInterface;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\QueryFactory;
use BronOS\PhpSql\Exception\PhpSqlException;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Query builder trait
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
trait QueryBuilderTrait
{
    use ModelTrait;

    protected QueryFactory $queryFactory;

    /**
     * @return QueryFactory
     */
    public function getQueryFactory(): QueryFactory
    {
        return $this->queryFactory;
    }

    /**
     * Returns new select query object filled with table name.
     *
     * @return SelectInterface
     *
     * @throws PhpSqlException
     */
    public function newSelect(): SelectInterface
    {
        return $this->getQueryFactory()
            ->newSelect()
            ->cols($this->getColumnNames())
            ->from($this->getTableName());
    }

    /**
     * Returns new insert query object with table name.
     *
     * @return InsertInterface
     *
     * @throws PhpSqlException
     */
    public function newInsert(): InsertInterface
    {
        return $this->getQueryFactory()->newInsert()->into($this->getTableName());
    }

    /**
     * Returns new update query object with table name.
     *
     * @return UpdateInterface
     *
     * @throws PhpSqlException
     */
    public function newUpdate(): UpdateInterface
    {
        return $this->getQueryFactory()->newUpdate()->table($this->getTableName());
    }

    /**
     * Returns new delete query object with table name.
     *
     * @return DeleteInterface
     *
     * @throws PhpSqlException
     */
    public function newDelete(): DeleteInterface
    {
        return $this->getQueryFactory()->newDelete()->from($this->getTableName());
    }
}