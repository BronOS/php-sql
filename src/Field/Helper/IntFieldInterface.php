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

namespace BronOS\PhpSql\Field\Helper;


use BronOS\PhpSql\QueryBuilder\Criteria;

/**
 * Int field interface.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
interface IntFieldInterface
{
    /**
     * @return int|null
     */
    public function getValue(): ?int;

    /**
     * @param int|null $value
     */
    public function setValue(?int $value): void;

    /**
     * Returns "Equal" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field = ?
     *
     * @param int|null $value
     * @param bool     $and
     *
     * @return Criteria
     */
    public function eq(?int $value = null, bool $and = true): Criteria;

    /**
     * Returns "NOT Equal" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field <> ?
     *
     * @param int|null $value
     * @param bool     $and
     *
     * @return Criteria
     */
    public function ne(?int $value = null, bool $and = true): Criteria;

    /**
     * Returns "Greater Than" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field > ?
     *
     * @param int|null $value
     * @param bool     $and
     *
     * @return Criteria
     */
    public function gt(?int $value = null, bool $and = true): Criteria;

    /**
     * Returns "Greater Than or Equal" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field >= ?
     *
     * @param int|null $value
     * @param bool     $and
     *
     * @return Criteria
     */
    public function gte(?int $value = null, bool $and = true): Criteria;

    /**
     * Returns "Less Than" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field < ?
     *
     * @param int|null $value
     * @param bool     $and
     *
     * @return Criteria
     */
    public function lt(?int $value = null, bool $and = true): Criteria;

    /**
     * Returns "Less Than or Equal" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field <= ?
     *
     * @param int|null $value
     * @param bool     $and
     *
     * @return Criteria
     */
    public function lte(?int $value = null, bool $and = true): Criteria;

    /**
     * Returns "In Array" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field IN (?,?,?...)
     *
     * @param float[] $values
     * @param bool    $and
     *
     * @return Criteria
     */
    public function in(array $values, bool $and = true): Criteria;

    /**
     * Returns "NOT In Array" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field NOT IN (?,?,?...)
     *
     * @param float[] $values
     * @param bool    $and
     *
     * @return Criteria
     */
    public function nin(array $values, bool $and = true): Criteria;
}