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


use Aura\SqlQuery\AbstractQuery;
use BronOS\PhpSql\Exception\PhpSqlException;
use PDOException;
use PDOStatement;

/**
 * Provides "execute" functionality for ORM result sets.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
trait ExecuteTrait
{
    use PdoTrait;

    /**
     * Executes raw SQL query and returns pdo statement object.
     *
     * @param string $query
     * @param array $binds
     *
     * @return PDOStatement
     *
     * @throws PhpSqlException
     */
    public function executeRawQuery(string $query, array $binds = []): PDOStatement
    {
        try {
            $sth = $this->getPdo()->prepare($query);
            if ($sth === false) {
                throw new PDOException("Cannot prepare sql statement");
            }

            $sth->execute($binds);
        } catch (PDOException $e) {
            throw new PhpSqlException(sprintf(
                'DB query execution error: %s: %s',
                $e->getCode(),
                $e->getMessage()
            ), (int)$e->getCode(), $e);
        }

        return $sth;
    }

    /**
     * Executes query object and returns pdo statement object.
     *
     * @param AbstractQuery $query
     *
     * @return PDOStatement
     * @throws PhpSqlException
     */
    public function executeQuery(AbstractQuery $query): PDOStatement
    {
        return $this->executeRawQuery($query->getStatement(), $query->getBindValues());
    }
}