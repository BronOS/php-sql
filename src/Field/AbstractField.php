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
use BronOS\PhpSqlSchema\Column\ColumnInterface;

/**
 * A column decorator.
 * Responsible for handling a field value and column declaration linked with model.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
abstract class AbstractField
{
    private AbstractModel $model;
    public bool $isDirty = false;
    protected static array $columns = [];
    protected string $columnName;

    /**
     * AbstractField constructor.
     *
     * @param AbstractModel $model
     * @param string        $columnName
     */
    public function __construct(AbstractModel $model, string $columnName)
    {
        $this->setModel($model);
        $this->columnName = $columnName;
    }

    /**
     * @param string $operator
     * @param mixed  $bind
     *
     * @param bool   $and
     *
     * @return Criteria
     */
    protected function toCriteria(string $operator, $bind, bool $and = true): Criteria
    {
        $cn = $this->getColumn()->getName();
        $statement = sprintf('%s %s', $cn, $operator);

        if (in_array(strtoupper($operator), ['IS NULL', 'IS NOT NULL'])) {
            return new Criteria($statement, [], $and);
        }

        $cnBind = ':' . $cn;
        $statement .= sprintf(is_array($bind) ? '(%s)' : ' %s', $cnBind);

        return new Criteria($statement, [$cn => $bind], $and);
    }

    /**
     * Returns column object.
     *
     * @return ColumnInterface
     */
    public function getColumn(): ColumnInterface
    {
        return self::$columns[$this->getModelClassName()];
    }

    /**
     * Returns whether column already exists in the registry.
     *
     * @return bool
     */
    protected function isColumnExists(): bool
    {
        return isset(self::$columns[$this->getModelClassName()]);
    }

    /**
     * Sets column.
     *
     * @param ColumnInterface $column
     */
    protected function setColumn(ColumnInterface $column): void
    {
        self::$columns[$this->getModelClassName()] = $column;
    }

    /**
     * @return AbstractModel
     */
    public function getModel(): AbstractModel
    {
        return $this->model;
    }

    /**
     * @param AbstractModel $model
     */
    public function setModel(AbstractModel $model): void
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    protected function getModelClassName(): string
    {
        return get_class($this->getModel()) . '::' . $this->columnName;
    }

    /**
     * Returns key ~> value array of query column.
     *
     * @return array
     */
    abstract public function toQuery(): array;

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
    abstract public function like(?string $value = null, bool $and = true): Criteria;

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
    abstract public function notLike(?string $value = null, bool $and = true): Criteria;

    /**
     * Returns "IS NULL" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field IS NULL
     *
     * @param bool $and
     *
     * @return Criteria
     */
    public function isNull(bool $and = true): Criteria
    {
        return $this->toCriteria('IS NULL', null, $and);
    }

    /**
     * Returns "IS NOT NULL" SQL WHERE statement including value for binds.
     * If value was not pass, use own internal value.
     *    Example: field IS NOT NULL
     *
     * @param bool $and
     *
     * @return Criteria
     */
    public function isNotNull(bool $and = true): Criteria
    {
        return $this->toCriteria('IS NOT NULL', null, $and);
    }
}