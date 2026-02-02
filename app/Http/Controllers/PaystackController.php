<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Paystack;

class PaystackController extends Controller
{
    public function callback(Request $request){

        $config =Helpers::get_business_settings('paystack');
        $order=Order::whereId($request['reference'])->first();
        if (!$order) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('messages.not_found')]
                ]
            ], 403);
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$request['reference'],
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer ".$config['secretKey'],
            "Cache-Control: no-cache",
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $result=json_decode($response,true);
        info($result);
        if (isset($result['data']) && $result['data']['status'] == 'success' && $result['data']['reference']==$order->id) {
            $order->transaction_reference = $result['data']['reference'];
            $order->payment_method = 'Paystack';
            $order->payment_status = 'paid';
            $order->order_status = 'confirmed';
            $order->confirmed = now();
            $order->save();
            Helpers::send_order_notification($order);
            if ($order->callback != null) {
                return redirect($order->callback . '&status=success');
            }else{
                return \redirect()->route('payment-success');
            }
        }

        $order->order_status = 'failed';
        $order->failed = now();
        $order->save();
        if ($order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }else{
            return \redirect()->route('payment-fail');
        }
    }

    public function redirectToGateway(Request $request)
    {
        try {
            $order = Order::with(['details'])->where(['id' => $request['orderID']])->first();
            DB::table('orders')
                ->where('id', $order['id'])
                ->update([
                    'payment_method' => 'paystack',
                    'order_status' => 'failed',
                    'transaction_reference' => $request['reference'],
                    'failed' => now(),
                    'updated_at' => now(),
                ]);

            return Paystack::getAuthorizationUrl()->redirectNow();
        } catch (\Exception $e) {
            Toastr::error(translate('messages.your_currency_is_not_supported',['method'=>translate('messages.paystack')]));
            return Redirect::back();
        }
    }

    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
        $order = Order::where(['transaction_reference' => $paymentDetails['data']['reference']])->first();
        if ($paymentDetails['status'] == true) {
            $order->payment_status = 'paid';
            $order->order_status = 'confirmed';
            $order->confirmed = now();
            $order->save();
            try {
                Helpers::send_order_notification($order);
            } catch (\Exception $e) {}
            if ($order->callback != null) {
                return redirect($order->callback . '&status=success');
            }else{
                return \redirect()->route('payment-success');
            }
        } else {
            DB::table('orders')
            ->where('id', $order['id'])
            ->update([
                'payment_method' => 'paystack',
                'order_status' => 'failed',
                'failed' => now(),
                'updated_at' => now(),
            ]);
            if ($order->callback != null) {
                return redirect($order->callback . '&status=fail');
            }else{
                return \redirect()->route('payment-fail');
            }
        }
    }
}
