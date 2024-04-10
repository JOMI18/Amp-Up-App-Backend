<?php


namespace App\Traits;

use App\Jobs\SendEmail;
use App\Mail\EmailAlert;
use App\Models\Otps;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;

// use Propaganistas\laravelPhone\PhoneNumber; // composer require propaganistas/laravel-phone


// Remember that namespaces and class names in PHP are case-sensitive.
trait Utilities
{
    public $rand;

    public function sendOtp($userId)
    {

        $this->rand = mt_rand(1000, 9999);
        $user = User::find($userId);

        if (!$user->id) {
            return false;
        }
        $r = new EmailAlert([
            'name' => $user->first_name, 'subject' => 'Amp Up OTP notification',
            'view' => 'alert', 'message' => 'The OTP to verify your email address on ' . env('APP_NAME') . ' is <b>' . $this->rand . '</b>'
        ]);
        Otps::updateOrCreate(['user_id' => $user->id], [
            'code' => $this->rand,
        ]);
        dispatch(new SendEmail($r, [$user->email]));
        return  response()->json(['status' => 'ok', 'message' => 'OTP has been sent successfully.']);
    }


    public function sendEmails($data, array $emails)
    {

        dispatch(new SendEmail($data, $emails));
    }


    public function sendSms($phone, $message)
    {

        $response = Http::withHeaders([
            "Content-Type" => "application/json"
        ])->post(
            "https://api.ng.teermii.com/api/sms/send",
            [
                "api_key" => config("app.termi_api_key"),
                "to" => $phone,
                "from" => "N-Alert",
                "sms" => $message,
                "type" => "plain",
                "channel" => "dnd",
            ]
        );

        $responseData =  $response->json();
        return true;
    }

    public function validatePhone($phone)
    {
        $validated = Validator::make($phone, ["phone" => "phone:NG"]);
        if ($validated->fails()) {
            return false;
        }
        return true;
    }

    // public function formatPhoneWithZip($phone)
    // {
    //     // // Ensure $phone is not null and is a string
    //     // if (is_null($phone) || !is_string($phone)) {
    //     //     return false; // or handle the error appropriately
    //     // }

    //     // // Validate phone number format
    //     // if (!$this->validatePhone(['phone' => $phone])) {
    //     //     return false; // or handle the validation failure
    //     // }

    //     // // Phone number is valid, create PhoneNumber object
    //     // $phoneNumber = new PhoneNumber($phone, "NG");
    //     // return $phoneNumber->formatE164();

    //     $phone = new PhoneNumber($phone, "NG");
    //     return $phone->formatE164();
    // }

    public function formatPhoneWithoutZip($phone)
    {
        $phone = new PhoneNumber($phone, "NG");
        $formatted = $phone->formatNational();
        return Str::replace(" ", "", $formatted);
    }

    public function detectPhoneNetwork($phone)
    {
        $data = [
            "api_key" => config("app.termi_api_key"),
            "phone_number" => $this->formatPhoneWithZip($phone)
        ];

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
        ])->get("https://api.ng.termii.com/api/check/dnd", $data)->body();

        $response = json_decode($response, true);

        // explode works like js split
        return explode(" ",  $response["network_code"])[0];
    }
}
