<?php

namespace StpBoard\Amazon\CloudWatch\Elasticsearch\Parameter;

use DateTime;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

final class DocumentCount
{
    const METRIC_NAME = 'SearchableDocuments';

    const NAMESPACE = 'AWS/ES';

    const PERIOD = 86400;

    const UNIT = 'Count';

    const STATISTICS = ['Average'];

    const ONE_MONTH_AGO_MIDNIGHT = '-1 month midnight';

    const TODAY_MIDNIGHT = 'today midnight';

    private $domainName;

    private $clientId;

    private $startDate;

    private $endDate;

    private function __construct(string $domainName, string $clientId, DateTime $startDate, DateTime $endDate)
    {
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
            $request->get('domain_name'),
            $request->get('client_id'),
            new DateTime(self::ONE_MONTH_AGO_MIDNIGHT),
            new DateTime(self::TODAY_MIDNIGHT)
        );
    }

    public function get(): array
    {
        return [
            'MetricName' => self::METRIC_NAME,
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
