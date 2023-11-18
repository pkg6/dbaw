<?php

namespace Pkg6\DBALW\Tests\Support;

use PHPUnit\Framework\TestCase;
use Pkg6\DBALW\Exceptions\FieldDataException;
use Pkg6\DBALW\Support\Arr;

class ArrTest extends TestCase
{
    public function ignoreFieldDataProvider()
    {
        return [
            [['id' => 1, 'name' => 'aaa'], ['id'], ['name' => 'aaa']],
            [[
                ['id' => 1, 'name' => 'aaa'],
                ['id' => 2, 'name' => 'bbb']
            ], ['id'], [
                ['name' => 'aaa'],
                ['name' => 'bbb']
            ]],
            [['id' => 1, 'name' => 'aaa'], [], ['id' => 1, 'name' => 'aaa']],
        ];
    }

    /**
     * @dataProvider ignoreFieldDataProvider
     * @return void
     */
    public function testIgnoreField($data, $fields, $expected)
    {
        $result = Arr::ignoreField($data, $fields);
        $this->assertEquals($expected, $result);
    }

    public function testFieldData()
    {

        // 测试一维数组
        $fields   = ['id', 'name'];
        $data     = ['id' => 1, 'name' => 'aaa'];
        $expected = ['id' => 1, 'name' => 'aaa'];

        $result = Arr::fieldsData($fields, $data);

        $this->assertEquals($expected, $result);

        // 测试二维数组
        $fields = ['id', 'name'];
        $data   = [
            ['id' => 1, 'name' => 'aaa'],
            ['id' => 2, 'name' => 'bbb']
        ];

        $expected = [
            ['id' => 1, 'name' => 'aaa'],
            ['id' => 2, 'name' => 'bbb']
        ];
        $result   = Arr::fieldsData($fields, $data);
        $this->assertEquals($expected, $result);
        // 测试fields包含不存在的字段
        $fields = ['id', 'not_exists'];
        $data   = ['id' => 1, 'name' => 'aaa'];

        $this->expectException(FieldDataException::class);

        Arr::fieldsData($fields, $data);
        // 测试默认异常
        $fields = ['id'];
        $data   = [];
        $this->expectException(FieldDataException::class);
        Arr::fieldsData($fields, $data);
    }

    public function getArrayDimensionsDataProvider()
    {
        return [
            [[], 0],
            [[1, 2, 3], 1],
            [[[1, 2], [3, 4]], 2],
            [[[1, 2], [3, [4, 5]]], 3],
            ["string", 0],
        ];
    }

    /**
     * @dataProvider getArrayDimensionsDataProvider
     * @return void
     */
    public function testGetArrayDimensions($array, $expected)
    {
        $result = Arr::getArrayDimensions($array);
        $this->assertEquals($expected, $result);
    }


}