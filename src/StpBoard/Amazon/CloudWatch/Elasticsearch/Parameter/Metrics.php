<?php

namespace StpBoard\Amazon\CloudWatch\Elasticsearch\Parameter;

use DateTime;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

final class Metrics
{
    const METRIC_SEARCHABLE_DOCUMENTS = 'SearchableDocuments';
    const ACTION_SEARCHABLE_DOCUMENTS_NAME = 'count';

    const METRIC_FREE_STORAGE_SPACE = 'FreeStorageSpace';
    const ACTION_FREE_STORAGE_SPACE_NAME = 'free_space';

    const METRIC_ACTION_MAPPER = [
        self::ACTION_SEARCHABLE_DOCUMENTS_NAME => self::METRIC_SEARCHABLE_DOCUMENTS,
        self::ACTION_FREE_STORAGE_SPACE_NAME => self::METRIC_FREE_STORAGE_SPACE,
    ];

    const NAMESPACE = 'AWS/ES';

    const UNIT = 'Count';

    const PERIOD = 86400;

    const STATISTICS = ['Average'];

    const ONE_MONTH_AGO_MIDNIGHT = '-1 month midnight';

    const TODAY_MIDNIGHT = 'today midnight';

    private $metricName;

    private $domainName;

    private $clientId;

    private $startDate;

    private $endDate;

    private function __construct(
        string $metricName,
        string $domainName,
        string $clientId,
        DateTime $startDate,
        DateTime $endDate
    ) {
        $this->metricName = $metricName;
        $this->domainName = $domainName;
        $this->clientId = $clientId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public static function fromApplication(Application $application): self
    {
        /** @var Request $request */
        $request = $application['request'];

        return new self(
            self::METRIC_ACTION_MAPPER[$request->get('action')] ?? self::METRIC_SEARCHABLE_DOCUMENTS,
            $request->get('domain_name'),
            $request->get('client_id'),
            new DateTime(self::ONE_MONTH_AGO_MIDNIGHT),
            new DateTime(self::TODAY_MIDNIGHT)
        );
    }

    public function toArray(): array
    {
        return [
            'MetricName' => $this->metricName,
            'Namespace' => self::NAMESPACE,
            'StartTime' => $this->startDate,
            'EndTime' => $this->endDate,
            'Period' => self::PERIOD,
            'Unit' => self::UNIT,
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
}
