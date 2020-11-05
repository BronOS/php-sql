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


use Aura\SqlQuery\AbstractQuery;
use Aura\SqlQuery\Common\ValuesInterface;

/**
 * Int value trait.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
trait IntValueTrait
{
    use ModelTrait;
    use DirtyTrait;
    use BindTrait;

    private ?int $value = null;

    /**
     * Binds field with where statement.
     * Uses internal value when passed value is null.
     *
     * @param AbstractQuery $query
     * @param int|null      $value
     * @param string        $operator
     */
    public function bindWhere(AbstractQuery $query, ?int $value = null, string $operator = '='): void
    {
        $this->where($query, $this->getColumn()->getName(), (string)($value ?? $this->getValue()), $operator);
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
     * Binds field with query column.
     * Uses internal value when passed value is null.
     *
     * @param ValuesInterface $query
     * @param int|null        $value
     */
    public function bindCol(ValuesInterface $query, ?int $value = null): void
    {
        $this->col($query, $this->getColumn()->getName(), (string)($value ?? $this->getValue()));
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