<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Zone;
use App\Models\Store;
use App\CentralLogics\StoreLogic;
use App\Models\Admin;
use App\Models\Translation;
use App\Models\VendorEmployee;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class VendorLoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $vendor_type= $request->vendor_type;

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if($vendor_type == 'owner'){
            if (auth('vendor')->attempt($data)) {
                $token = $this->genarate_token($request['email']);
                $vendor = Vendor::where(['email' => $request['email']])->first();
                if(!$vendor->stores[0]->status)
                {
                    return response()->json([
                        'errors' => [
                            ['code' => 'auth-002', 'message' => translate('messages.inactive_vendor_warning')]
                        ]
                    ], 403);
                }
                $vendor->auth_token = $token;
                $vendor->save();
                return response()->json(['token' => $token, 'zone_wise_topic'=> $vendor->stores[0]->zone->store_wise_topic], 200);
            }  else {
                $errors = [];
                array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
                return response()->json([
                    'errors' => $errors
                ], 401);
            }
        }elseif($vendor_type == 'employee'){

            if (auth('vendor_employee')->attempt($data)) {
                $token = $this->genarate_token($request['email']);
                $vendor = VendorEmployee::where(['email' => $request['email']])->first();
                if($vendor->store->status == 0)
                {
                    return response()->json([
                        'errors' => [
                            ['code' => 'auth-002', 'message' => translate('messages.inactive_vendor_warning')]
                        ]
                    ], 403);
                }
                $vendor->auth_token = $token;
                $vendor->save();
                $role = $vendor->role ? json_decode($vendor->role->modules):[];
                return response()->json(['token' => $token, 'zone_wise_topic'=> $vendor->store->zone->store_wise_topic, 'role'=>$role], 200);
            } else {
                $errors = [];
                array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
                return response()->json([
                    'errors' => $errors
                ], 401);
            }
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }

    }

    private function genarate_token($email)
    {
        $token = Str::random(120);
        $is_available = Vendor::where('auth_token', $token)->where('email', '!=', $email)->count();
        if($is_available)
        {
            $this->genarate_token($email);
        }
        return $token;
    }

 public function register(Request $request)
{
    $status = BusinessSetting::where('key', 'toggle_store_registration')->first();
    if (!isset($status) || $status->value == '0') {
        return response()->json([
            'errors' => Helpers::error_processor('self-registration', translate('messages.store_self_registration_disabled'))
        ]);
    }

    $validator = Validator::make($request->all(), [
        'f_name' => 'required|max:100',
        'l_name' => 'nullable|max:100',
        'latitude' => 'required',
        'longitude' => 'required',
        'email' => 'required|unique:vendors',
        'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:vendors',
        'minimum_delivery_time' => 'required',
        'maximum_delivery_time' => 'required',
        'delivery_time_type' => 'required',
        'password' => ['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
        'zone_id' => 'required',
        'module_id' => 'required',
        'logo' => 'required|file',
        // cover_photo اختيارية
        'cover_photo' => 'nullable|file',
        'tax' => 'required',
        'translations' => 'required'
    ]);

    // Zone check
    if ($request->zone_id) {
        $point = new Point($request->latitude, $request->longitude);
        $zone = Zone::contains('coordinates', $point)->where('id', $request->zone_id)->first();
        if (!$zone) {
            $validator->getMessageBag()->add('latitude', translate('messages.coordinates_out_of_zone'));
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
    }

    /**
     * translations ممكن تيجي:
     * - string JSON (الأشهر في form-data)
     * - array مباشرة (لو client بيبعتها json)
     */
    $data = $request->translations;

    if (is_string($data)) {
        $data = json_decode($data, true);
    }

    if (!is_array($data) || count($data) < 1) {
        $validator->getMessageBag()->add('translations', translate('messages.Name and description in english is required'));
        return response()->json(['errors' => Helpers::error_processor($validator)], 403);
    }

    if ($validator->fails()) {
        return response()->json(['errors' => Helpers::error_processor($validator)], 403);
    }

    // استخراج name/address بشكل آمن (بدون الاعتماد على $data[0] و $data[1])
    $storeName = null;
    $storeAddress = null;

    foreach ($data as $t) {
        $key = $t['key'] ?? null;
        $val = $t['value'] ?? null;

        if ($key === 'name' && $val) {
            $storeName = $val;
        }
        if (($key === 'address' || $key === 'description') && $val) {
            // خليها address لو مشروعك بيستخدم description بدل address
            $storeAddress = $val;
        }
    }

    // fallback لو الداتا جاية بالاندكس زي كودك القديم
    if (!$storeName && isset($data[0]['value'])) {
        $storeName = $data[0]['value'];
    }
    if (!$storeAddress && isset($data[1]['value'])) {
        $storeAddress = $data[1]['value'];
    }

    if (!$storeName) {
        return response()->json([
            'errors' => [
                ['code' => 'translations', 'message' => 'Store name is required in translations. (key=name)']
            ]
        ], 403);
    }

    if (!$storeAddress) {
        return response()->json([
            'errors' => [
                ['code' => 'translations', 'message' => 'Store address is required in translations. (key=address)']
            ]
        ], 403);
    }

    try {
        DB::beginTransaction();

        $vendor = new Vendor();
        $vendor->f_name = $request->f_name;
        $vendor->l_name = $request->l_name;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->password = bcrypt($request->password);
        $vendor->status = null;
        $vendor->save();

        $store = new Store();
        $store->name = $storeName;
        $store->phone = $request->phone;
        $store->email = $request->email;

        // logo required
        $store->logo = Helpers::upload('store/', 'png', $request->file('logo'));

        // cover_photo optional (تجنب null)
        if ($request->hasFile('cover_photo')) {
            $store->cover_photo = Helpers::upload('store/cover/', 'png', $request->file('cover_photo'));
        }

        $store->address = $storeAddress;
        $store->latitude = $request->latitude;
        $store->longitude = $request->longitude;
        $store->vendor_id = $vendor->id;
        $store->zone_id = $request->zone_id;
        $store->tax = $request->tax;
        $store->delivery_time = $request->minimum_delivery_time . '-' . $request->maximum_delivery_time . ' ' . $request->delivery_time_type;
        $store->module_id = $request->module_id;
        $store->status = 0;
        $store->save();

        // stores_count + schedule
        // increment stores_count safely
if ($store->module) {
    $store->module->increment('stores_count');
}

// always_open safely
$moduleType   = optional($store->module)->module_type;     // delivery / service / default ...
$moduleConfig = $moduleType ? config('module.' . $moduleType) : null;

if (is_array($moduleConfig) && ($moduleConfig['always_open'] ?? false)) {
    StoreLogic::insert_schedule($store->id);
}

        // تجهيز translations للـ insert
        foreach ($data as $key => $i) {
            $data[$key]['translationable_type'] = 'App\Models\Store';
            $data[$key]['translationable_id'] = $store->id;
        }
        Translation::insert($data);

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        info('Store register error: ' . $e->getMessage());

        return response()->json([
            'message' => 'Registration failed',
            'error' => $e->getMessage()
        ], 500);
    }

    // mails
    try {
        $admin = Admin::where('role_id', 1)->first();

        $mail_status = Helpers::get_mail_status('registration_mail_status_store');
        if (config('mail.status') && $mail_status == '1') {
            Mail::to($request['email'])->send(new \App\Mail\VendorSelfRegistration('pending', $vendor->f_name . ' ' . $vendor->l_name));
        }

        $mail_status = Helpers::get_mail_status('store_registration_mail_status_admin');
        if (config('mail.status') && $mail_status == '1' && $admin && !empty($admin['email'])) {
            Mail::to($admin['email'])->send(new \App\Mail\StoreRegistration('pending', $vendor->f_name . ' ' . $vendor->l_name));
        }
    } catch (\Exception $ex) {
        info($ex->getMessage());
    }

    return response()->json(['message' => translate('messages.application_placed_successfully')], 200);
}
}
