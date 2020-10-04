<?php

namespace BronOS\PhpSql\Tests\Repository;



use Aura\SqlQuery\QueryFactory;
use BronOS\PhpSql\Repository\Cache\ApcuCacheStorage;
use BronOS\PhpSql\Tests\BaseTestCase;
use BronOS\PhpSql\Tests\Mock\BlogModel;
use BronOS\PhpSql\Tests\Mock\BlogRepository;

class RepositoryTest extends BaseTestCase
{
    public function testExecute()
    {
        $repo = new BlogRepository(
            $this->getPdo(),
            new QueryFactory('mysql'),
            new BlogModel(),
            new ApcuCacheStorage()
        );

        $model = $repo->findById(1);

        $this->assertInstanceOf(BlogModel::class, $model);
        $this->assertEquals(1, $model->getId()->getValue());
        $this->assertEquals('my blog 1', $model->getTitle()->getValue());
    }
}
