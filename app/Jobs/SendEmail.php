<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     public $data, $email;

     public function __construct($data, $email)
    {
        $this->data = $data;
        $this->email = $email;
    }


     // public function sendSms($email, $body)
    // {
    //     // use GuzzleHttp\Client;
    //     $to = User::where('email', $email)->first();
    //     $client = new Client();

    //     $body=strip_tags($body);
    //     $response = $client->post('https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=Md8dAnfjl6SKq9v0xgBeEzFDC6gIwnvX6EhfzW2zCQSa4vyPi9ajcmTiPycD&from=Freedoms&to='.$to->phone.'&body='.$body);

    //     // Get the response body as a string
    //     $result = $response->getBody()->getContents();
    //     return true;
    //     // Print or process the result as needed
    //     // echo $result;
    // }

    
    /**
     * Execute the job.
     */
    public function handle()
    {
        //
        foreach ($this->email as $mail) {
            // $this->sendSms($mail, $this->data->data['message']);
            Mail::to($mail)->send($this->data);
            
        }
    }
}
