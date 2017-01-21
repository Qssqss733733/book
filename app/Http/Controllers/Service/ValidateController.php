<?php

namespace App\Http\Controllers\Service;

use App\Tool\Validate\Validate;
use App\Tool\SMS\SendTemplateSMS;
use App\Models\M3Result;
use App\Entity\TempPhone;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ValidateController extends Controller
{
    //验证码
    public function create(){
       $valida = new Validate();
        return $valida->doimg();
    }
    //发送手机验证码
    public function sendSMS(Request $request){
        $m3_result = new M3Result;
        $phone = $request->input('phone','');
        if($phone == ''){
            $m3_result->status = 1;
            $m3_result->message = '手机号不能为空';
            return $m3_result->toJson();
        }
        $SendTemplateSMS = new sendTemplateSMS;
        $code = '';
        $charset = '1234567890';
        $_len = strlen($charset) - 1;
        for ($i = 0;$i <6;++$i) {
            $code .= $charset[mt_rand(0, $_len)];
        }
        $m3_result = $SendTemplateSMS->sendTemplateSMS("$phone", array($code, 60), 1);
        if($m3_result->status == 0){
            $TempPhone = new TempPhone;
            $TempPhone->phone = $phone;
            $TempPhone->code = $code;
            $TempPhone->deadline = date('Y-m-d H-i-s',time() + 60*60);
            $TempPhone->save();
        };
        return $m3_result->toJson();

    }

}
