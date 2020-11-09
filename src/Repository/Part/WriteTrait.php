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
use Aura\SqlQuery\Common\UpdateInterface;
use BronOS\PhpSql\Exception\DeleteException;
use BronOS\PhpSql\Exception\InsertException;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Exception\UpdateException;

/**
 * Write trait
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
trait WriteTrait
{
    use ExecuteTrait;
    use QueryBuilderTrait;

    /**
     * Executes insert query and returns last inserted id.
     *
     * @param string $query
     * @param array  $binds
     *
     * @return string
     *
     * @throws InsertException
     */
    public function executeInsertRaw(string $query, array $binds = []): string
    {
        try {
            $this->execute($query, $binds);
            return $this->getPdo()->lastInsertId();
        } catch (PhpSqlException $e) {
            throw new InsertException('Database insert error: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Executes update query and returns number of updated rows.
     *
     * @param string $query
     * @param array  $binds
     *
     * @return int
     *
     * @throws UpdateException
     */
    public function executeUpdateRaw(string $query, array $binds = []): int
    {
        try {
            return $this->execute($query, $binds)->rowCount();
        } catch (PhpSqlException $e) {
            throw new UpdateException('Database update error: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Executes delete query and returns number of affected rows.
     *
     * @param string $query
     * @param array  $binds
     *
     * @return int
     *
     * @throws DeleteException
     */
    public function executeDeleteRaw(string $query, array $binds = []): int
    {
        try {
            return $this->execute($query, $binds)->rowCount();
        } catch (PhpSqlException $e) {
            throw new DeleteException('Database delete error: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Executes insert query and returns last inserted id.
     *
     * @param InsertInterface $insert
     *
     * @return string
     *
     * @throws InsertException
     */
    public function executeInsert(InsertInterface $insert): string
    {
        return $this->executeInsertRaw($insert->getStatement(), $insert->getBindValues());
    }

    /**
     * Executes update query and returns number of updated rows.
     *
     * @param UpdateInterface $update
     *
     * @return int
     *
     * @throws UpdateException
     */
    public function executeUpdate(UpdateInterface $update): int
    {
        return $this->executeUpdateRaw($update->getStatement(), $update->getBindValues());
    }

    /**
     * Executes delete query and returns number of affected rows.
     *
     * @param DeleteInterface $delete
     *
     * @return int
     *
     * @throws DeleteException
     */
    public function executeDelete(DeleteInterface $delete): int
    {
        return $this->executeDeleteRaw($delete->getStatement(), $delete->getBindValues());
    }
}