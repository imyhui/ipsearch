<?php

namespace App\Http\Controllers;

use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

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
        if(!isset($request->ip)){
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
        return  $response['hits'];
    }

    public function searchIp(Request $request)
    {
        if(isset($request->ip))
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
        if($q!=null)
        {
            $response = $this->client->search($this->params);
//            dd($response);
            $res = $response; //$response['hits']['hits'][0]['_source']['data']['http']; //对输出数据过滤
        }
//        dd($res);
        return view('search', compact('res','q'));
    }
}
