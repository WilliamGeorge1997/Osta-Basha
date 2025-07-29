<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/send-sms', function (Request $request) {
    // Initialize the Vonage client
    $basic = new \Vonage\Client\Credentials\Basic("532a1a51", "OfxkimnW3TQdgmEN");
    $client = new \Vonage\Client($basic);

    // Use proper international format with + prefix
    $to = "+201289521255"; // Make sure this is a valid mobile number

    // Use a simple alphanumeric sender ID (or try a numeric one)
    $from = "OstaBasha";

    // Send the SMS
    $response = $client->sms()->send(
        new \Vonage\SMS\Message\SMS($to, $from, 'Your verification code is 1234')
    );

    $message = $response->current();

    return response()->json([
        'status' => $message->getStatus(),
        'message' => $message->getStatus() == 0 ? 'The message was accepted by our SMS provider' : 'Failed with status: ' . $message->getStatus(),
        'message_id' => $message->getMessageId()
    ]);
});
