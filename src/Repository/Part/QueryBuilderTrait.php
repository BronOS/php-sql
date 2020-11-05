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

namespace BronOS\PhpSql\Repository\Part;


use Aura\SqlQuery\AbstractQuery;
use Aura\SqlQuery\Common\DeleteInterface;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\QueryFactory;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Field\Helper\ArrayValueFieldInterface;
use BronOS\PhpSql\Field\Helper\BoolValueFieldInterface;
use BronOS\PhpSql\Field\Helper\DateTimeValueFieldInterface;
use BronOS\PhpSql\Field\Helper\FloatValueFieldInterface;
use BronOS\PhpSql\Field\Helper\IntValueFieldInterface;
use BronOS\PhpSql\Field\Helper\StringValueFieldInterface;
use BronOS\PhpSql\Model\AbstractModel;
use DateTime;

/**
 * Query builder trait
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
trait QueryBuilderTrait
{
    use ModelTrait;

    protected QueryFactory $queryFactory;

    /**
     * Returns new select query object filled with table name and where statement.
     *
     * @param IntValueFieldInterface $field
     * @param int                    $value
     * @param bool                   $withColumns
     *
     * @return AbstractQuery|SelectInterface
     *
     * @throws PhpSqlException
     */
    public function newSelectWhereInt(
        IntValueFieldInterface $field,
        int $value,
        bool $withColumns = true
    ): SelectInterface {
        $select = $this->newSelect($withColumns);
        $field->bindWhere($select, $value);
        return $select;
    }

    /**
     * Returns new select query object filled with table name.
     *
     * @param bool $withColumns
     *
     * @return AbstractQuery|SelectInterface
     *
     * @throws PhpSqlException
     */
    public function newSelect(bool $withColumns = true): SelectInterface
    {
        $select = $this->getQueryFactory()->newSelect();

        if ($withColumns) {
            $select->cols($this->getColumnNames());
        }

        return $select->from($this->getTableName());
    }

    /**
     * @return QueryFactory
     */
    public function getQueryFactory(): QueryFactory
    {
        return $this->queryFactory;
    }

    /**
     * Returns new select query object filled with table name and where statement.
     *
     * @param FloatValueFieldInterface $field
     * @param float                    $value
     * @param bool                     $withColumns
     *
     * @return AbstractQuery|SelectInterface
     *
     * @throws PhpSqlException
     */
    public function newSelectWhereFloat(
        FloatValueFieldInterface $field,
        float $value,
        bool $withColumns = true
    ): SelectInterface {
        $select = $this->newSelect($withColumns);
        $field->bindWhere($select, $value);
        return $select;
    }

    /**
     * Returns new select query object filled with table name and where statement.
     *
     * @param BoolValueFieldInterface $field
     * @param bool                    $value
     * @param bool                    $withColumns
     *
     * @return AbstractQuery|SelectInterface
     *
     * @throws PhpSqlException
     */
    public function newSelectWhereBool(
        BoolValueFieldInterface $field,
        bool $value,
        bool $withColumns = true
    ): SelectInterface {
        $select = $this->newSelect($withColumns);
        $field->bindWhere($select, $value);
        return $select;
    }

    /**
     * Returns new select query object filled with table name and where statement.
     *
     * @param StringValueFieldInterface $field
     * @param string                    $value
     * @param bool                      $withColumns
     *
     * @return AbstractQuery|SelectInterface
     *
     * @throws PhpSqlException
     */
    public function newSelectWhereString(
        StringValueFieldInterface $field,
        string $value,
        bool $withColumns = true
    ): SelectInterface {
        $select = $this->newSelect($withColumns);
        $field->bindWhere($select, $value);
        return $select;
    }

    /**
     * Returns new select query object filled with table name and where statement.
     *
     * @param ArrayValueFieldInterface $field
     * @param array                    $value
     * @param bool                     $withColumns
     *
     * @return AbstractQuery|SelectInterface
     *
     * @throws PhpSqlException
     */
    public function newSelectWhereArray(
        ArrayValueFieldInterface $field,
        array $value,
        bool $withColumns = true
    ): SelectInterface {
        $select = $this->newSelect($withColumns);
        $field->bindWhere($select, $value);
        return $select;
    }

    /**
     * Returns new select query object filled with table name and where statement.
     *
     * @param DateTimeValueFieldInterface $field
     * @param DateTime                    $value
     * @param bool                        $withColumns
     *
     * @return AbstractQuery|SelectInterface
     *
     * @throws PhpSqlException
     */
    public function newSelectWhereDateTime(
        DateTimeValueFieldInterface $field,
        DateTime $value,
        bool $withColumns = true
    ): SelectInterface {
        $select = $this->newSelect($withColumns);
        $field->bindWhere($select, $value);
        return $select;
    }

    /**
     * Returns new insert query object with table name.
     *
     * @return AbstractQuery|InsertInterface
     *
     * @throws PhpSqlException
     */
    public function newInsert(): InsertInterface
    {
        return $this->getQueryFactory()->newInsert()->into($this->getTableName());
    }

    /**
     * Returns new insert query object with table name and all dirty fields.
     *
     * @param AbstractModel $model
     *
     * @return InsertInterface|AbstractQuery
     *
     * @throws PhpSqlException
     */
    public function newInsertByModel(AbstractModel $model): InsertInterface
    {
        $insert = $this->getQueryFactory()->newInsert();

        foreach ($model->getDirtyFields() as $field) {
            $field->bindCol($insert);
        }

        return $insert->into($this->getTableName());
    }

    /**
     * Returns new update query object with table name.
     *
     * @return AbstractQuery|UpdateInterface
     *
     * @throws PhpSqlException
     */
    public function newUpdate(): UpdateInterface
    {
        return $this->getQueryFactory()->newUpdate()->table($this->getTableName());
    }

    /**
     * @param AbstractModel $model
     * @param callable      $func
     *
     * @return UpdateInterface
     *
     * @throws PhpSqlException
     */
    private function newUpdateWhere(AbstractModel $model, callable $func): UpdateInterface
    {
        $update = $this->getQueryFactory()->newUpdate();

        foreach ($model->getDirtyFields() as $field) {
            $field->bindCol($update);
        }

        $func($update);

        return $update->table($this->getTableName());
    }

    /**
     * Returns new update query object with table name, all dirty fields and where statement.
     *
     * @param AbstractModel          $model
     * @param IntValueFieldInterface $whereBy
     *
     * @return UpdateInterface|AbstractQuery
     *
     * @throws PhpSqlException
     */
    public function newUpdateWhereInt(AbstractModel $model, IntValueFieldInterface $whereBy): UpdateInterface
    {
        return $this->newUpdateWhere($model, function (AbstractQuery $update) use ($whereBy) {
            $whereBy->bindWhere($update);
        });
    }

    /**
     * Returns new update query object with table name, all dirty fields and where statement.
     *
     * @param AbstractModel            $model
     * @param FloatValueFieldInterface $whereBy
     *
     * @return UpdateInterface
     *
     * @throws PhpSqlException
     */
    public function newUpdateWhereFloat(AbstractModel $model, FloatValueFieldInterface $whereBy): UpdateInterface
    {
        return $this->newUpdateWhere($model, function (AbstractQuery $update) use ($whereBy) {
            $whereBy->bindWhere($update);
        });
    }

    /**
     * Returns new update query object with table name, all dirty fields and where statement.
     *
     * @param AbstractModel           $model
     * @param BoolValueFieldInterface $whereBy
     *
     * @return UpdateInterface
     *
     * @throws PhpSqlException
     */
    public function newUpdateWhereBool(AbstractModel $model, BoolValueFieldInterface $whereBy): UpdateInterface
    {
        return $this->newUpdateWhere($model, function (AbstractQuery $update) use ($whereBy) {
            $whereBy->bindWhere($update);
        });
    }

    /**
     * Returns new update query object with table name, all dirty fields and where statement.
     *
     * @param AbstractModel             $model
     * @param StringValueFieldInterface $whereBy
     *
     * @return UpdateInterface
     *
     * @throws PhpSqlException
     */
    public function newUpdateWhereString(AbstractModel $model, StringValueFieldInterface $whereBy): UpdateInterface
    {
        return $this->newUpdateWhere($model, function (AbstractQuery $update) use ($whereBy) {
            $whereBy->bindWhere($update);
        });
    }

    /**
     * Returns new update query object with table name, all dirty fields and where statement.
     *
     * @param AbstractModel            $model
     * @param ArrayValueFieldInterface $whereBy
     *
     * @return UpdateInterface
     *
     * @throws PhpSqlException
     */
    public function newUpdateWhereArray(AbstractModel $model, ArrayValueFieldInterface $whereBy): UpdateInterface
    {
        return $this->newUpdateWhere($model, function (AbstractQuery $update) use ($whereBy) {
            $whereBy->bindWhere($update);
        });
    }

    /**
     * Returns new update query object with table name, all dirty fields and where statement.
     *
     * @param AbstractModel               $model
     * @param DateTimeValueFieldInterface $whereBy
     *
     * @return UpdateInterface
     *
     * @throws PhpSqlException
     */
    public function newUpdateWhereDateTime(AbstractModel $model, DateTimeValueFieldInterface $whereBy): UpdateInterface
    {
        return $this->newUpdateWhere($model, function (AbstractQuery $update) use ($whereBy) {
            $whereBy->bindWhere($update);
        });
    }

    /**
     * Returns new delete query object with table name.
     *
     * @return AbstractQuery|DeleteInterface
     *
     * @throws PhpSqlException
     */
    public function newDelete(): DeleteInterface
    {
        return $this->getQueryFactory()->newDelete()->from($this->getTableName());
    }

    /**
     * Returns new delete query object with table name and where statement.
     *
     * @param IntValueFieldInterface $field
     * @param int                    $value
     *
     * @return AbstractQuery|DeleteInterface
     *
     * @throws PhpSqlException
     */
    public function newDeleteWhereInt(IntValueFieldInterface $field, int $value): DeleteInterface
    {
        $delete = $this->newDelete();
        $field->bindWhere($delete, $value);
        return $delete;
    }

    /**
     * Returns new delete query object with table name and where statement.
     *
     * @param FloatValueFieldInterface $field
     * @param float                    $value
     *
     * @return AbstractQuery|DeleteInterface
     *
     * @throws PhpSqlException
     */
    public function newDeleteWhereFloat(FloatValueFieldInterface $field, float $value): DeleteInterface
    {
        $delete = $this->newDelete();
        $field->bindWhere($delete, $value);
        return $delete;
    }

    /**
     * Returns new delete query object with table name and where statement.
     *
     * @param StringValueFieldInterface $field
     * @param string                    $value
     *
     * @return AbstractQuery|DeleteInterface
     *
     * @throws PhpSqlException
     */
    public function newDeleteWhereString(StringValueFieldInterface $field, string $value): DeleteInterface
    {
        $delete = $this->newDelete();
        $field->bindWhere($delete, $value);
        return $delete;
    }

    /**
     * Returns new delete query object with table name and where statement.
     *
     * @param BoolValueFieldInterface $field
     * @param bool                    $value
     *
     * @return AbstractQuery|DeleteInterface
     *
     * @throws PhpSqlException
     */
    public function newDeleteWhereBool(BoolValueFieldInterface $field, bool $value): DeleteInterface
    {
        $delete = $this->newDelete();
        $field->bindWhere($delete, $value);
        return $delete;
    }

    /**
     * Returns new delete query object with table name and where statement.
     *
     * @param ArrayValueFieldInterface $field
     * @param array                    $value
     *
     * @return AbstractQuery|DeleteInterface
     *
     * @throws PhpSqlException
     */
    public function newDeleteWhereArray(ArrayValueFieldInterface $field, array $value): DeleteInterface
    {
        $delete = $this->newDelete();
        $field->bindWhere($delete, $value);
        return $delete;
    }

    /**
     * Returns new delete query object with table name and where statement.
     *
     * @param DateTimeValueFieldInterface $field
     * @param DateTime                    $value
     *
     * @return AbstractQuery|DeleteInterface
     *
     * @throws PhpSqlException
     */
    public function newDeleteWhereDateTime(DateTimeValueFieldInterface $field, DateTime $value): DeleteInterface
    {
        $delete = $this->newDelete();
        $field->bindWhere($delete, $value);
        return $delete;
    }
}