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


use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\QueryBuilder\Criteria;
use DateTime;
use Exception;

/**
 * A column decorator.
 * Responsible for handling a field value and column declaration linked with model.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
abstract class AbstractDateTimeField extends AbstractField implements DateTimeFieldInterface
{
    private ?DateTime $value = null;

    /**
     * Returns key ~> value array of query column.
     *
     * @return array
     */
    public function toQuery(): array
    {
        $value = $this->getValue();
        if (!is_null($value)) {
            $value = $this->format($value);
        }

        return [$this->getColumn()->getName() => $value];
    }

    /**
     * @return DateTime|null
     */
    public function getValue(): ?DateTime
    {
        return $this->value;
    }

    /**
     * @param DateTime|null $value
     */
    public function setValue(?DateTime $value): void
    {
        $this->isDirty = true;
        $this->getModel()->isDirty = true;
        $this->value = $value;
    }

    /**
     * @param DateTime|null $dt
     *
     * @return string|null
     */
    private function format(?DateTime $dt): ?string
    {
        if (is_null($dt)) {
            $dt = $this->getValue();
        }

        if (is_null($dt)) {
            return null;
        }

        return $dt->format('Y-m-d H:i:s');
    }

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
    public function eq(?DateTime $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('=', [$this->format($value)], $and);
    }

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
    public function ne(?DateTime $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('<>', [$this->format($value)], $and);
    }

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
    public function gt(?DateTime $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('>', [$this->format($value)], $and);
    }

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
    public function gte(?DateTime $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('>=', [$this->format($value)], $and);
    }

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
    public function lt(?DateTime $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('<', [$this->format($value)], $and);
    }

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
    public function lte(?DateTime $value = null, bool $and = true): Criteria
    {
        return $this->toCriteria('<=', [$this->format($value)], $and);
    }

    /**
     * Returns "In Array" SQL WHERE statement including values for binds.
     * If value was not pass, use own internal value.
     *    Example: field IN (?,?,?...)
     *
     * @param DateTime[] $values
     * @param bool       $and
     *
     * @return Criteria
     */
    public function in(array $values, bool $and = true): Criteria
    {
        return $this->toCriteria(
            'IN',
            array_map(function (?DateTime $value) {
                return $this->format($value);
            }, count($values) > 0 ? array_values($values) : [$this->getValue()]),
            $and
        );
    }

    /**
     * Returns "NOT In Array" SQL WHERE statement including values for binds.
     * If value was not pass, use own internal value.
     *    Example: field NOT IN (?,?,?...)
     *
     * @param DateTime[] $values
     * @param bool       $and
     *
     * @return Criteria
     */
    public function nin(array $values, bool $and = true): Criteria
    {
        return $this->toCriteria(
            'NOT IN',
            array_map(function (?DateTime $value) {
                return $this->format($value);
            }, count($values) > 0 ? array_values($values) : [$this->getValue()]),
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
        return $this->toCriteria('LIKE', [$value ?: $this->format($this->getValue())], $and);
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
        return $this->toCriteria('LIKE', [$value ?: $this->format($this->getValue())], $and);
    }

    /**
     * @param array  $row
     * @param string $fieldName
     *
     * @throws PhpSqlException
     */
    protected function setValueFromRow(array $row, string $fieldName): void
    {
        if (isset($row[$fieldName]) && !is_null($row[$fieldName])) {
            try {
                $this->value = new DateTime($row[$fieldName]);
            } catch (Exception $e) {
                throw new PhpSqlException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
}