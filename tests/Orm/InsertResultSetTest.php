<?php

namespace BronOS\PhpSql\Tests\Orm;


use BronOS\PhpSql\Exception\ResolvedException;
use BronOS\PhpSql\Exception\UnresolvedException;
use BronOS\PhpSql\Orm\AbstractOrmModel;
use BronOS\PhpSql\Tests\Mock\BlogOrmModel;

class InsertResultSetTest extends ResultSetFactoryTest
{
    public function testInsert()
    {
        $blog = new BlogOrmModel();
        $blog->getTitle()->setValue('blog2');
        $blog->getSecondTitle()->setValue('test2');
        $id = (int)$blog->insert()->lastInsertedId();

        $this->assertFalse($blog->isDirty);
        $this->assertFalse($blog->getId()->isDirty);
        $this->assertFalse($blog->getTitle()->isDirty);
        $this->assertFalse($blog->getSecondTitle()->isDirty);

        /** @var BlogOrmModel $model */
        $model = $blog->find($blog->getId()->eq($id))->first();

        $this->assertInstanceOf(BlogOrmModel::class, $model);
        $this->assertEquals($id, $model->getId()->getValue());
        $this->assertEquals('blog2', $model->getTitle()->getValue());
        $this->assertEquals('test2', $model->getSecondTitle()->getValue());
    }

    public function testUnresolved()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->newInsert();

        $this->expectException(UnresolvedException::class);
        $rs->lastInsertedId();
    }

    public function testResolved()
    {
        $blog = new BlogOrmModel();
        $blog->getTitle()->setValue('blog2');
        $blog->getSecondTitle()->setValue('test2');
        $rs = $blog->insert();

        $this->expectException(ResolvedException::class);
        $rs->exec();
    }

    protected function setUp(): void
    {
        AbstractOrmModel::$resultSetFactory = $this->getResultSetFactory();
    }
}
