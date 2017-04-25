<?php

namespace StpBoard\Amazon\CloudWatch\Elasticsearch;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use StpBoard\Amazon\CloudWatch\Elasticsearch\Exception\StatisticsException;
use StpBoard\Amazon\CloudWatch\Elasticsearch\Parameter\Metrics;
use StpBoard\Base\BoardProviderInterface;
use StpBoard\Base\TwigTrait;
use Symfony\Component\HttpFoundation\Request;

class ControllerProvider implements BoardProviderInterface, ControllerProviderInterface
{
    use TwigTrait;

    const ROUTE_PREFIX = '/amazon/cloudwatch/elasticsearch';

    const CHART_VIEW_TEMPLATE_NAME = 'chart.html.twig';

    const ERROR_VIEW_TEMPLATE_NAME = 'error.html.twig';

    /**
     * @return string
     */
    public static function getRoutePrefix()
    {
        return self::ROUTE_PREFIX;
    }

    /**
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->initTwig(__DIR__ . '/Resources/views');

        $controllers = $app['controllers_factory'];

        /** @var ControllerCollection $controllers */
        $controllers->get(
            '/',
            function (Application $application) {

                try {
                    /** @var Request $request */
                    $request = $application['request'];

                    $statistics = Statistics::fromApplication($application);
                    $metrics = Metrics::fromApplication($application);

                    return $this->twig->render(
                        self::CHART_VIEW_TEMPLATE_NAME,
                        [
                            'metrics' => $metrics,
                            'statistics' => $statistics->get($metrics),
                            'name' => $request->get('name'),
                        ]
                    );
                } catch (StatisticsException $statisticsException) {
                    return $this->twig->render(
                        self::ERROR_VIEW_TEMPLATE_NAME,
                        [
                            'exception' => $statisticsException,
                        ]
                    );
                }
            }
        );

        return $controllers;
    }
}
