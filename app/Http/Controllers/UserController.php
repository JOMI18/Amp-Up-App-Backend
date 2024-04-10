<?php

namespace App\Http\Controllers;

use App\Models\Otps;
use App\Models\User;
use App\Traits\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use Utilities;

    public function verifyBVN(Request $request)
    {
        $validated = Validator::make($request->all(), ["bvn" => ["required", "digits:11", "unique:users", "numeric"]]);
        if ($validated->fails()) {
            return response()->json(["status" => "error", "message" => $validated->errors()->first()]);
        }
        // NECESSARY DOCUMENTATION NEEDED
        $response = Http::withHeaders([
            "app-id" => config("app.prembly_app-id"),
            "Cookie" => config("app.prembly_Cookie"),
            "x-api-key" => config("app.prembly_x-api-key"),
        ])->post("https://api.prembly.com/identitypass/verification/bvn_validation", ["number" => $request->bvn]);

        $responseData = $response->json();
        $status = $responseData["status"];
        if ($status !== true) {
            return response()->json(["status" => "error", "message" => "Invalid BVN"]);
        }

        $data = $responseData["data"];
        $firstName = $data["firstName"];
        $middleName = $data["middleName"];
        $lastName = $data["lastName"];
        $dateOfBirth = $data["dateOfBirth"];
        $phoneNumber = $data["phoneNumber"];
        $user = User::find($request->user()->id)->first();
        $dob = Carbon::createFromFormat("d-M-Y", $dateOfBirth)->format("Y-m-d");

        if ($user->dob !== $dob) {
            return response()->json(["status" => "error", "message" => "Date of birth do not match"]);
        };

        if (strtolower($user->first_name) !== trim(strtolower($firstName)) || strtolower($user->last_name) !== trim(strtolower($lastName))) {
            return response()->json(["status" => "error", "message" => "Names do not match"]);
        };


        $user->bvn = $request->bvn;
        $user->bvn_verified_at = now();
        $user->save();

        $random = mt_rand(1000, 9999);
        $msg = "One Time Password from " . config("app.name") . ":" . $random . "Valid for 30 seconds, one-time";
        Otps::updateOrCreate(["user_id" => $user->id], ["code" => $random]);

        // if ($request->bvn) {
        //     dispatch(new MonnifyJob($user));
        // }

        $this->sendSms($this->formatPhoneWithZip($phoneNumber), $msg);

        // return response()->json(["status" => "ok", "message" => "Your BVN was verified successfully", "phone"=>]);
        return response()->json(["status" => "ok", "message" => "Your BVN was verified successfully",]);
    }



    public function createTxPin(Request $request)
    {
        $user = User::find($request->user()->id)->first();
        if (!$user) {
            return response()->json(['status' => 'error', "message" => 'User not found']);
        }
        $user->tx_pin = Hash::make($request->pin);
        $user->save();
        return response()->json(['status' => 'ok', "message" => 'You have created your PIN successfully']);
    }

    public function sendSmsOtp(Request $request)
    {
        $random = mt_rand(1000, 9999);

        $msg = "One Time Password from " . config("app-name") . ": " . $random . " Valid for 30 seconds, one-time";
        Otps::updateOrCreate(['user_id' => auth()->user()->id], [
            'code' => $random,
        ]);
        $this->sendSms($this->formatPhoneWithZip($request->phone), $msg);
        return response()->json(['status' => 'ok', 'message' => 'OTP has been sent successfully.']);
    }

    public function detectNumberProvider(Request $request)
    {
        return $this->detectPhoneNetwork($request->phone);
    }
}
