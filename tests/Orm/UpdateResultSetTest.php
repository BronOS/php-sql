<?php

namespace BronOS\PhpSql\Tests\Orm;


use BronOS\PhpSql\Exception\ResolvedException;
use BronOS\PhpSql\Exception\UnresolvedException;
use BronOS\PhpSql\Orm\AbstractOrmModel;
use BronOS\PhpSql\Tests\Mock\BlogOrmModel;

class UpdateResultSetTest extends ResultSetFactoryTest
{
    public function testUpdate()
    {
        $blog = new BlogOrmModel();

        /** @var BlogOrmModel $model */
        $model = $blog->find($blog->getId()->eq(1))->first();
        $model->getSecondTitle()->setValue('UPDATED');
        $model->updateByPk();

        $this->assertFalse($blog->isDirty);
        $this->assertFalse($blog->getId()->isDirty);
        $this->assertFalse($blog->getTitle()->isDirty);
        $this->assertFalse($blog->getSecondTitle()->isDirty);

        $this->assertInstanceOf(BlogOrmModel::class, $model);
        $this->assertEquals(1, $model->getId()->getValue());
        $this->assertEquals('my blog 1', $model->getTitle()->getValue());
        $this->assertEquals('UPDATED', $model->getSecondTitle()->getValue());
    }

    public function testUnresolved()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->newUpdate(true, $blog->getId()->eq(1));

        $this->expectException(UnresolvedException::class);
        $rs->affectedRows();
    }

    public function testResolved()
    {
        $blog = new BlogOrmModel();
        /** @var BlogOrmModel $model */
        $model = $blog->find($blog->getId()->eq(1))->first();
        $model->getSecondTitle()->setValue('UPDATED');
        $rs = $model->updateByPk();

        $this->expectException(ResolvedException::class);
        $rs->exec();
    }

    protected function setUp(): void
    {
        AbstractOrmModel::$resultSetFactory = $this->getResultSetFactory();
    }
}
