<?php

namespace BronOS\PhpSql\Tests\Orm;


use BronOS\PhpSql\Exception\NotFoundException;
use BronOS\PhpSql\Exception\ResolvedException;
use BronOS\PhpSql\Exception\UnresolvedException;
use BronOS\PhpSql\Orm\AbstractOrmModel;
use BronOS\PhpSql\Tests\Mock\BlogOrmModel;

class DeleteResultSetTest extends ResultSetFactoryTest
{
    public function testDelete()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->find();
        $rs->executeRawQuery("INSERT INTO blog (title, second_title) VALUES ('blog2', 'test2')");
        $id = (int)$rs->getPdo()->lastInsertId();

        /** @var BlogOrmModel $model */
        $model = $blog->find($blog->getId()->eq($id))->first();
        $rc = $model->deleteByPk()->affectedRows();

        $this->assertEquals(1, $rc);
        $this->assertTrue($model->isDeleted);

        $this->expectException(NotFoundException::class);
        $blog->find($blog->getId()->eq($id))->first();
    }

    public function testUnresolved()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->newDelete($blog->getId()->eq(1));

        $this->expectException(UnresolvedException::class);
        $rs->affectedRows();
    }

    public function testResolved()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->find();
        $rs->executeRawQuery("INSERT INTO blog (title, second_title) VALUES ('blog2', 'test2')");
        $id = (int)$rs->getPdo()->lastInsertId();
        $rs = $blog->delete($blog->getId()->eq($id));

        $this->expectException(ResolvedException::class);
        $rs->exec();
    }

    protected function setUp(): void
    {
        AbstractOrmModel::$resultSetFactory = $this->getResultSetFactory();
    }
}
