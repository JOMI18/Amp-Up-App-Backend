<?php


namespace App\Traits;

use Illuminate\Support\Str;
use GuzzleHttp\Client;

// you need to create an account and pay to use
trait Monnify
{
    public function getMonnifyToken()
    {

    }
    public function createMonnifyAccounts($user)
    {
        $client = new Client();
        $data = [
            "accountReference" => Str::uuid(),
            "accountName" => $user->first_name,
            "customerEmail" => $user->email,
            "currencyCode" => "NGN",
            "bvn" => $user->bvn,
            // ask how that works
            "contractCode" => config("app.MONNIFY_CONTRACTCODE"), // inside config for security
            "customerName" => $user->first_name,
            "getAllAvailableBanks" => true,

        ];

        $response = $client->post("https://api.monnify.com/api/v2/bank-transfer/reserved-accounts", [
            "json" => $data,
            "headers" => [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . $this->getMonnifyToken(),
            ]
        ]);

        $dataencod = json_decode($response->getBody()->getContents(), true);

        $responseBody = $dataencod("responseBody");

        // Accessing specific fields
        $contractCode = $responseBody["contractCode"];
        $accountReference = $responseBody["accountReference"];
        $accountName = $responseBody["accountName"];
        $customerEmail = $responseBody["customerEmail"];
        $customerName = $responseBody["customerName"];
        $accounts = $responseBody["accounts"];
        $collectionChannel = $responseBody["collectionChannel"];
        $reservationReference = $responseBody["reservationReference"];
        $reservedAccountType = $responseBody["reservedAccountType"];
        $status = $responseBody["status"];
        $createdOn = $responseBody["createdOn"];
        $incomeSplitConfig = $responseBody["incomeSplitConfig"];
        $bvn = $responseBody["bvn"];
        $restrictPaymentSource = $responseBody["restrictPaymentSource"];

        foreach ($accounts as $account) {
            // BankAccount a table and model needs to be created
            // BankAccount::create([
            //     "bankCode" => $account["bankCode"],
            //     "bankName" => $account["bankName"],
            //     "accountNumber" => $account["accountNumber"],
            //     "accountName" => $account["accountName"],
            //     "user_id" => $user_id,
            //     "status" => $status,
            //     "accountReference" => $accountReference,
            // ]);
        }
        return true;
    }
}
