<?php

namespace StpBoard\Amazon\CloudWatch\Elasticsearch;

use Aws\CloudWatch\CloudWatchClient;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class CloudWatchClientFactory
{
    const VERSION = 'latest';

    public static function fromApplication(Application $application): CloudWatchClient
    {
        /** @var Request $request */
        $request = $application['request'];

        return new CloudWatchClient([
            'version' => self::VERSION,
            'region' => $request->get('region'),
            'credentials' => [
                'key' => $request->get('aws_key'),
                'secret' => $request->get('aws_secret'),
            ],
        ]);
    }
}
