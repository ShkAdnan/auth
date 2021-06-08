<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function registerUser(Request $request){

        $validator = \Validator::make($request->all(),[  
            'user_name'        => 'required|min:2|max:5',
            'email'            => 'required|email',
            'password'         => 'required',
            'user_role'        => 'required'
        ]);
    
        if ($validator->fails()) {
            return $validator->errors();
        }

        $pin = rand(100000,500000);

        $user = User::create([
            'user_name'     => $request->user_name,
            'email'         => $request->email,
            'avatar'        => $request->avatar,
            'password'      => Hash::make($request->password),
            'user_role'     => $request->user_role,
            'register_at'   => Carbon::now()->toDateTimeString(),
            'pin'           => $pin,
            'active'        => 0
        ]);

        return response()->json([
            'status'        => 'success',
            'statusMessage' => 'Register',
            'httpCode'      => '200',
            'errorCode'     => '',
            'response'      => 'Please check your email and verify pin'
        ], 200);
    }

    public function loginUser(Request $request){
        $validator = \Validator::make($request->all(),[ 
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $count= User::where('email' , $request->email)->where('active', '1')->get();
        if($count->count() > 0){
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
      
                return response()->json([
                    'status'        => 'success',
                    'statusMessage' => 'Login Successfull',
                    'httpCode'      => '200',
                    'errorCode'     => '',
                    'response'      => ""
                ], 200);
             }
        }else{
            return response()->json([
                'status'        => 'error',
                'statusMessage' => 'Account not verified',
                'httpCode'      => '422',
                'errorCode'     => '',
                'response'      => ""
            ], 200);
        }
        
    }
    public function verifyUser(Request $request){
        $validator = \Validator::make($request->all(),[ 
            'email'     => 'required|email',
            'pin'  => 'required'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        User::where('email' , $request->email)
        ->where('pin' , $request->pin)->update([
            'active' => '1'
        ]);

        return response()->json([
            'status'        => 'success',
            'statusMessage' => 'Verified',
            'httpCode'      => '200',
            'errorCode'     => '',
            'response'      => 'Your account is verified please login'
        ], 200);
    }

    public function updateUser(Request $request){
        User::where('id' , $request->id)
        ->where('pin' , $request->pin)->update([
            'user_name'     => $request->user_name,
            'email'         => $request->email,
            'avatar'        => $request->avatar,
            'password'      => Hash::make($request->password),
            'user_role'     => $request->user_role,
        ]);

        return response()->json([
            'status'        => 'success',
            'statusMessage' => 'Information Updated',
            'httpCode'      => '200',
            'errorCode'     => '',
            'response'      => 'You have successfully updated your information'
        ], 200);
    }
    
}
