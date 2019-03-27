<?php
/**
 * Created by PhpStorm.
 * User: imyhui
 * Date: 2019/3/27
 * Time: 上午11:47
 */

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Elasticsearch\ClientBuilder;


class SearchService
{
    private $params;
    private $client;

    public function __construct()
    {
        $this->params = [
            'index' => config('database.es.index'),
            'type' => config('database.es.type'),
        ];
        $this->client = ClientBuilder::create()->build();
    }

    public function getIpDataRedis($ip)
    {
        $data = Redis::get($ip);
        return $data ? unserialize($data) : null;
    }

    public function setIpDataRedis($ip, $ipData)
    {
        Redis::setex($ip, 12 * 3600, serialize($ipData));
    }

    public function searchByIp($ip)
    {
        $data = $this->getIpDataRedis($ip);
        if ($data != null) {
            return $data;
        }

        $this->params['body'] = [
            'query' => [
                'match' => [
                    'ip' => $ip
                ]
            ]
        ];

        $ipData = $this->client->search($this->params)['hits'];

        if ($ipData['total'] != 0) {
            $this->setIpDataRedis($ip, $ipData);
        }
        return $ipData;
    }

    public function searchSingleType($type, $value)
    {
        $this->params['body'] = [
            'query' => [
                'match' => [
                    $type => $value
                ]
            ]
        ];
        $ipData = $this->client->search($this->params)['hits'];
        return $ipData;
    }

    public function searchTypes($query)
    {
        $queryData = [];
        if (isset($query['ip']))
            array_push($queryData, [
                'regexp' => [
                    'ip' => ($query['ip']) . ".*?"
                ]
            ]);
        if (isset($query['host']))
            array_push($queryData, [
                'regexp' => [
                    'data.http.response.request.host' => ($query['host']) . ".*?"
                ]
            ]);
        if (isset($query['servers']))
            array_push($queryData, [
                'match' => [
                    'data.http.response.headers.server' => $query['servers']
                ]
            ]);
        if (isset($query['powered_by']))
            array_push($queryData, [
                'match' => [
                    'data.http.response.headers.x_powered_by' => $query['powered_by']
                ]
            ]);
        if (isset($query['body']))
            array_push($queryData, [
                'regexp' => [
                    'data.http.response.body' => ($query['body']) . ".*?"
                ]
            ]);
        if (isset($query['code']))
            array_push($queryData, [
                'match' => [
                    'data.http.response.status_code' => $query['code']
                ]
            ]);
        if (isset($query['protocol']))
            array_push($queryData, [
                'match' => [
                    'data.http.response.protocol.name' => $query['protocol'] . ".*?"
                ]
            ]);
        if (isset($query['scheme']))
            array_push($queryData, [
                'match' => [
                    'data.http.response.request.url.scheme' => $query['scheme']
                ]
            ]);
        if (isset($query['country']))
            array_push($queryData, [
                'match' => [
                    'data.http.response.request.tls_handshake.server_certificates.certificate.parsed.issuer.country' => $query['country']
                ]
            ]);
        if (isset($query['locality']))
            array_push($queryData, [
                'match' => [
                    'data.http.response.request.tls_handshake.server_certificates.certificate.parsed.issuer.locality' => $query['locality']
                ]
            ]);
        if (isset($query['province']))
            array_push($queryData, [
                'match' => [
                    'data.http.response.request.tls_handshake.server_certificates.certificate.parsed.issuer.province' => $query['province']
                ]
            ]);
        if (isset($query['organization']))
            array_push($queryData, [
                'match' => [
                    'data.http.response.request.tls_handshake.server_certificates.certificate.parsed.issuer.organization' => $query['organization']
                ]
            ]);

        $this->params['body'] = [
            'query' => [
                'bool' => [
                    'should' => $queryData
                ]
            ]
        ];

        $page = $query['page'] ?? 0;
        if ($page > 0) {
            $this->params['from'] = ($page - 1) * 10;
        }
        $data = $this->client->search($this->params)['hits'];
        return $data;
    }


}