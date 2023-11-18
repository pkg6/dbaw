<?php

namespace Pkg6\DBALW\Contracts;


interface SchedulerInterface
{

    /**
     * @param DBALWriterInterface $writer
     * @return $this
     */
    public function setWriter(DBALWriterInterface $writer);

    /**
     * 修复表
     * @return void
     */
    public function repair();

    /**
     * 优化表
     * @return void
     */
    public function optimize();

    /**
     * 迁移结构
     * @return void
     */
    public function migrateStructure();

    /**
     * 迁移所有数据(不包含表结构)
     * @return void
     */
    public function migrateAllData();

    /**
     * 迁移表结构和表数据
     * @return void
     */
    public function migrate();

    /**
     * 恢复备份
     * @return void
     */
    public function restore();
}