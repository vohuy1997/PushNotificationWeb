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

    public function push(Request $request) {
        $tokenKey = $request->get('tokenKey');
        $os = $request->get('os');
        
        $dataReturn = 'fail';
        $status = 404;
        $errorMsg = 'device has not been updated on server';

        $serverKey = "AAAAST4QH5Y:APA91bGUy0VnHuUu580KBNvVcWkWym6ZIDG_HyDt5muYgZ1YxqvjDOQWNlxCwcnJEFVwfPULB6YN4FiQONgOmRtc9SJNp14iMrb5cm50kRPdJ_aqPXAJ-9vewSbu8haIMMhWkn7L6mFm";
        $url = 'https://fcm.googleapis.com/fcm/send';
        $resPushNotification = array(
            "title" => $request->get('title'),
            "body" => $request->get('body')
        );
        $data = '';
        if ($os == "1") {
            $data = array(
                "to" => $tokenKey,
                "collapse_key" => "type_a",
                "content_available" => true,
                "priority" => "high",
                "notification" => $resPushNotification
            );
        } else if ($os == "0") {
            $data = array(
                "to" => $tokenKey,
                "collapse_key" => "type_a",
                "content_available" => true,
                "priority" => "high",
                "data" => $resPushNotification
            );
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: key='.$serverKey,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($data)
        ));

        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: '  .  curl_error($ch));
        }
        $responseData = json_decode($result, TRUE);
        if ($responseData['success'] == 1) {
            $dataReturn = "success";
            $status = 200;
            $errorMsg = '';
        } else {
            $dataReturn = "fail";
        }

        return response()->json([
            'result' => 0,
            'now_dt' => date('Y-m-d H:i:s'),
            'data' => $dataReturn,
            'err_cd' => $status,
            'err_msg' => $errorMsg
        ], $status);
    }

    public function pushAll(Request $request) {
        $dataReturn = 'fail';
        $status = 404;
        $errorMsg = 'device has not been updated on server';
        /*$s = "AAAAQq6fsPY:APA91bF8ChFGBow9rIDMimlCXjIwBbNB23CJm_kr_xlWcNIRCYkwsP-gbwNarB8WB5oc1aWkOrnx61eWYQndlejqNTCFXvA8ZCFpLhwP9ez8jGRGBFmzNXejMZBJ47h4l1qum3HIGYjg";*/
        $serverKey = "AAAAST4QH5Y:APA91bGUy0VnHuUu580KBNvVcWkWym6ZIDG_HyDt5muYgZ1YxqvjDOQWNlxCwcnJEFVwfPULB6YN4FiQONgOmRtc9SJNp14iMrb5cm50kRPdJ_aqPXAJ-9vewSbu8haIMMhWkn7L6mFm";
        $url = 'https://fcm.googleapis.com/fcm/send';
        $resPushNotification = array(
            "title" => $request->get('title'),
            "body" => $request->get('body')
        );


        $tokenIos = DB::table('info_app')->select('fcmToken')->where('deviceType','1')->get();
        $tokenAndroid = DB::table('info_app')->select('fcmToken')->where('deviceType','0')->get();

        $registration_ids_ios = [];
        for ($i=0; $i < count($tokenIos); $i++) { 
            foreach ($tokenAndroid[$i] as $token) {
                array_push($registration_ids_ios, $token);
            }
        }

        $registration_ids_android = [];
        for ($i=0; $i < count($tokenAndroid); $i++) { 
            foreach ($tokenAndroid[$i] as $token) {
                array_push($registration_ids_android, $token);
            }
        }

        $data = '';
        if (!$tokenIos->isEmpty()) {
            $data = array(
                "collapse_key" => "type_a",
                "content_available" => true,
                "priority" => "high",
                "notification" => $resPushNotification,
                "registration_ids" => $registration_ids_ios
            );
            $ch = curl_init($url);
            curl_setopt_array($ch, array(
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: key='.$serverKey,
                    'Content-Type: application/json'
                ),
                CURLOPT_POSTFIELDS => json_encode($data)
            ));

            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('FCM Send Error: '  .  curl_error($ch));
            }
            $responseData = json_decode($result, TRUE);
        }
        if (!$tokenAndroid->isEmpty()) {
            $data = array(
                "collapse_key" => "type_a",
                "content_available" => true,
                "priority" => "high",
                "data" => $resPushNotification,
                "registration_ids" => $registration_ids_android
            );
            $ch = curl_init($url);
            curl_setopt_array($ch, array(
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: key='.$serverKey,
                    'Content-Type: application/json'
                ),
                CURLOPT_POSTFIELDS => json_encode($data)
            ));

            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('FCM Send Error: '  .  curl_error($ch));
            }
            $responseData = json_decode($result, TRUE);

        }
        
        if ($responseData['success'] != 0) {
            $dataReturn = "success";
            $status = 200;
            $errorMsg = '';
        } else {
            $dataReturn = "fail";
        }

        return response()->json([
            'result' => 0,
            'now_dt' => date('Y-m-d H:i:s'),
            'data' => $responseData,
            'err_cd' => $status,
            'err_msg' => $errorMsg
        ], $status);
    }

}
