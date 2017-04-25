<?php

namespace StpBoard\Amazon\CloudWatch\Elasticsearch\Parameter;

use DateTime;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

final class Metrics
{
    const METRICS_SEARCHABLE_DOCUMENTS = 'SearchableDocuments';
    const ACTION_SEARCHABLE_DOCUMENTS_NAME = 'count';

    const METRICS_FREE_STORAGE_SPACE = 'FreeStorageSpace';
    const ACTION_FREE_STORAGE_SPACE_NAME = 'free_storage';

    const METRICS_CPU_UTILIZATION = 'CPUUtilization';
    const ACTION_CPU_UTILIZATION_NAME = 'cpu';

    const ACTION_METRICS_MAPPER = [
        self::ACTION_SEARCHABLE_DOCUMENTS_NAME => self::METRICS_SEARCHABLE_DOCUMENTS,
        self::ACTION_FREE_STORAGE_SPACE_NAME => self::METRICS_FREE_STORAGE_SPACE,
        self::ACTION_CPU_UTILIZATION_NAME => self::METRICS_CPU_UTILIZATION,
    ];

    const METRICS_UNIT_MAPPER = [
        self::METRICS_SEARCHABLE_DOCUMENTS => self::UNIT_COUNT,
        self::METRICS_FREE_STORAGE_SPACE => self::UNIT_MEGABYTES,
        self::METRICS_CPU_UTILIZATION => self::UNIT_PERCENT,
    ];

    const UNIT_COUNT = 'Count';
    const UNIT_MEGABYTES = 'Megabytes';
    const UNIT_PERCENT = 'Percent';

    const NAMESPACE = 'AWS/ES';

    const STATISTICS = ['Average'];

    private $metricsName;

    private $domainName;

    private $clientId;

    private $unit;

    private $period;

    private $startDate;

    private $endDate;

    private function __construct(
        string $metricsName,
        string $domainName,
        string $clientId,
        string $unit,
        int $period,
        DateTime $startDate,
        DateTime $endDate
    ) {
        $this->metricsName = $metricsName;
        $this->domainName = $domainName;
        $this->clientId = $clientId;
        $this->unit = $unit;
        $this->period = $period;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public static function fromApplication(Application $application): self
    {
        /** @var Request $request */
        $request = $application['request'];
        $metricsName = self::ACTION_METRICS_MAPPER[$request->get('action')] ?? self::METRICS_SEARCHABLE_DOCUMENTS;
        $metricsUnit = self::METRICS_UNIT_MAPPER[$metricsName];

        return new self(
            $metricsName,
            $request->get('domain_name'),
            $request->get('client_id'),
            $metricsUnit,
            $request->get('period'),
            new DateTime($request->get('start_date')),
            new DateTime($request->get('end_date'))
        );
    }

    public function toArray(): array
    {
        return [
            'MetricName' => $this->metricsName,
            'Namespace' => self::NAMESPACE,
            'StartTime' => $this->startDate,
            'EndTime' => $this->endDate,
            'Period' => $this->period,
            'Unit' => $this->unit,
            'Statistics' => self::STATISTICS,
            'Dimensions' => [
                [
                    'Name' => 'DomainName',
                    'Value' => $this->domainName,
                ],
                [
                    'Name' => 'ClientId',
                    'Value' => $this->clientId,
                ]
            ],
        ];
    }

    public function getUnit(): string
    {
        return $this->unit;
    }
}
