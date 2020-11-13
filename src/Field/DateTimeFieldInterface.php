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

namespace BronOS\PhpSql\Field;


use BronOS\PhpSql\QueryBuilder\Criteria;
use DateTime;

/**
 * Date time field interface.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
interface DateTimeFieldInterface
{
    /**
     * @return DateTime|null
     */
    public function getValue(): ?DateTime;

    /**
     * @param DateTime|null $value
     */
    public function setValue(?DateTime $value): void;

    /**
     * Returns "Equal" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field = ?
     *
     * @param DateTime|null $value
     * @param bool          $and
     *
     * @return Criteria
     */
    public function eq(?DateTime $value = null, bool $and = true): Criteria;

    /**
     * Returns "NOT Equal" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field <> ?
     *
     * @param DateTime|null $value
     * @param bool          $and
     *
     * @return Criteria
     */
    public function ne(?DateTime $value = null, bool $and = true): Criteria;

    /**
     * Returns "Greater Than" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field > ?
     *
     * @param DateTime|null $value
     * @param bool          $and
     *
     * @return Criteria
     */
    public function gt(?DateTime $value = null, bool $and = true): Criteria;

    /**
     * Returns "Greater Than or Equal" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field >= ?
     *
     * @param DateTime|null $value
     * @param bool          $and
     *
     * @return Criteria
     */
    public function gte(?DateTime $value = null, bool $and = true): Criteria;

    /**
     * Returns "Less Than" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field < ?
     *
     * @param DateTime|null $value
     * @param bool          $and
     *
     * @return Criteria
     */
    public function lt(?DateTime $value = null, bool $and = true): Criteria;

    /**
     * Returns "Less Than or Equal" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field <= ?
     *
     * @param DateTime|null $value
     * @param bool          $and
     *
     * @return Criteria
     */
    public function lte(?DateTime $value = null, bool $and = true): Criteria;

    /**
     * Returns "In Array" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field IN (?,?,?...)
     *
     * @param DateTime[] $values
     * @param bool       $and
     *
     * @return Criteria
     */
    public function in(array $values, bool $and = true): Criteria;

    /**
     * Returns "NOT In Array" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field NOT IN (?,?,?...)
     *
     * @param DateTime[] $values
     * @param bool       $and
     *
     * @return Criteria
     */
    public function nin(array $values, bool $and = true): Criteria;
}