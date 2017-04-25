<?php

namespace StpBoard\Amazon\CloudWatch\Elasticsearch;

use Silex\Application;
use StpBoard\Amazon\CloudWatch\Elasticsearch\Exception\StatisticsException;
use StpBoard\Amazon\CloudWatch\Elasticsearch\Parameter\DocumentCount;
use StpBoard\Amazon\CloudWatch\Elasticsearch\Result\StatisticsCollection;
use Symfony\Component\HttpFoundation\Request;

class DynamicStatistics
{
    const ACTION_COUNT_PARAMETER_NAME = 'count';

    const ACTION_PARAMETER_NAME = 'action';

    const ACTION_PARAMETER_NOT_SUPPORTED = 'Action parameter is not supported';

    private $statistics;

    private $application;

    public function __construct(Statistics $statistics, Application $application)
    {
        $this->statistics = $statistics;
        $this->application = $application;
    }

    public static function fromApplication(Application $application): self
    {
        return new self(
            Statistics::fromApplication($application),
            $application
        );
    }

    /**
     * @throws StatisticsException
     */
    public function get(): StatisticsCollection
    {
        /** @var Request $request */
        $request = $this->application['request'];

        if ($request->get(self::ACTION_PARAMETER_NAME) == self::ACTION_COUNT_PARAMETER_NAME) {
            return $this->statistics->getDocumentCount(
                DocumentCount::fromApplication($this->application)
            );
        }

        throw StatisticsException::createForNotSupported(self::ACTION_PARAMETER_NOT_SUPPORTED);
    }
}
