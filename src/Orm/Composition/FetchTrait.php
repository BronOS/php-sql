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

namespace BronOS\PhpSql\Orm\Composition;


use Aura\SqlQuery\Common\SelectInterface;
use BronOS\PhpSql\Exception\NotFoundException;
use BronOS\PhpSql\Exception\PhpSqlException;
use PDO;

/**
 * Provides "execute" functionality for ORM result sets.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
trait FetchTrait
{
    use ExecuteTrait;

    /**
     * Executes raw SQL query, fetches first result and returns an array indexed by column.
     * Does NOT resolve result set.
     *
     * @param string $query
     * @param array  $binds
     *
     * @return array
     *
     * @throws NotFoundException
     * @throws PhpSqlException
     */
    public function fetchRaw(string $query, array $binds = []): array
    {
        $res = $this->executeRawQuery($query, $binds)->fetch(PDO::FETCH_ASSOC);

        if ($res === false) {
            throw new NotFoundException('Database record not found');
        }

        return $res;
    }

    /**
     * Executes raw SQL query, fetches all results and returns an array of array indexed by column.
     * Does NOT resolve result set.
     *
     * @param string $query
     * @param array  $binds
     *
     * @return array
     *
     * @throws PhpSqlException
     */
    public function fetchAllRaw(string $query, array $binds = []): array
    {
        return $this->executeRawQuery($query, $binds)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Executes query object, fetches first result and returns an array indexed by column.
     * Does NOT resolve result set.
     *
     * @param SelectInterface $query
     *
     * @return array
     *
     * @throws NotFoundException
     * @throws PhpSqlException
     */
    public function fetchQuery(SelectInterface $query): array
    {
        return $this->fetchRaw($query->getStatement(), $query->getBindValues());
    }

    /**
     * Executes query object, fetches all results and returns an array of array indexed by column.
     * Does NOT resolve result set.
     *
     * @param SelectInterface $query
     *
     * @return array
     *
     * @throws PhpSqlException
     */
    public function fetchAllQuery(SelectInterface $query): array
    {
        return $this->fetchAllRaw($query->getStatement(), $query->getBindValues());
    }
}