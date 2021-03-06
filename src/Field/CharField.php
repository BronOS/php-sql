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
use BronOS\PhpSqlSchema\Column\String\CharColumn;
use BronOS\PhpSqlSchema\Exception\ColumnDeclarationException;

/**
 * CHAR SQL column representation.
 *
 * The CHAR and VARCHAR types are similar, but differ in the way they are stored and retrieved.
 * They also differ in maximum length and in whether trailing spaces are retained.
 *
 * The CHAR and VARCHAR types are declared with a length that indicates the maximum number
 * of characters you want to store. For example, CHAR(30) can hold up to 30 characters.
 *
 * The length of a CHAR column is fixed to the length that you declare when you create the table.
 * The length can be any value from 0 to 255.
 * When CHAR values are stored, they are right-padded with spaces to the specified length.
 * When CHAR values are retrieved, trailing spaces are removed unless the PAD_CHAR_TO_FULL_LENGTH SQL mode is enabled.
 *
 * Values in VARCHAR columns are variable-length strings.
 * The length can be specified as a value from 0 to 65,535.
 * The effective maximum length of a VARCHAR is subject to the maximum row size
 * (65,535 bytes, which is shared among all columns) and the character set used.
 *
 * In contrast to CHAR, VARCHAR values are stored as a 1-byte or 2-byte length prefix plus data.
 * The length prefix indicates the number of bytes in the value.
 * A column uses one length byte if values require no more than 255 bytes,
 * two length bytes if values may require more than 255 bytes.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
class CharField extends AbstractStringField implements StringFieldInterface
{
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
            $this->setColumn(new CharColumn(
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
     * @return CharColumn
     */
    public function getColumn(): CharColumn
    {
        return parent::getColumn();
    }
}
