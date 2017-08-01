<?php

namespace StpBoard\Amazon\CloudWatch\Elasticsearch\Result;

use Aws\Result;
use DateTime;

class StatisticsCollection
{
    private $data;

    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromAwsResult(Result $result, string $statistics): self
    {
        return new self(
            self::extractPointsFromSortedData(
                self::getDataSortedByDateAsc($result),
                $statistics
            )
        );
    }

    public function get(): array
    {
        return $this->data ?? [];
    }

    private static function getDataSortedByDateAsc(Result $result): array
    {
        $dataPoints = $result['Datapoints'] ?? [];

        uasort(
            $dataPoints,
            function ($firstDataPoint, $secondDataPoint) {
                /** @var DateTime $firstDate */
                $firstDate = $firstDataPoint['Timestamp'];
                /** @var DateTime $secondDate */
                $secondDate = $secondDataPoint['Timestamp'];

                return $firstDate->format('U') <=> $secondDate->format('U');
            }
        );

        return $dataPoints;
    }

    private static function extractPointsFromSortedData(array $sortedData, string $statistics): array
    {
        return array_values(
            array_map(
                function (array $dataPoint) use ($statistics) {
                    /** @var DateTime $dataPointData */
                    $dataPointData = $dataPoint['Timestamp'];

                    return [
                        'x' => $dataPointData->format('U') * 1000,
                        'y' => $dataPoint[$statistics]
                    ];
                },
                $sortedData
            )
        );
    }
}
