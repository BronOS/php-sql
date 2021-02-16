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


use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSql\QueryBuilder\Criteria;
use BronOS\PhpSqlSchema\Column\Json\JsonColumn;
use BronOS\PhpSqlSchema\Exception\ColumnDeclarationException;
use JsonException;

/**
 * JSON SQL column representation.
 *
 * ???
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class JsonField extends AbstractField implements JsonFieldInterface
{
    private ?string $value = null;

    /**
     * IntColumn constructor.
     *
     * @param AbstractModel $model
     * @param array         $row
     * @param string        $name
     * @param bool          $isNullable
     * @param string|null   $default
     * @param string|null   $comment
     *
     * @throws ColumnDeclarationException
     */
    public function __construct(
        AbstractModel $model,
        array $row,
        string $name,
        bool $isNullable = false,
        ?string $default = null,
        ?string $comment = null
    ) {
        parent::__construct($model, $name);

        if (!$this->isColumnExists()) {
            $this->setColumn(new JsonColumn(
                $name,
                $isNullable,
                $default,
                $comment
            ));
        }

        $this->setValueFromRow($row, $name);
    }

    /**
     * Returns column object.
     *
     * @return JsonColumn
     */
    public function getColumn(): JsonColumn
    {
        return parent::getColumn();
    }

    /**
     * @return array|null
     *
     * @throws JsonException
     */
    public function getValueAsArray(): ?array
    {
        if (is_null($this->value)) {
            return null;
        }

        return json_decode($this->value, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->isDirty = true;
        $this->getModel()->isDirty = true;
        $this->value = $value;
    }

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
            $this->value = (string)$row[$fieldName];
        }
    }
}
