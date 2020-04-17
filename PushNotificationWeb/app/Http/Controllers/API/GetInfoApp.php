<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GetInfoApp extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getInfo(Request $request)
    {
        $serialNumber = $request->get('serialNumber');
        $checkSeriNumber = DB::table('info_app')
            ->where('serialNumber', '=', $serialNumber)
            ->get();
        $dataReturn = 'fail';
        $status = 200;
        $errorMsg = null;
        if ($serialNumber && $checkSeriNumber->isEmpty()) {
            $operationSystem = $request->get('operationSystem');
            $versionCode = $request->get('versionCode');
            $versionBuild = $request->get('versionBuild');
            $deviceType = $request->get('deviceType');
            $bundleID = $request->get('bundleID');
            $deviceName = $request->get('deviceName');
            $fcmToken = $request->get('fcmToken');
            DB::table('info_app')->insert([
                ['deviceName' => $deviceName,
                 'serialNumber' => $serialNumber,
                 'operationSystem' => $operationSystem,
                 'versionCode' => $versionCode,
                 'versionBuild' => $versionBuild,
                 'deviceType' => $deviceType,
                 'bundleID' => $bundleID,
                 'fcmToken' => $fcmToken]
            ]);

            $dataReturn = 'insert info sucess';
            $status = 200;
        } else {
            $dataReturn = 'inserted or serial number empty';
            $status = 404;
            $errorMsg = 'fail';
        }
        return response()->json([
            'result' => 0,
            'now_dt' => date('Y-m-d H:i:s'),
            'data' => $dataReturn,
            'err_cd' => $status,
            'err_msg' => $errorMsg
        ], $status);
    }

    public function updateFCMToken(Request $request)
    {
        $serialNumber = $request->get('serialNumber');
        $fcmToken = $request->get('fcmToken');
        $checkSeriNumber = DB::table('info_app')
            ->where('serialNumber', '=', $serialNumber)
            ->get();
        $dataReturn = 'fail';
        $status = 404;
        $errorMsg = 'device has not been updated on server';

        if ($checkSeriNumber->isNotEmpty()) {
            DB::table('info_app')
                ->where('serialNumber', '=', $serialNumber)
                ->update(['fcmToken' => $fcmToken]);
            $dataReturn = 'update FCM sucess';
            $status = 200;
            $errorMsg = null ;
        }

        return response()->json([
            'result' => 0,
            'now_dt' => date('Y-m-d H:i:s'),
            'data' => $dataReturn,
            'err_cd' => $status,
            'err_msg' => $errorMsg
        ], $status);
    }

}
