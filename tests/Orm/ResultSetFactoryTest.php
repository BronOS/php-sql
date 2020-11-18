<?php

namespace BronOS\PhpSql\Tests\Orm;


use Aura\SqlQuery\Common\DeleteBuilder;
use Aura\SqlQuery\Common\SelectBuilder;
use Aura\SqlQuery\Common\UpdateBuilder;
use Aura\SqlQuery\Mysql\InsertBuilder;
use Aura\SqlQuery\Mysql\Quoter;
use BronOS\PhpSql\Orm\DeleteResultSet;
use BronOS\PhpSql\Orm\InsertResultSet;
use BronOS\PhpSql\Orm\ResultSetFactory;
use BronOS\PhpSql\Orm\ResultSetFactoryInterface;
use BronOS\PhpSql\Orm\SelectResultSet;
use BronOS\PhpSql\Orm\UpdateResultSet;
use BronOS\PhpSql\Tests\BaseTestCase;
use BronOS\PhpSql\Tests\Mock\BlogModel;
use BronOS\PhpSql\Tests\Mock\BlogOrmModel;
use PHPUnit\Framework\TestCase;

class ResultSetFactoryTest extends BaseTestCase
{
    public function testNewInsert()
    {
        $this->assertInstanceOf(
            InsertResultSet::class,
            $this->getResultSetFactory()->newInsert(new BlogOrmModel())
        );
    }

    public function testNewSelect()
    {
        $this->assertInstanceOf(
            SelectResultSet::class,
            $this->getResultSetFactory()->newSelect(new BlogOrmModel())
        );
    }

    public function testNewDelete()
    {
        $this->assertInstanceOf(
            DeleteResultSet::class,
            $this->getResultSetFactory()->newDelete(new BlogOrmModel())
        );
    }

    public function testNewUpdate()
    {
        $this->assertInstanceOf(
            UpdateResultSet::class,
            $this->getResultSetFactory()->newUpdate(new BlogOrmModel())
        );
    }

    protected function getResultSetFactory(): ResultSetFactoryInterface
    {
        return new ResultSetFactory(
            $this->getPdo(),
            new Quoter(),
            new SelectBuilder(),
            new InsertBuilder(),
            new UpdateBuilder(),
            new DeleteBuilder()
        );
    }
}
