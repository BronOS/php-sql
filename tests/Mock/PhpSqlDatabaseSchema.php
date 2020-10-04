<?php

namespace BronOS\PhpSql\Tests\Mock;


use BronOS\PhpSql\Database\AbstractDatabase;

class PhpSqlDatabaseSchema extends AbstractDatabase
{
    protected static string $databaseName = 'php-sql';

    protected BlogModel $blogModel;

    public function __construct()
    {
        $this->blogModel = new BlogModel();
    }
}