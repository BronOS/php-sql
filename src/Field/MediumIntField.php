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


use BronOS\PhpSql\Field\Helper\IntFieldInterface;
use BronOS\PhpSql\Field\Helper\IntFieldTrait;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSqlSchema\Column\Numeric\MediumIntColumn;
use BronOS\PhpSqlSchema\Exception\ColumnDeclarationException;

/**
 * A representation of the MEDIUMINT SQL column.
 *
 * The number in the bracket in int(N) is often confused by the maximum size allowed for the column,
 * as it does in the case of varchar(N).
 * But this is not the case with Integer data types - the number N
 * in the bracket is not the maximum size for the column,
 * but simply a parameter to tell MySQL what width to display the column at when
 * the table’s data is being viewed via the MySQL console
 * (when you’re using the ZEROFILL attribute).
 *
 * An MEDIUMINT will always be 3 bytes (24 bit) no matter what length is specified.
 * Signed value is: -2^(24-1) to 0 to 2^(24-1)-1 = -8388608 to 0 to 8388607. One bit is for sign.
 * Unsigned values is: 0 to 2^24-1 = 0 to 16777215
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class MediumIntField extends AbstractField implements IntFieldInterface
{
    use IntFieldTrait;

    /**
     * @param AbstractModel $model
     * @param array         $row
     * @param string        $name
     * @param int           $size
     * @param bool          $isUnsigned
     * @param bool          $isAutoincrement
     * @param bool          $isNullable
     * @param string|null   $default
     * @param bool          $isZerofill
     * @param string|null   $comment
     *
     * @throws ColumnDeclarationException
     */
    public function __construct(
        AbstractModel $model,
        array $row,
        string $name,
        int $size,
        bool $isUnsigned = false,
        bool $isAutoincrement = false,
        bool $isNullable = false,
        ?string $default = null,
        bool $isZerofill = false,
        ?string $comment = null
    ) {
        parent::__construct($model, $name);

        if (!$this->isColumnExists()) {
            $this->setColumn(new MediumIntColumn(
                $name,
                $size,
                $isUnsigned,
                $isAutoincrement,
                $isNullable,
                $default,
                $isZerofill,
                $comment
            ));
        }

        $this->setValueFromRow($row, $name);
    }

    /**
     * Returns column object.
     *
     * @return MediumIntColumn
     */
    public function getColumn(): MediumIntColumn
    {
        return parent::getColumn();
    }
}
