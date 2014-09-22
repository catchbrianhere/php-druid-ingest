<?php

namespace PhpDruidIngest\QueryParameters;

use DruidFamiliar\Abstracts\AbstractTaskParameters;
use DruidFamiliar\Exception\MissingParametersException;
use DruidFamiliar\Interfaces\IDruidQueryParameters;

/**
 * Class IndexTaskQueryParameters represents parameter values for an indexing task for Druid.
 *
 * @package PhpDruidIngest
 */
class IndexTaskQueryParameters extends AbstractTaskParameters implements IDruidQueryParameters
{

    /**
     * ISO Time String of Batch Ingestion Window Start Time
     *
     * @var string
     */
    public $intervalStart;


    /**
     * ISO Time String of Batch Ingestion Window End Time
     *
     * @var string
     */
    public $intervalEnd;


    /**
     * @var string
     */
    public $granularityType = 'uniform';


    /**
     * @var string
     */
    public $granularity = 'DAY';


    /**
     * DataSource Name
     *
     * @var string
     */
    public $dataSource;


    /**
     * Path for ingestion. Keep in mind the coordinator and historical nodes will need to be able to access this!
     *
     * Intended to be set through $this->setFilePath(...).
     *
     * @var string
     */
    public $baseDir;


    /**
     * Filename for ingestion. Keep in mind the coordinator and historical nodes will need to be able to access this!
     *
     * Intended to be set through $this->setFilePath(...).
     *
     * @var string
     */
    public $filePath;


    /**
     * Format of ingestion.
     *
     * @var string
     */
    public $format = 'json';


    /**
     * The data's time dimension key.
     *
     * @var string
     */
    public $timeDimension;


    /**
     * Array of strings representing the data's non-time dimensions' keys.
     *
     * @var array
     */
    public $dimensions;


    /**
     * Array of json encoded strings
     *
     * Intended to be set through $this->setAggregators(...).
     *
     * @var array
     */
    public $aggregators = array();


    public function setFilePath($path) {
        $fileInfo = pathinfo($path);
        $this->baseDir = $fileInfo['dirname'];
        $this->filePath = $fileInfo['basename'];
    }

    /**
     * Configure the aggregators for this request.
     *
     * @param $aggregatorsArray PHP Array of aggregators
     */
    public function setAggregators($aggregatorsArray)
    {
        $this->aggregators = array();

        foreach( $aggregatorsArray as $key => $val)
        {
            $this->aggregators[] = json_encode( $val );
        }

    }


    /**
     * @throws MissingParametersException
     */
    public function validate()
    {
        // Validate missing params
        $missingParams = array();

        if ( !isset( $this->queryType       ) ) { $missingParams[] = 'queryType';       }
        if ( !isset( $this->dataSource      ) ) { $missingParams[] = 'dataSource';      }
        if ( !isset( $this->intervalStart   ) ) { $missingParams[] = 'intervalStart';   }
        if ( !isset( $this->intervalEnd     ) ) { $missingParams[] = 'intervalEnd';     }
        if ( !isset( $this->granularity     ) ) { $missingParams[] = 'granularity';     }
        if ( !isset( $this->dimensions      ) ) { $missingParams[] = 'dimensions';      }
        if ( !isset( $this->aggregators     ) ) { $missingParams[] = 'aggregators';     }

        if ( count($missingParams) > 0 ) {
            throw new \DruidFamiliar\Exception\MissingParametersException($missingParams);
        }



        // Validate empty params
        $emptyParams = array();

        if ( $this->queryType === '' ) { $emptyParams[] = 'queryType'; }
        if ( $this->dataSource === '' ) { $emptyParams[] = 'dataSource'; }

        if ( count($emptyParams) > 0 ) {
            throw new \DruidFamiliar\Exception\MissingParametersException($missingParams);
        }
    }
}