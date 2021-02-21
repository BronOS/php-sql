<?php

namespace BronOS\PhpSql\Tests\Model;


use BronOS\PhpSql\Tests\Mock\BlogModel;
use BronOS\PhpSqlSchema\Column\Numeric\IntColumn;
use BronOS\PhpSqlSchema\Column\String\VarCharColumn;
use BronOS\PhpSqlSchema\SQLTableSchema;
use PHPUnit\Framework\TestCase;


class ModelTest extends TestCase
{
    public function testSchema()
    {
        $model = new BlogModel(['id' => 1, 'title' => 'test']);
        $schema = $model->getSchema();

        $this->assertInstanceOf(SQLTableSchema::class, $schema);

        $this->assertEquals('blog', $model->getSchema()->getName());
        $this->assertEquals(1, $model->getId()->getValue());
        $this->assertEquals('test', $model->getTitle()->getValue());

        $this->assertInstanceOf(IntColumn::class, $schema->getColumn('id'));
        $this->assertInstanceOf(VarCharColumn::class, $schema->getColumn('title'));

        $this->assertFalse($model->isDirty);
        $this->assertFalse($model->getId()->isDirty);
        $this->assertFalse($model->getTitle()->isDirty);

        $model->getId()->setValue(45);
        $this->assertTrue($model->isDirty);
        $this->assertTrue($model->getId()->isDirty);
        $this->assertFalse($model->getTitle()->isDirty);

        $dirtyFields = $model->getDirtyFields();
        $this->assertCount(1, $dirtyFields);
        $this->assertArrayHasKey('id', $dirtyFields);

        $newModel = $model->newFromRow(['id' => 200]);
        $this->assertEquals(45, $model->getId()->getValue());
        $this->assertEquals(200, $newModel->getId()->getValue());
        $this->assertEquals('test', $model->getTitle()->getValue());
        $this->assertNull($newModel->getTitle()->getValue());

        $newList = $model->newFromRows([
            ['id' => 300],
            ['id' => 400],
        ]);
        $this->assertCount(2, $newList);
    }

    public function testDirty()
    {
        $model = new BlogModel();
        $model->getTitle()->setValue('test');

        $this->assertTrue($model->getTitle()->isDirty);

        $dirtyFields = $model->getDirtyFields();

        $this->assertCount(1, $dirtyFields);
        $this->assertArrayHasKey('title', $dirtyFields);
        $this->assertEquals('test', $dirtyFields['title']->getValue());
    }

    public function testNew()
    {
        $model = new BlogModel();

        $nm = $model->new();
        $this->assertInstanceOf(BlogModel::class, $nm);
        $this->assertFalse($model->getTitle()->isDirty);
    }
}
