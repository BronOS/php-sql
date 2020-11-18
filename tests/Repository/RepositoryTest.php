<?php

namespace BronOS\PhpSql\Tests\Repository;


use Aura\SqlQuery\QueryFactory;
use BronOS\PhpSql\Exception\InsertException;
use BronOS\PhpSql\Exception\NotFoundException;
use BronOS\PhpSql\Repository\Cache\ApcuCacheStorage;
use BronOS\PhpSql\Tests\BaseTestCase;
use BronOS\PhpSql\Tests\Mock\BlogModel;
use BronOS\PhpSql\Tests\Mock\BlogRepository;


class RepositoryTest extends BaseTestCase
{
    public function testExecute()
    {
        $repo = $this->getRepo();

        $id = (int)$repo->executeInsert(
            $repo->getQueryFactory()->newInsert()->cols(['title' => 'UT'])->into('blog')
        );

        $model = $repo->findById($id);

        $this->assertInstanceOf(BlogModel::class, $model);
        $this->assertEquals($id, $model->getId()->getValue());
        $this->assertEquals('UT', $model->getTitle()->getValue());

        $this->assertTrue($repo->delete($id));
    }

    public function testDelete()
    {
        $repo = $this->getRepo();

        $id = (int)$repo->executeInsert(
            $repo->getQueryFactory()->newInsert()->cols(['title' => 'UT'])->into('blog')
        );

        $res = $repo->delete($id);

        $this->assertTrue($res);

        $this->expectException(NotFoundException::class);
        $repo->findById($id);
    }

    public function testCreate()
    {
        $repo = $this->getRepo();
        $model = new BlogModel();
        $model->getTitle()->setValue('Blog 2');
        $id = $repo->create($model);

        $this->assertTrue($id > 0);

        $rModel = $repo->findById($id);

        $this->assertEquals('Blog 2', $rModel->getTitle()->getValue());

        $repo->delete($id);
    }

    public function testUpdate()
    {
        $repo = $this->getRepo();
        $model = $repo->findById(1);

        $model->getTitle()->setValue('Blog UPDATED');

        $this->assertTrue($repo->update($model));

        $rModel = $repo->findById(1, true);

        $this->assertEquals('Blog UPDATED', $rModel->getTitle()->getValue());
    }

    public function testError()
    {
        $repo = $this->getRepo();

        $this->expectException(InsertException::class);
        $repo->executeInsertRaw('INSERT INTO blog (title) VALUES (?, ?)', ['ttt']);
    }

    private function getRepo(): BlogRepository
    {
        return new BlogRepository(
            $this->getPdo(),
            new QueryFactory('mysql'),
            new BlogModel(),
            new ApcuCacheStorage()
        );
    }
}
