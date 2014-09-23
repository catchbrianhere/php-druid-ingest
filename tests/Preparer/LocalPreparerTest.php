<?php

namespace PhpDruidIngest\Preparer;

use PHPUnit_Framework_TestCase;

class LocalPreparerTest extends PHPUnit_Framework_TestCase
{

    public function testWritesWithDelimiter()
    {
        $p = $this->getMock('PhpDruidIngest\Preparer\LocalFilePreparer', array('writeFile', 'delimit', 'getPreparedPath'));
        $p->outputBaseDir = 'some/dir';
        $p->outputFilename = 'filename.json';

        $fakeData = array(
            'row1',
            'row2',
            'row3',
        );

        $p->expects($this->once())->method('getPreparedPath')->willReturn('some/dir/filename.json');
        $p->expects($this->once())->method('delimit')->with( $fakeData )->willReturn( 'row1\nrow2\nrow3' );
        $p->expects($this->once())->method('writeFile')->with( 'some/dir/filename.json', 'row1\nrow2\nrow3' );

        /**
         * @var \PhpDruidIngest\Preparer\LocalFilePreparer $p
         */
        $fakePath = $p->prepare( $fakeData );

        $this->assertEquals('some/dir/filename.json', $fakePath);
    }

    public function testWriteJSON()
    {
        $p = $this->getMock('PhpDruidIngest\Preparer\LocalFilePreparer', array('writeFile'));

        $p->expects($this->once())->method('writeFile')->with( 'some/dir/filename.json', 'row1\nrow2\nrow3' );

        $fakeData = array(
            '{"id": 1,"animal":"cat","legs":4}',
            '{"id": 2,"animal":"cat","legs":3}',
            '{"id": 3,"animal":"dog","legs":4}',
            '{"id": 4,"animal":"spider","legs":8}',
        );

        $p->prepare( $fakeData );

        $this->markTestIncomplete('need to mock io');
    }

    public function testWriteCSV()
    {
        $p = $this->getMock('PhpDruidIngest\Preparer\LocalFilePreparer', array('writeFile'));

        $p->expects($this->once())->method('writeFile')->with( $this->anything(), 'row1\nrow2\nrow3' );

        $fakeData = array(
            'id,animal,legs',
            '1,cat,4',
            '2,cat,3',
            '3,dog,4',
            '4,spider,8',
        );

        $p->prepare( $fakeData );

        $this->markTestIncomplete('need to mock io');

    }

    public function testWriteTSV()
    {
        $p = $this->getMock('PhpDruidIngest\Preparer\LocalFilePreparer', array('writeFile'));

        $p->expects($this->once())->method('writeFile')->with( 'some/dir/filename.json', 'row1\nrow2\nrow3' );

        $fakeData = array(
            'id animal  legs',
            '1  cat 4',
            '2  cat 3',
            '3  dog 4',
            '4  spider  8',
        );

        $p->prepare( $fakeData );

        $this->markTestIncomplete('need to mock io');
    }

    public function testHandlesWritePermissionsGracefully()
    {
        $this->markTestIncomplete();
    }

}