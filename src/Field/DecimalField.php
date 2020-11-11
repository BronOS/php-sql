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


use BronOS\PhpSql\Field\Helper\FloatFieldInterface;
use BronOS\PhpSql\Field\Helper\FloatFieldTrait;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSqlSchema\Column\Numeric\DecimalColumn;
use BronOS\PhpSqlSchema\Exception\ColumnDeclarationException;

/**
 * A representation of the DECIMAL SQL column.
 *
 * The DECIMAL data type is used to store exact numeric values in the database.
 * We often use the DECIMAL data type for columns that preserve exact precision e.g.,
 * money data in accounting systems.
 *
 * To define a column whose data type is DECIMAL you use the following syntax:
 *      column_name  DECIMAL(P,D);
 * In the syntax above:
 *      P is the precision that represents the number of significant digits.
 *        The range of P is 1 to 65.
 *      D is the scale that that represents the number of digits after the decimal point.
 *        The range of D is 0 and 30. MySQL requires that D is less than or equal to (<=) P.
 *
 * The DECIMAL(P,D) means that the column can store up to P digits with D decimals.
 * The actual range of the decimal column depends on the precision and scale.
 *
 * Like the INT data type, the DECIMAL type also has UNSIGNED and ZEROFILL attributes.
 * If we use the UNSIGNED attribute, the column with DECIMAL UNSIGNED will not accept negative values.
 *
 * In case we use ZEROFILL, MySQL will pad the display value by 0
 * up to display width specified by the column definition.
 * In addition, if we use ZERO FILL for the DECIMAL column,
 * MySQL will add the UNSIGNED attribute to the column automatically.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class DecimalField extends AbstractField implements FloatFieldInterface
{
    use FloatFieldTrait;

    /**
     * AbstractDecimalColumn constructor.
     *
     * @param AbstractModel $model
     * @param array         $row
     * @param string        $name
     * @param int           $precision
     * @param int           $scale
     * @param bool          $isUnsigned
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
        int $precision = 10,
        int $scale = 2,
        bool $isUnsigned = false,
        bool $isNullable = false,
        ?string $default = null,
        bool $isZerofill = false,
        ?string $comment = null
    ) {
        parent::__construct($model, $name);

        if (!$this->isColumnExists()) {
            $this->setColumn(new DecimalColumn(
                $name,
                $precision,
                $scale,
                $isUnsigned,
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
     * @return DecimalColumn
     */
    public function getColumn(): DecimalColumn
    {
        return parent::getColumn();
    }
}
