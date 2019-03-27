<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class IpController extends Controller
{
    private $params;
    private $client;
    private $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(Request $request)
    {
        $ip = $request->ip;
        if (!isset($ip)) {
            return response()->json([
                'code' => '1001',
                'message' => '未提交ip'
            ]);
        }
        $ipData = $this->searchService->searchByIp($ip);
        return $ipData;
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
        $ipData = $this->searchService->searchSingleType($request->type, $request->value);
        return $ipData;
    }

    public function searchIp(Request $request)
    {
        $ip = $request->ip;
        if (!isset($ip)) {
            $ipData = null;
        } else {
            $ipData = json_encode($this->searchService->searchByIp($ip));
        }
//        dd($ipData);
        return view('search', compact('ipData', 'ip'));
    }

    /*
    public function advancedSearchTest(Request $request)
    {

        $rules = [];
        $original = $request->rules;
        $isStandard = preg_match('/(|)([^=]+)(=[^|]+)/', $original);
        //todo
        $arrs = explode("|", $original);
        try {
            foreach ($arrs as $arr) {
                if ($arr != null) {
                    $temp = explode("=", $arr);
                    $rules[] = [
                        $temp[0] => $temp[1]
                    ];
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => '1001',
                'message' => '语法错误'
            ]);
        }
        foreach ($rules as $rule) {
            foreach ($rule as $key => $value)
                $request->$key = $value;
        }
        $res = $this->advancedSearch($request);

        return response()->json([
            'code' => 1000,
            'data' => $res
        ]);
    }
    */

    public function advancedSearch(Request $request)
    {
        $ipDatas = $this->searchService->searchTypes($request->all());
        return $ipDatas;
    }
}
