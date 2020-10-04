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


use BronOS\PhpSql\Field\Helper\DirtyTrait;
use BronOS\PhpSql\Field\Helper\ModelTrait;
use BronOS\PhpSql\Model\AbstractModel;
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
class AbstractField
{
    use ModelTrait;
    use DirtyTrait;

    protected static array $columns = [];
    private string $columnName;

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
     * @return string
     */
    private function getModelClassName(): string
    {
        return get_class($this->getModel()) . '::' . $this->columnName;
    }
}