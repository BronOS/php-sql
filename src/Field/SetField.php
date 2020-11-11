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


use BronOS\PhpSql\Field\Helper\ArrayFieldInterface;
use BronOS\PhpSql\Field\Helper\ArrayFieldTrait;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSqlSchema\Column\String\SetColumn;
use BronOS\PhpSqlSchema\Exception\ColumnDeclarationException;

/**
 * SET SQL column representation.
 *
 * A string object that can have 0 or more values, chosen from a list of possible values.
 * You can list up to 64 values in a SET list
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class SetField extends AbstractField implements ArrayFieldInterface
{
    use ArrayFieldTrait;

    /**
     * IntColumn constructor.
     *
     * @param AbstractModel $model
     * @param array         $row
     * @param string        $name
     * @param array         $options
     * @param bool          $isNullable
     * @param string|null   $default
     * @param string|null   $charset
     * @param string|null   $collate
     * @param string|null   $comment
     *
     * @throws ColumnDeclarationException
     */
    public function __construct(
        AbstractModel $model,
        array $row,
        string $name,
        array $options,
        bool $isNullable = false,
        ?string $default = null,
        ?string $charset = null,
        ?string $collate = null,
        ?string $comment = null
    ) {
        parent::__construct($model, $name);

        if (!$this->isColumnExists()) {
            $this->setColumn(new SetColumn(
                $name,
                $options,
                $isNullable,
                $default,
                $charset,
                $collate,
                $comment
            ));
        }

        $this->setValueFromRow($row, $name);
    }

    /**
     * Returns column object.
     *
     * @return SetColumn
     */
    public function getColumn(): SetColumn
    {
        return parent::getColumn();
    }
}
