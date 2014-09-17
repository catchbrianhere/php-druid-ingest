<?php

namespace PhpDruidIngest;

use PHPUnit_Framework_TestCase;

class SimpleIndexGeneratorTest extends PHPUnit_Framework_TestCase
{
    private $mockDataSourceName = 'my-datasource';

    public function getMockIndexTaskParameters()
    {
        $params = new IndexTaskParameters();

        $params->intervalStart = '1981-01-01T4:20';
        $params->intervalEnd = '2012-03-01T3:00';
        $params->granularityType = 'uniform';
        $params->granularity = 'DAY';
        $params->dataSource = $this->mockDataSourceName;
        $params->format = 'json';
        $params->timeDimension = 'date_dim';
        $params->dimensions = array('one_dim', 'two_dim');

        $params->setFilePath('/another/file/path/to/a/file.bebop');
        $params->setAggregators(array(
            array( 'type' => 'count', 'name' => 'count' ),
            array( 'type' => 'longSum', 'name' => 'total_referral_count', 'fieldName' => 'referral_count' )
        ));


        return $params;
    }

    public function testGenerateIndexReturnsJSONString()
    {
        $generator = new SimpleIndexGenerator();
        $params = $this->getMockIndexTaskParameters();

        $index = $generator->generateIndex( $params );

        $this->assertJson( $index );
        return $index;
    }

    /**
     * @depends testGenerateIndexReturnsJSONString
     */
    public function testGenerateIndexHasIndexType($jsonString)
    {
        $parsedIndex = json_decode( $jsonString, true );

        $this->assertArrayHasKey('type', $parsedIndex);
        $this->assertEquals( 'index', $parsedIndex['type'] );
    }

    /**
     * @depends testGenerateIndexReturnsJSONString
     */
    public function testGenerateIndexUsesDataSource($jsonString)
    {
        $parsedIndex = json_decode( $jsonString, true );

        $this->assertArrayHasKey('type', $parsedIndex);

        $this->assertArrayHasKey('dataSource', $parsedIndex);
        $this->assertEquals( $this->mockDataSourceName, $parsedIndex['dataSource'] );
    }

    public function testGenerateIndexUsesPathFromPreparer()
    {
        $this->markTestIncomplete();
    }

    /**
     * @depends testGenerateIndexReturnsJSONString
     */
    public function testGenerateIndexUsesNonTimeDimensions($jsonString)
    {
        $parsedIndex = json_decode( $jsonString, true );
        $mockParams = $this->getMockIndexTaskParameters();

        $this->assertArrayHasKey( 'firehose', $parsedIndex );
        $this->assertArrayHasKey( 'parser', $parsedIndex['firehose'] );
        $this->assertArrayHasKey( 'data', $parsedIndex['firehose']['parser'] );

        $this->assertArrayHasKey( 'format', $parsedIndex['firehose']['parser']['data'] );
        $this->assertEquals( 'json', $parsedIndex['firehose']['parser']['data']['format'] );

        $this->assertArrayHasKey( 'dimensions', $parsedIndex['firehose']['parser']['data'] );
        $this->assertCount(count($mockParams->dimensions), $parsedIndex['firehose']['parser']['data']['dimensions']);
        $this->assertEquals($mockParams->dimensions, $parsedIndex['firehose']['parser']['data']['dimensions']);
        $this->assertContains( 'one_dim', $parsedIndex['firehose']['parser']['data']['dimensions'] );
        $this->assertContains( 'two_dim', $parsedIndex['firehose']['parser']['data']['dimensions'] );
    }

    /**
     * @depends testGenerateIndexReturnsJSONString
     */
    public function testGenerateIndexUsesTimeDimension($jsonString)
    {
        $parsedIndex = json_decode( $jsonString, true );
        $mockParams = $this->getMockIndexTaskParameters();

        $this->assertArrayHasKey( 'firehose', $parsedIndex );
        $this->assertArrayHasKey( 'parser', $parsedIndex['firehose'] );
        $this->assertArrayHasKey( 'timestampSpec', $parsedIndex['firehose']['parser'] );
        $this->assertArrayHasKey( 'column', $parsedIndex['firehose']['parser']['timestampSpec'] );
        $this->assertEquals( $mockParams->timeDimension, $parsedIndex['firehose']['parser']['timestampSpec']['column'] );
    }

    public function testGenerateIndexUsesFirehose()
    {
        // type should be local
        // baseDir & filter should reflect path to prepared file
        // parser should include json format and all dimension data

        $this->markTestIncomplete();
    }


    public function testGenerateIndexDefinesGranularitySpec()
    {
        // granularitySpec
            // type
            // gran
            // intervals
        $this->markTestIncomplete();
    }

/**
 * Example index task for reference
     *
curl -X 'POST' -H 'Content-Type:application/json' -d @referral_visit-data_indexing-task.json 0.0.0.0:8087/druid/indexer/v1/task
     *
{
"type" : "index",
"dataSource" : "referral-visit-test-data-w-facility-ids",
"granularitySpec" : {
"type" : "uniform",
"gran" : "DAY",
"intervals" : [ "2010/2020" ]
},
"aggregators" : [
        {
            "type" : "count",
            "name" : "count"
        },
        {
            "type": "longSum",
            "name": "total_referral_count",
            "fieldName": "referral_count"
         }
    ],
    "firehose" : {
    "type" : "local",
        "baseDir" : "/home/jhegman",
        "filter" : "all.json",
        "parser" : {
        "timestampSpec" : {
            "column" : "date"
            },
            "data" : {
            "format" : "json",
                "dimensions" : [ "group", "referral_id", "referral_name", "referral_count", "patient_id", "facility_id" ]
            }

        }
    }
}
     *
*/

}