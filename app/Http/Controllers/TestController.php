<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\Invitation;
use App\Mail\Pin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{

    public function __construct(RepeatedRespository $repeatedRespository)
    {
        $this->middleware('auth', ['except' => ['sendEmail' , 'registerUser'] ]);
    
    }

    public function sendEmail(Request $request){
        $validator = \Validator::make($request->all(),[  
            'email'            => 'required|email',  
        ]);
    
        if ($validator->fails()) {
            return $validator->errors();
        }

        $details = [
            'link' => "http://127.0.0.1:8000/api/register"
        ];

        Mail::to($request->email)->send(new Invitation($details));

        return $this->response('success', 'Email Sent', '200', 'Successfully Sent');

    }
    public function registerUser(Request $request){

        $validator = \Validator::make($request->all(),[  
            'user_name'        => 'required|min:2|max:5',
            'email'            => 'required|email',
            'password'         => 'required',
        ]);
    
        if ($validator->fails()) {
            return $validator->errors();
        }

        $pin = rand(100000,500000);

        $user = User::create([
            'user_name'     => $request->user_name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'avatar'        => "",
            'user_role'     => "user",
            'register_at'   => Carbon::now()->toDateTimeString(),
            'pin'           => $pin,
            'active'        => 0
        ]);

        Mail::to($request->email)->send(new Pin($pin));
        return $this->response('success', 'Register Successful', '200', 'Please check your email and verify pin');

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
      
                return $this->response('success', 'Login Successful', '200', 'You are logged in to your account');

             }
        }else{

        return $this->response('error', 'Account not verified', '422', '');

        
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
        return $this->response('success', 'Verified', '200', 'Your account is verified please login');

    }

    public function updateUser(Request $request){


        User::where('id' , $request->id)
        ->where('pin' , $request->pin)->update([
            'user_name'     => $request->user_name,
            'email'         => $request->email,
            'avatar'        => $req->avatar ? $this->avatar($req->image) : "",
            'password'      => Hash::make($request->password),
            'user_role'     => $request->user_role,
        ]);

        return $this->response('success', 'Information Updated', '200', 'You have successfully updated your information');

    }

    public function avatar($image){

        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename = pathinfo($filenameWithExt);
        //get just extension
        $extension = $image->extension();
        $nameToStore = $filename['filename'] . "_".time().".".$extension;
        //Move to folder
        $path = $image->storeAs('uploads/avatar/' ,$nameToStore);
        return $nameToStore;
    }
    public function response($status, $statusMessage, $httpCode, $response){
        return response()->json([
            'status'        => $status,
            'statusMessage' => $statusMessage,
            'httpCode'      => $httpCode,
            'errorCode'     => '',
            'response'      => $response
        ], 200);
    }
    
}
