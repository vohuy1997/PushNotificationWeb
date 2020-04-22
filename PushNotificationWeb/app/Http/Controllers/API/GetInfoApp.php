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
        $fcmToken = $request->get('fcmToken');
        $os = $request->get('os');
        
        $dataReturn = 'fail';
        $status = 404;
        $errorMsg = 'fcm_token fails';

        $serverKey = "AAAAST4QH5Y:APA91bGUy0VnHuUu580KBNvVcWkWym6ZIDG_HyDt5muYgZ1YxqvjDOQWNlxCwcnJEFVwfPULB6YN4FiQONgOmRtc9SJNp14iMrb5cm50kRPdJ_aqPXAJ-9vewSbu8haIMMhWkn7L6mFm";
        $url = 'https://fcm.googleapis.com/fcm/send';
        $resPushNotification = array(
            "title" => $request->get('title'),
            "body" => $request->get('body')
        );
        $data = '';
        if ($os == "1") {
            $data = array(
                "to" => $fcmToken,
                "collapse_key" => "type_a",
                "content_available" => true,
                "priority" => "high",
                "notification" => $resPushNotification
            );
        } else if ($os == "0") {
            $data = array(
                "to" => $fcmToken,
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
        $deviceName = $request->get('deviceName');
        $versionCode = $request->get('versionCode');
        $bundleID = $request->get('bundleID');
        $osType = $request->get('osType');
        $operationSystem = $request->get('operationSystem');
        $versionBuild = $request->get('versionBuild');
        $dataReturn = 'fail';
        $status = 404;
        $errorMsg = 'fcm_token error';
        $serverKey = "AAAAST4QH5Y:APA91bGUy0VnHuUu580KBNvVcWkWym6ZIDG_HyDt5muYgZ1YxqvjDOQWNlxCwcnJEFVwfPULB6YN4FiQONgOmRtc9SJNp14iMrb5cm50kRPdJ_aqPXAJ-9vewSbu8haIMMhWkn7L6mFm";
        $url = 'https://fcm.googleapis.com/fcm/send';
        $resPushNotification = array(
            "title" => $request->get('title'),
            "body" => $request->get('body')
        );

        $responseData['success'] = '';
        $tokenIos = [];
        $tokenAndroid = [];
        $registration_ids_ios = [];
        $registration_ids_android = [];
        $conditionDeviceName = false;
        $conditionVersionCode = false;
        $conditionBundleID = false;
        $conditionVersionBuild = false;
        $conditionOperationSystem = false;

        if ($bundleID != '') {
            $conditionBundleID = true;
        }
        if ($deviceName != '') {
            $conditionDeviceName = true;
        }
        if ($versionCode != '') {
            $conditionVersionCode = true;
        }
        if ($versionBuild != '') {
            $conditionVersionBuild = true;
        } 
        if ($operationSystem != '') {
            $conditionOperationSystem = true;
        } 

        $tokenIos = DB::table('info_app')
        ->select('fcmToken')
        ->where('deviceType','1')
        ->when($conditionDeviceName, function ($query) use ($deviceName) {
            return $query->where('deviceName', $deviceName);
        })
        ->when($conditionVersionCode, function ($query) use ($versionCode) {
            return $query->where('versionCode', $versionCode);
        })
        ->when($conditionBundleID, function ($query) use ($bundleID) {
            return $query->where('bundleID', $bundleID);
        })
        ->when($conditionVersionBuild, function ($query) use ($versionBuild) {
            return $query->where('versionBuild', $versionBuild);
        })
        ->when($conditionOperationSystem, function ($query) use ($operationSystem) {
            return $query->where('operationSystem', $operationSystem);
        })
        ->whereNotNull('fcmToken')
        ->get();

        $tokenAndroid = DB::table('info_app')
        ->select('fcmToken')
        ->where('deviceType','0')
        ->when($conditionDeviceName, function ($query) use ($deviceName) {
            return $query->where('deviceName', $deviceName);
        })
        ->when($conditionVersionCode, function ($query) use ($versionCode) {
            return $query->where('versionCode', $versionCode);
        })
        ->when($conditionBundleID, function ($query) use ($bundleID) {
            return $query->where('bundleID', $bundleID);
        })
        ->when($conditionVersionBuild, function ($query) use ($versionBuild) {
            return $query->where('versionBuild', $versionBuild);
        })
        ->when($conditionOperationSystem, function ($query) use ($operationSystem) {
            return $query->where('operationSystem', $operationSystem);
        })
        ->whereNotNull('fcmToken')
        ->get();

        if (!$tokenIos->isEmpty()){
            for ($i=0; $i < count($tokenIos); $i++) { 
                if(!is_null($tokenIos[$i]->fcmToken)){
                    foreach ($tokenIos[$i] as $token) {
                        array_push($registration_ids_ios, $token);
                    }
                }
                
            }
        }

        if (!$tokenAndroid->isEmpty()){
            for ($i=0; $i < count($tokenAndroid); $i++) { 
                if(!is_null($tokenAndroid[$i]->fcmToken)){
                    foreach ($tokenAndroid[$i] as $token) {
                        array_push($registration_ids_android, $token);
                    }
                }
                
            }
        }

        $data = '';
        if ($registration_ids_ios != null && ($osType == '1' || $osType == '')) {
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
        if ($registration_ids_android != null && ($osType == '0' || $osType == '')) {
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
        }
        else if ($osType != '1' && $osType != '' && $osType != '0') {
            $status = 501;
            $errorMsg = 'osType error';
        }
        else if ($conditionBundleID
            || $conditionOperationSystem
            || $conditionVersionBuild
            || $conditionVersionCode
            || $conditionDeviceName) {
            $status = 500;
            $errorMsg = 'device not found';
        }
        else {
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

}
