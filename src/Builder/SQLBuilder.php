<?php

namespace Pkg6\DBALW\Builder;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Pkg6\DBALW\Contracts\DBALInterface;

class SQLBuilder
{
    /**
     * 对比1个数据库同一个的表索引，执行sourceBuilders或targetBuilders中的sql保持一致
     * @param DBALInterface $dbal
     * @param string $sourceTable
     * @param string $targetTable
     * @return DiffData
     * @throws \Doctrine\DBAL\Exception
     */
    public static function diffIndexBySameDB(DBALInterface $dbal, string $sourceTable, string $targetTable)
    {
        $source      = $dbal->getConnection()->createSchemaManager()->introspectTable($sourceTable);
        $target      = $dbal->getConnection()->createSchemaManager()->introspectTable($targetTable);
        $sourceIndex = $source->getIndexes();
        $targetIndex = $target->getIndexes();
        return self::diffIndexBuilders($sourceTable, $sourceIndex, $targetTable, $targetIndex);
    }

    /**
     * 对比2个数据库相同的表索引，执行sourceBuilders或targetBuilders中的sql保持一致
     * @param DBALInterface $sourceDBAL
     * @param DBALInterface $targetDBAL
     * @param string $table
     * @return DiffData
     * @throws \Doctrine\DBAL\Exception
     */
    public static function diffIndexBySameTable(DBALInterface $sourceDBAL, DBALInterface $targetDBAL, string $table)
    {
        $source      = $sourceDBAL->getConnection()->createSchemaManager()->introspectTable($table);
        $target      = $targetDBAL->getConnection()->createSchemaManager()->introspectTable($table);
        $sourceIndex = $source->getIndexes();
        $targetIndex = $target->getIndexes();
        return self::diffIndexBuilders($table, $sourceIndex, $table, $targetIndex);
    }

    /**
     * @param string $sourceTable
     * @param Index[] $sourceIndex
     * @param string $targetTable
     * @param Index[] $targetIndex
     * @return DiffData
     */
    protected static function diffIndexBuilders($sourceTable, $sourceIndex, $targetTable, $targetIndex)
    {
        $addIndexes     = array_diff(array_keys($targetIndex), array_keys($sourceIndex));
        $removeIndexes  = array_diff(array_keys($sourceIndex), array_keys($targetIndex));
        $sourceBuilders = [];
        $targetBuilders = [];
        foreach ($addIndexes as $indexName) {
            $sourceBuilders[] = SQLBuilder::addIndex($targetIndex[$indexName], $sourceTable, $indexName);
            $targetBuilders[] = SQLBuilder::deleteIndex($targetTable, $indexName);
        }
        foreach ($removeIndexes as $indexName) {
            $sourceBuilders[] = SQLBuilder::deleteIndex($sourceTable, $indexName);
            $targetBuilders[] = SQLBuilder::addIndex($sourceIndex[$indexName], $targetTable, $indexName);
        }
        return new DiffData($sourceBuilders, $targetBuilders);

    }

    /**
     * 给表添加索引
     * @param Index $index
     * @param string $tableName
     * @param string $indexName
     * @return IndexBuilder
     */
    public static function addIndex(Index $index, string $tableName, string $indexName)
    {
        return new IndexBuilder($tableName, $indexName, false, $index);
    }

    /**
     * 给表删除索引
     * @param string $tableName
     * @param string $indexName
     * @return IndexBuilder
     */
    public static function deleteIndex(string $tableName, string $indexName)
    {
        return new IndexBuilder($tableName, $indexName);
    }

    /**
     * 对比相同数据库相同的表结构，执行sourceBuilders或targetBuilders中的sql保持一致
     * @param DBALInterface $dbal
     * @param $sourceTable
     * @param $targetTable
     * @return DiffData
     * @throws \Doctrine\DBAL\Exception
     */
    public static function diffFieldBySameDB(DBALInterface $dbal, $sourceTable, $targetTable)
    {
        $platform      = $dbal->getConnection()->getDatabasePlatform();
        $source        = $dbal->getConnection()->createSchemaManager()->introspectTable($sourceTable);
        $target        = $dbal->getConnection()->createSchemaManager()->introspectTable($targetTable);
        $sourceColumns = $source->getColumns();
        $targetColumns = $target->getColumns();
        return self::diffFieldBuilders($sourceTable, $sourceColumns, $targetTable, $targetColumns, $platform);
    }

    /**
     * 对比2个数据库相同的表结构，执行sourceBuilders或targetBuilders中的sql保持一致
     * @param DBALInterface $sourceDBAL
     * @param DBALInterface $targetDBAL
     * @param string $tableName
     * @return DiffData
     * @throws \Doctrine\DBAL\Exception
     */
    public static function diffFieldBySameTable(DBALInterface $sourceDBAL, DBALInterface $targetDBAL, $tableName)
    {
        $platform      = $sourceDBAL->getConnection()->getDatabasePlatform();
        $source        = $sourceDBAL->getConnection()->createSchemaManager()->introspectTable($tableName);
        $target        = $targetDBAL->getConnection()->createSchemaManager()->introspectTable($tableName);
        $sourceColumns = $source->getColumns();
        $targetColumns = $target->getColumns();
        return self::diffFieldBuilders($tableName, $sourceColumns, $tableName, $targetColumns, $platform);
    }

    /**
     * @param string $sourceTable
     * @param Column[] $sourceColumns
     * @param string $targetTable
     * @param Column[] $targetColumns
     * @param AbstractPlatform $platform
     * @return DiffData
     */
    protected static function diffFieldBuilders($sourceTable, $sourceColumns, $targetTable, $targetColumns, AbstractPlatform $platform)
    {
        $addColumns     = array_diff(array_keys($sourceColumns), array_keys($targetColumns));
        $removeColumns  = array_diff(array_keys($targetColumns), array_keys($sourceColumns));
        $sourceBuilders = [];
        $targetBuilders = [];
        foreach ($addColumns as $fieldName) {
            $sourceBuilders[] = SQLBuilder::deleteField($sourceTable, $fieldName);
            $targetBuilders[] = SQLBuilder::addField($sourceColumns[$fieldName], $targetTable, $fieldName, $platform);
        }
        foreach ($removeColumns as $fieldName) {
            $sourceBuilders[] = SQLBuilder::addField($targetColumns[$fieldName], $sourceTable, $fieldName, $platform);
            $targetBuilders[] = SQLBuilder::deleteField($targetTable, $fieldName);
        }
        return new DiffData($sourceBuilders, $targetBuilders);
    }

    /**
     * 添加指定数据表中的字段
     * @param Column $column
     * @param string $tableName
     * @param string $fieldName
     * @param AbstractPlatform $platform
     * @return FieldBuilder
     */
    public static function addField(Column $column, string $tableName, string $fieldName, AbstractPlatform $platform)
    {
        return new FieldBuilder($tableName, $fieldName, false, $column, $platform);
    }

    /**
     * 删除指定数据表中字段
     * @param string $tableName
     * @param string $fieldName
     * @return FieldBuilder
     */
    public static function deleteField(string $tableName, string $fieldName)
    {
        return new FieldBuilder($tableName, $fieldName);
    }

    /**
     * 生成批量数据一条sql插入语句
     * @param string $tableName 'users';
     * @param array $rowData [
     * [
     * 'name' => 'John Doe',
     * 'email' => 'john@example.com'
     * ],
     * [
     * 'name' => 'Jane Doe 2',
     * 'email' => 'jane2@example.com'
     * ]
     * ]
     * @return InsertBuilder
     */
    public function InsertBatchBuilder(string $tableName, array $rowData)
    {
        return new InsertBuilder($tableName, $rowData);
    }

    /**
     * 生成批量数据一条sql插入语句
     * @param string $tableName 'users';
     * @param array $data [
     * 'name' => 'John Doe',
     * 'email' => 'john@example.com'
     * ];
     * @param string $method
     * @return InsertBuilder
     * @see InsertBuilder::METHODREPLACEINTO
     * @see InsertBuilder::METHODINSERTINTO
     */
    public static function InsertBuilder(string $tableName, array $data, $method = InsertBuilder::METHODINSERTINTO)
    {
        return new InsertBuilder($tableName, $data, $method);
    }
}