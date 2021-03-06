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

/**
 * A column decorator.
 * Responsible for handling a field value and column declaration linked with model.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
abstract class AbstractIntField extends AbstractField implements IntFieldInterface
{
    private ?int $value = null;

    /**
     * Returns key ~> value array of query column.
     *
     * @return array
     */
    public function toQuery(): array
    {
        return [$this->getColumn()->getName() => $this->getValue()];
    }

    /**
     * @return int|null
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * @param int|null $value
     */
    public function setValue(?int $value): void
    {
        $this->isDirty = true;
        $this->getModel()->isDirty = true;
        $this->value = $value;
    }

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
    public function eq(?int $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('=', $value ?: $this->getValue(), $and);
    }

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
    public function ne(?int $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('<>', $value ?: $this->getValue(), $and);
    }

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
    public function gt(?int $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('>', $value ?: $this->getValue(), $and);
    }

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
    public function gte(?int $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('>=', $value ?: $this->getValue(), $and);
    }

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
    public function lt(?int $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('<', $value ?: $this->getValue(), $and);
    }

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
    public function lte(?int $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('<=', $value ?: $this->getValue(), $and);
    }

    /**
     * Returns "In Array" SQL WHERE statement including values for binds.
     * If value was not pass, use own internal value.
     *    Example: field IN (?,?,?...)
     *
     * @param string[] $values
     * @param bool     $and
     *
     * @return Criteria
     */
    public function in(array $values, bool $and = true): Criteria
    {
        return $this->toCriteria(
            'IN',
            count($values) > 0 ? array_values($values) : [$this->getValue()],
            $and
        );
    }

    /**
     * Returns "NOT In Array" SQL WHERE statement including values for binds.
     * If value was not pass, use own internal value.
     *    Example: field NOT IN (?,?,?...)
     *
     * @param string[] $values
     * @param bool     $and
     *
     * @return Criteria
     */
    public function nin(array $values, bool $and = true): Criteria
    {
        return $this->toCriteria(
            'NOT IN',
            count($values) > 0 ? array_values($values) : [$this->getValue()],
            $and
        );
    }

    /**
     * Returns "Like" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field LIKE ?
     *
     * @param string|null $value
     * @param bool        $and
     *
     * @return Criteria
     */
    public function like(?string $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('LIKE', $value ?: $this->getValue(), $and);
    }

    /**
     * Returns "Not Like" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field NOT LIKE ?
     *
     * @param string|null $value
     * @param bool        $and
     *
     * @return Criteria
     */
    public function notLike(?string $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('LIKE', $value ?: $this->getValue(), $and);
    }

    /**
     * @param array  $row
     * @param string $fieldName
     */
    protected function setValueFromRow(array $row, string $fieldName): void
    {
        if (isset($row[$fieldName]) && !is_null($row[$fieldName])) {
            $this->value = (int)$row[$fieldName];
        }
    }
}