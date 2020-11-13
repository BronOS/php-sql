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
use Aura\SqlQuery\Common\WhereInterface;
use Aura\SqlQuery\QueryFactory;
use BronOS\PhpSql\Exception\PhpSqlException;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSql\QueryBuilder\Criteria;
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
     * @return QueryFactory
     */
    public function getQueryFactory(): QueryFactory
    {
        return $this->queryFactory;
    }

    /**
     * Returns new select query object filled with table name,
     * columns and where clause by passed criterias.
     *
     * @param Criteria ...$criterias
     *
     * @return SelectInterface|AbstractQuery
     *
     * @throws PhpSqlException
     */
    public function newSelect(Criteria ...$criterias): SelectInterface
    {
        $query = $this->getQueryFactory()->newSelect()
            ->cols($this->getColumnNames())
            ->from($this->getTableName());

        return $this->bindWhere($query, ...$criterias);
    }

    /**
     * Returns new insert query object filled with table name and all dirty fields.
     *
     * @param AbstractModel $model
     *
     * @return InsertInterface|AbstractQuery
     *
     * @throws PhpSqlException
     */
    public function newInsert(AbstractModel $model): InsertInterface
    {
        return $this->getQueryFactory()->newInsert()
            ->cols($model->dirtyFieldToQuery())
            ->into($this->getTableName());
    }

    /**
     * Returns new update query object filled with with table name,
     * all dirty fields and where clause by passed criterias.
     *
     * @param AbstractModel $model
     * @param Criteria      ...$criterias
     *
     * @return UpdateInterface|AbstractQuery
     *
     * @throws PhpSqlException
     */
    public function newUpdate(AbstractModel $model, Criteria ...$criterias): UpdateInterface
    {
        $query = $this->getQueryFactory()->newUpdate()
            ->cols($model->dirtyFieldToQuery())
            ->table($this->getTableName());

        return $this->bindWhere($query, ...$criterias);
    }

    /**
     * Returns new delete query object filled with table name and where clause by passed criterias.
     *
     * @param Criteria ...$criterias
     *
     * @return DeleteInterface|AbstractQuery
     *
     * @throws PhpSqlException
     */
    public function newDelete(Criteria ...$criterias): DeleteInterface
    {
        $query = $this->getQueryFactory()->newDelete()
            ->from($this->getTableName());
        return $this->bindWhere($query, ...$criterias);
    }

    /**
     * @param WhereInterface $query
     * @param Criteria       ...$criterias
     *
     * @return WhereInterface|AbstractQuery
     */
    private function bindWhere(WhereInterface $query, Criteria ...$criterias): WhereInterface
    {
        foreach ($criterias as $criteria) {
            if ($criteria->isAnd()) {
                $query->where($criteria->getCond(), $criteria->getBinds());
            } else {
                $query->orWhere($criteria->getCond(), $criteria->getBinds());
            }
        }

        return $query;
    }
}