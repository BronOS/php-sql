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


use BronOS\PhpSql\Exception\TransactionException;
use PDO;

/**
 * Transaction interface.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
interface TransactionInterface
{
    /**
     * Initiates a transaction.
     *
     * Turns off autocommit mode. While autocommit mode is turned off,
     * changes made to the database via the PDO object instance are not committed
     * until you end the transaction by calling {@link PDO::commit()}.
     * Calling {@link PDO::rollBack()} will roll back all changes to the database and
     * return the connection to autocommit mode.
     *
     * Some databases, including MySQL, automatically issue an implicit COMMIT
     * when a database definition language (DDL) statement
     * such as DROP TABLE or CREATE TABLE is issued within a transaction.
     * The implicit COMMIT will prevent you from rolling back any other changes
     * within the transaction boundary.
     *
     * Returns bool TRUE on success or FALSE on failure.
     *
     * @link https://php.net/manual/en/pdo.begintransaction.php
     *
     * @return bool
     *
     * @throws TransactionException If there is already a transaction started or
     * the driver does not support transactions
     */
    public function beginTransaction(): bool;


    /**
     * Commits transaction.
     * Returns bool TRUE on success or FALSE on failure.
     *
     * @return bool
     *
     * @throws TransactionException if there is no active transaction.
     */
    public function commitTransaction(): bool;

    /**
     * Rollbacks transaction.
     * Returns bool TRUE on success or FALSE on failure.
     *
     * @return bool
     *
     * @throws TransactionException if there is no active transaction.
     */
    public function rollbackTransaction(): bool;

    /**
     * Checks if inside a transaction.
     * Returns bool TRUE if a transaction is currently active, and FALSE if not.
     *
     * @link https://php.net/manual/en/pdo.intransaction.php
     *
     * @return bool
     */
    public function inTransaction(): bool;
}