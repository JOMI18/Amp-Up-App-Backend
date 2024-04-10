<?php

namespace App\Http\Controllers;

use App\Models\Otps;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\Utilities;

class AuthController extends Controller
{
    use Utilities;
    public $rand;


    public function login(Request $request)
    {
        // sleep(5);
        // return response()->json($request->all());
        // return response()->json(['data' => 'Hello world']);
        $valid = Validator::make($request->all(), [
            //they have to be the exact name they;re sent from the frontend
            'email_or_phone' => 'required',
            'password' => 'required',
            'device_model' => 'required',
            'device_id' => 'required',
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()->first()]);
        }

        $user = User::where([['email', $request->email_or_phone], ['status', 1]])->orWhere([['phone', $request->email_or_phone]], [['status', 1]])->first();
        // the 1 here controls activity by admin
        if (!$user || !Hash::check($request->password, $user->password)) {

            return response()->json(['status' => 'error', 'message' => 'The provided credentials are incorrect', 'otp' => false,]);
            // return response()->json(['status' => 'error', 'message' => 'The provided credentials are incorrect',]);
        }
        $user->tokens()->delete();

        // if (!$user->bvn) {
        //     $this->sendOtp(($user->id));
        //     return response()->json(['otp' => false, 'bvn' => true, 'status' => 'error', 'message' => 'We need  to verify your BVN as directed by CBN', 'user' => $user, "token" => $user->createToken($request->device_model)->plainTextToken]);
        // }

        if (!$user->email_verified_at) {
            $this->sendOtp($user->id);
            return response()->json(['otp' => true, 'status' => 'error', 'message' => 'Your email has not been verified. please verify it now', 'user' => $user, "token" => $user->createToken($request->device_model)->plainTextToken]);
        }

        if ($user->device_id != $request->device_id) {
            $this->sendOtp($user->id);
            return response()->json(['otp' => true, 'status' => 'error', 'message' => 'Your account is active on another device, Verification needed to use it here.', 'user' => $user, "token" => $user->createToken($request->device_model)->plainTextToken]);
        }

        return response()->json(['otp' => false, 'status' => 'ok', 'user' => $user, "token" => $user->createToken($request->device_model)->plainTextToken]);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'string|required|min:2',
            'last_name' => 'string|required|min:2',
            'gender' => 'string|required',
            'date_of_birth' => 'required|date',
            'username' => 'required|string|unique:users|min:4',
            'email' => 'string|required|email|unique:users',
            'phone' => 'required|unique:users|min:11|max:15',
            'password' => ['required', 'confirmed', 'digits:6', 'numeric', function ($attribute, $value, $fail) {

                if (
                    preg_match('/(\d)\1{2,}/', $value) || preg_match('/123|234|345|456|567|678|789|098|987|876|765|654|543|432|321|012/', $value)
                ) {
                    $fail('The password is too simple.');
                }
            },],
            'device_id' => 'string|required',
            'device_model' => 'string|required',

        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 'error',
            ]);
        }


        $validated = $validator->validated();
        $validated['dob'] = Carbon::createFromFormat('m/d/Y', $validated['date_of_birth'])->toDateString();
        $user = User::create($validated);


        $this->sendOtp($user->id);

        return response()->json(['status' => 'ok', 'user' => $user, 'token' => $user->createToken($request->device_model)->plainTextToken]);
    }

    public function checkOtp(Request $request)
    {
        $id = User::where('email', $request->email)->first()->id;
        return $this->verifyOtp($id, $request->otp);
    }

    public function sendOtpNow(Request $request)
    {

        $id = User::where('email', $request->email)->first()->id;
        return $this->sendOtp($id);
    }


    public function verifyOtp($userId, $code)
    {

        $time = Otps::where('user_id', $userId)->first();
        if (!$time) {
            return response()->json(['status' => 'error', 'message' => 'Invalid OTP']);
        }

        if (!Hash::check($code . '', $time->code)) {

            return response()->json(['status' => 'error', 'message' => 'Invalid OTP']);
        }
        $startTime = Carbon::parse($time->updated_at);
        $finishTime = Carbon::parse(Carbon::now());
        $totalDuration = $finishTime->diffInSeconds($startTime);
        if ($totalDuration > 60) {

            return response()->json(['status' => 'error', 'message' => 'OTP has expired']);
        }

        Otps::updateOrCreate(['user_id' => $time->user_id], [
            'code' => Hash::make($this->rand)

        ]);

        $user = User::find($userId);
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }

        return response()->json(['status' => 'ok', 'message' => 'OTP has been sent']);
    }




    // public function resetOtp(Request $request)
    // {
    //     // return response()->json(['status'=>'error','message'=>$request->user_id.'']);
    //     $time = Otp::where('user_id', $request->user_id)->first();
    //     if (!$time) {
    //         return response()->json(['status' => 'error', 'message' => 'Invalid OTP']);
    //     }

    //     if (!Hash::check($request->code . '', $time->code)) {

    //         return response()->json(['status' => 'error', 'message' => 'Invalid OTP']);
    //     }
    //     $startTime = Carbon::parse($time->updated_at);
    //     $finishTime = Carbon::parse(Carbon::now());
    //     $totalDuration = $finishTime->diffInSeconds($startTime);
    //     if ($totalDuration > 300) {

    //         return response()->json(['status' => 'error', 'message' => 'OTP has expired']);
    //     }
    //     // User::updateOrCreate(['user_id',$request->user_id],['code'])
    //     Otp::updateOrCreate(['user_id' => $request->user_id], [
    //         'code' => Hash::make($this->rand),

    //     ]);


    //     return response()->json(['status' => 'ok', 'message' => 'OTP has been sent']);
    // }
}
