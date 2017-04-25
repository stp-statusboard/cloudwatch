<?php

namespace StpBoard\Amazon\CloudWatch\Elasticsearch;

use Aws\CloudWatch\CloudWatchClient;
use Exception;
use Silex\Application;
use StpBoard\Amazon\CloudWatch\Elasticsearch\Exception\StatisticsException;
use StpBoard\Amazon\CloudWatch\Elasticsearch\Parameter\Metrics;
use StpBoard\Amazon\CloudWatch\Elasticsearch\Result\StatisticsCollection;

class Statistics
{
    private $cloudWatchClient;

    private function __construct(CloudWatchClient $cloudWatchClient)
    {
        $this->cloudWatchClient = $cloudWatchClient;
    }

    public static function fromApplication(Application $application): self
    {
        return new Statistics(
            CloudWatchClientFactory::fromApplication($application)
        );
    }

    /**
     * @throws StatisticsException
     */
    public function get(Metrics $metrics): StatisticsCollection
    {
        try {
            return StatisticsCollection::fromAwsResult(
                $this->cloudWatchClient->getMetricStatistics($metrics->toArray())
            );
        } catch (Exception $exception) {
            throw StatisticsException::fromPrevious($exception);
        }
    }
}
