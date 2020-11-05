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


use BronOS\PhpSql\Field\Helper\StringValueFieldInterface;
use BronOS\PhpSql\Field\Helper\StringValueTrait;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSqlSchema\Column\String\BinaryColumn;
use BronOS\PhpSqlSchema\Exception\ColumnDeclarationException;

/**
 * BINARY SQL column representation.
 *
 * The BINARY and VARBINARY types are similar to CHAR and VARCHAR,
 * except that they store binary strings rather than nonbinary strings.
 * That is, they store byte strings rather than character strings.
 * This means they have the binary character set and collation,
 * and comparison and sorting are based on the numeric values of the bytes in the values.
 *
 * The permissible maximum length is the same for BINARY and VARBINARY as it is for CHAR and VARCHAR,
 * except that the length for BINARY and VARBINARY is measured in bytes rather than characters.
 *
 * The BINARY and VARBINARY data types are distinct from the CHAR BINARY and VARCHAR BINARY data types.
 * For the latter types, the BINARY attribute does not cause the column to be treated as a binary string column.
 * Instead, it causes the binary (_bin) collation for the column character set
 * (or the table default character set if no column character set is specified) to be used,
 * and the column itself stores nonbinary character strings rather than binary byte strings.
 * For example, if the default character set is utf8mb4,
 * CHAR(5) BINARY is treated as CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin.
 * This differs from BINARY(5), which stores 5-byte binary strings that have the binary character set and collation.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class BinaryField extends AbstractField implements StringValueFieldInterface
{
    use StringValueTrait;

    /**
     * IntColumn constructor.
     *
     * @param AbstractModel $model
     * @param array         $row
     * @param string        $name
     * @param int           $size
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
        int $size = 1,
        bool $isNullable = false,
        ?string $default = null,
        ?string $charset = null,
        ?string $collate = null,
        ?string $comment = null
    ) {
        parent::__construct($model, $name);

        if (!$this->isColumnExists()) {
            $this->setColumn(new BinaryColumn(
                $name,
                $size,
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
     * @return BinaryColumn
     */
    public function getColumn(): BinaryColumn
    {
        return parent::getColumn();
    }
}
