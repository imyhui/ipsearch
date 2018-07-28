<?php

namespace App\Http\Controllers;

use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IpController extends Controller
{
    private $params;
    private $client;

    public function __construct()
    {
        $this->params = [
            'index' => 'test',
            'type' => 'ip',
        ];
        $this->client = ClientBuilder::create()->build();
    }

    public function search(Request $request)
    {
//        dd($request->ip);
        if (!isset($request->ip)) {
            return response()->json([
                'code' => '1001',
                'message' => '未提交ip'
            ]);
        }

        $this->params['body'] = [
            'query' => [
                'match' => [
                    'ip' => $request->ip//'104.17.210.109'
                ]
            ]
        ];
        //       dd($params);
        $response = $this->client->search($this->params);
        return $response['hits'];
    }

    public function searchByType(Request $request)
    {
        $rules = [
            'type' => 'required',
            'value' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json([
                'code' => 1001,
                'message' => $validator->errors()
            ]);
        $data = [];
        foreach ($rules as $key => $value) {
            $data[$key] = $request->input($key);
        }
        $this->params['body'] = [
            'query' => [
                'match' => [
                    $data['type'] => $data['value']
                ]
            ]
        ];
        $response = $this->client->search($this->params);
        return $response['hits'];
    }

    public function searchIp(Request $request)
    {
        if (isset($request->ip))
            $q = $request->ip;
        else
            $q = null;
        $this->params['body'] = [
            'query' => [
                'match' => [
                    'ip' => $q      // 查询条件
                ]
            ]
        ];
        if ($q != null) {
            $response = $this->client->search($this->params);
//            dd($response);
            $res = $response; //$response['hits']['hits'][0]['_source']['data']['http']; //对输出数据过滤
        }
//        dd($res);
        return view('search', compact('res', 'q'));
    }

    public function advancedSearchTest(Request $request)
    {

        $this->params['body'] = [
            'query' => [
//                'wildcard' => [
//                    'ip' => "104.*?"
//                ]
//                'regexp' => [
//                    'ip' => "104.17.210.109.*?"
////                     正则模糊匹配
//                ],
//                'regexp' => [
//                    'data.http.response.status_line' => ".*?403.*?"
//                ]
//                'match' => [
//                    'data.http.response.status_code' => 200
//                ],
//                'regexp' => [
////                    'data.http.response.protocol.name' => "HTTP"
//                    'data.http.response.headers.server' => "nginx.*?"
//                ]
                'regexp' => [
//                    'data.http.response.headers.server' => "nginx.*?"
                    'data.http.response.body' => "github.*?"
                ]
//                'bool' => [
//                    'must' => [
////                        [ 'match' => [ 'timestamp' => '2017-11-21T02:30:28-05:00' ] ],
////                        [ 'terms' => [ 'timestamp' => ['2017-11-21T02:30:28-05:00'] ] ],
//
////                        [ 'match' => [ 'ip' => '104.17.210.109' ] ],
//                        [ 'range' => [ 'timestamp' => ['gte' => '2017-11-21T02:30:28-05:00'] ] ],
//                    ]
//                ]
            ]
        ];
        //       dd($params);

        $res = $this->client->search($this->params);
        return response()->json([
            'data' => $res
        ]);
    }

    public function advancedSearch(Request $request)
    {
        $res = [];
        if (isset($request->ip))
            array_push($res, [
                'regexp' => [
                    'ip' => ($request->ip) . ".*?"
                ]
            ]);
        if (isset($request->host))
            array_push($res, [
                'regexp' => [
                    'data.http.response.request.host' => ($request->host) . ".*?"
                ]
            ]);
        if (isset($request->servers))
            array_push($res, [
                'regexp' => [
                    'data.http.response.headers.server' => ($request->servers) . ".*?"
                ]
            ]);
        if (isset($request->powered_by))
            array_push($res, [
                'match' => [
                    'data.http.response.headers.x_powered_by' => $request->powered_by
                ]
            ]);
        if (isset($request->body))
            array_push($res, [
                'regexp' => [
                    'data.http.response.body' => ($request->body) . ".*?"
                ]
            ]);
        if (isset($request->code))
            array_push($res, [
                'match' => [
                    'data.http.response.status_code' => $request->code
                ]
            ]);
        if (isset($request->protocol))
            array_push($res, [
                'match' => [
                    'data.http.response.protocol.name' => $request->protocol . ".*?"
                ]
            ]);
        if (isset($request->scheme))
            array_push($res, [
                'match' => [
                    'data.http.response.request.url.scheme' => $request->scheme
                ]
            ]);
        if (isset($request->country))
            array_push($res, [
                'match' => [
                    'data.http.response.request.tls_handshake.server_certificates.certificate.parsed.issuer.country' => $request->country
                ]
            ]);
        if (isset($request->locality))
            array_push($res, [
                'match' => [
                    'data.http.response.request.tls_handshake.server_certificates.certificate.parsed.issuer.locality' => $request->locality
                ]
            ]);
        if (isset($request->province))
            array_push($res, [
                'match' => [
                    'data.http.response.request.tls_handshake.server_certificates.certificate.parsed.issuer.province' => $request->province
                ]
            ]);
        if (isset($request->organization))
            array_push($res, [
                'match' => [
                    'data.http.response.request.tls_handshake.server_certificates.certificate.parsed.issuer.organization' => $request->organization
                ]
            ]);

        $this->params['body'] = [
            'query' => [
                'bool' => [
                    'should' => $res
                ]
            ]
        ];

        $res = $this->client->search($this->params);
        return $res['hits'];
    }
}
