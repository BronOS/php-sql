<?php

namespace BronOS\PhpSql\Tests\Orm;


use BronOS\PhpSql\Exception\ResolvedException;
use BronOS\PhpSql\Exception\UnresolvedException;
use BronOS\PhpSql\Orm\AbstractOrmModel;
use BronOS\PhpSql\Tests\Mock\BlogOrmModel;

class SelectResultSetTest extends ResultSetFactoryTest
{
    public function testFirst()
    {
        $blog = new BlogOrmModel();
        /** @var BlogOrmModel $model */
        $model = $blog->find($blog->getId()->eq(1))->first();

        $this->assertInstanceOf(BlogOrmModel::class, $model);
        $this->assertEquals(1, $model->getId()->getValue());
        $this->assertEquals('my blog 1', $model->getTitle()->getValue());
        $this->assertEquals('', $model->getSecondTitle()->getValue());
    }

    public function testAll()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->find();
        $rs->executeRawQuery("INSERT INTO blog (title, second_title) VALUES ('blog2', 'test2')");
        $id = (int)$rs->getPdo()->lastInsertId();

        /** @var BlogOrmModel[] $models */
        $models = $blog->find($blog->getId()->in([1, $id]))->all();

        $this->assertIsArray($models);
        $this->assertCount(2, $models);

        $this->assertInstanceOf(BlogOrmModel::class, $models[0]);
        $this->assertEquals(1, $models[0]->getId()->getValue());
        $this->assertEquals('my blog 1', $models[0]->getTitle()->getValue());
        $this->assertEquals('', $models[0]->getSecondTitle()->getValue());

        $this->assertInstanceOf(BlogOrmModel::class, $models[1]);
        $this->assertEquals($id, $models[1]->getId()->getValue());
        $this->assertEquals('blog2', $models[1]->getTitle()->getValue());
        $this->assertEquals('test2', $models[1]->getSecondTitle()->getValue());
    }

    public function testResultAll()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->find();
        $rs->executeRawQuery("INSERT INTO blog (title, second_title) VALUES ('blog2', 'test2')");
        $id = (int)$rs->getPdo()->lastInsertId();

        $rs = $blog->find($blog->getId()->in([1, $id]));
        $rs->all();

        /** @var BlogOrmModel[] $models */
        $models = $rs->resultAll();

        $this->assertIsArray($models);
        $this->assertCount(2, $models);

        $this->assertInstanceOf(BlogOrmModel::class, $models[0]);
        $this->assertEquals(1, $models[0]->getId()->getValue());
        $this->assertEquals('my blog 1', $models[0]->getTitle()->getValue());
        $this->assertEquals('', $models[0]->getSecondTitle()->getValue());

        $this->assertInstanceOf(BlogOrmModel::class, $models[1]);
        $this->assertEquals($id, $models[1]->getId()->getValue());
        $this->assertEquals('blog2', $models[1]->getTitle()->getValue());
        $this->assertEquals('test2', $models[1]->getSecondTitle()->getValue());
    }

    public function testResultFirst()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->find($blog->getId()->eq(1));
        $rs->first();

        /** @var BlogOrmModel $model */
        $model = $rs->resultFirst();

        $this->assertInstanceOf(BlogOrmModel::class, $model);
        $this->assertEquals(1, $model->getId()->getValue());
        $this->assertEquals('my blog 1', $model->getTitle()->getValue());
        $this->assertEquals('', $model->getSecondTitle()->getValue());
    }

    public function testUnresolved()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->find($blog->getId()->eq(1));

        $this->expectException(UnresolvedException::class);
        $rs->resultFirst();
    }

    public function testResolved()
    {
        $blog = new BlogOrmModel();
        $rs = $blog->find($blog->getId()->eq(1));
        $rs->first();

        $this->expectException(ResolvedException::class);
        $rs->first();
    }

    protected function setUp(): void
    {
        AbstractOrmModel::$resultSetFactory = $this->getResultSetFactory();
    }
}
