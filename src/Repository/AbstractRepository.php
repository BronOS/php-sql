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

namespace BronOS\PhpSql\Repository;


use Aura\SqlQuery\QueryFactory;
use BronOS\PhpSql\Model\AbstractModel;
use BronOS\PhpSql\Repository\Part\ExecuteTrait;
use BronOS\PhpSql\Repository\Part\ModelTrait;
use BronOS\PhpSql\Repository\Part\PdoTrait;
use BronOS\PhpSql\Repository\Part\QueryBuilderTrait;
use BronOS\PhpSql\Repository\Part\ReadTrait;
use BronOS\PhpSql\Repository\Part\TransactionTrait;
use BronOS\PhpSql\Repository\Part\WriteTrait;
use PDO;

/**
 * Abstract repository.
 * Responsible for building and executing sql queries.
 *
 * @package   bronos\php-sql
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2020
 * @license   https://opensource.org/licenses/MIT
 */
abstract class AbstractRepository implements RepositoryInterface
{
    use PdoTrait;
    use ModelTrait;
    use ExecuteTrait;
    use QueryBuilderTrait;
    use TransactionTrait;
    use WriteTrait;
    use ReadTrait;

    /**
     * AbstractRepository constructor.
     *
     * @param PDO           $pdo
     * @param QueryFactory  $queryFactory
     * @param AbstractModel $model
     */
    public function __construct(
        PDO $pdo,
        QueryFactory $queryFactory,
        AbstractModel $model
    ) {
        $this->pdo = $pdo;
        $this->model = $model;
        $this->queryFactory = $queryFactory;
    }
}