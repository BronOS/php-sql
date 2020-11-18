<?php

namespace BronOS\PhpSql\Tests\Mock;


use BronOS\PhpSql\Field\IntField;
use BronOS\PhpSql\Field\VarCharField;
use BronOS\PhpSql\Orm\AbstractOrmModel;
use BronOS\PhpSqlSchema\Exception\ColumnDeclarationException;

class BlogOrmModel extends AbstractOrmModel
{
    protected IntField $id;
    protected VarCharField $title;
    protected VarCharField $secondTitle;
    protected ?string $tableName = 'blog';

    /**
     * UserModel constructor.
     *
     * @param array $row
     *
     * @throws ColumnDeclarationException
     */
    public function __construct(array $row = [])
    {
        $this->id = new IntField($this, $row, 'id', 11, true, true);
        $this->title = new VarCharField($this, $row, 'title', 100, false, '');
        $this->secondTitle = new VarCharField($this, $row, 'second_title', 100, false, '');
    }

    /**
     * @return IntField
     */
    public function getId(): IntField
    {
        return $this->id;
    }

    /**
     * @return VarCharField
     */
    public function getTitle(): VarCharField
    {
        return $this->title;
    }

    /**
     * @return VarCharField
     */
    public function getSecondTitle(): VarCharField
    {
        return $this->secondTitle;
    }
}