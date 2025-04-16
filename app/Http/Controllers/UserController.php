<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PackageController;
use App\Http\Models\AccountReup;
use App\Http\Models\Customer;
use App\User;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Log;
use function trans;
use function view;

class UserController extends Controller {

    public function __construct() {
        
    }

    public function index() {
        if (Auth::check()) {
            return redirect()->secure('calendar');
        } else {
            return view('layouts.login');
        }
    }

    public function login(Request $req) {
//        Log::info('onLogin|request=' . json_encode($req->all()));
        $username = $req->user_name;
        $password = $req->password;
        $rediect = "calendar";
        if (isset($req->redirect) && $req->redirect != "") {
            $rediect = $req->redirect;
        }
        $validator = Validator::make($req->all(), [
                    'user_name' => 'required',
                    'password' => 'required|min:3|max:32',
//                    'g-recaptcha-response' => 'required|captcha'
                        ], [
                    'user_name.required' => 'The User Name field is required',
                    'password.required' => 'The Password field is required',
                    'password.min' => trans('label.validate.password.min', ['values' => '3']),
                    'password.max' => trans('label.validate.password.max', ['values' => '32']),
                    'g-recaptcha-response.required' => trans('label.validate.g-recaptcha-response.required'),
                    'g-recaptcha-response.captcha' => trans('label.validate.g-recaptcha-response.captcha')
        ]);
        if ($validator->fails()) {
            Log::info("login fail " . json_encode($validator));
            return redirect('login')
                            ->withErrors($validator)
                            ->withInput();
        }
        if (Auth::attempt(['user_name' => $username, 'password' => $password])) {
            if (Auth::user()->status == 1) {
                if (in_array('11', explode(",", Auth::user()->role)) || in_array('20', explode(",", Auth::user()->role)) || in_array('1', explode(",", Auth::user()->role))) {
//                    return redirect('dashboard');
                    return redirect()->secure($rediect);
                } else {
                    Auth::logout();
                    return redirect('login')->with("message", 'You do not have permission to access this application');
                }
            } else {
                Auth::logout();
                return redirect('login')->with("message", 'Your account has been locked');
            }
        }
        return redirect('login')->with("message", 'Wrong User name or Password');
    }

    public function logout() {
        Auth::logout();
        return redirect('login');
    }

    public function viewRegister() {
        return view('auth.register');
    }

    public function onCreateNewUser(Request $req) {
        $u_name = $req->user_name;
        $validator = Validator::make($req->all(), [
                    'name' => 'required|max:190',
                    'phone' => 'required|min:5|max:20',
                    'user_name' => 'required|string|unique:users,user_name,' . $u_name . '|regex:/^([a-zA-Z0-9]+[\ \-]?)+[a-zA-Z0-9]+$/im',
                    'user_name' => 'required|string|unique:accountreup,user_name,' . $u_name . '|regex:/^([a-zA-Z0-9]+[\ \-]?)+[a-zA-Z0-9]+$/im',
                    'password' => 'required|min:3|max:32',
                    'password_confirmation' => 'required|same:password'], [
                    'name.required' => trans('label.validate.name.required'),
                    'name.max' => trans('label.validate.password.max', ['values' => '190']),
                    'user_name.required' => trans('label.validate.user_name.required'),
                    'user_name.unique' => trans('label.validate.user_name.unique'),
                    'user_name.regex' => trans('label.validate.user_name.regex'),
                    'phone.required' => trans('label.validate.phone.required'),
                    'phone.min' => trans('label.validate.phone.min', ['values' => '5']),
                    'phone.max' => trans('label.validate.phone.max', ['values' => '20']),
                    'password.required' => trans('label.validate.password.required'),
                    'password.min' => trans('label.validate.password.min', ['values' => '3']),
                    'password.max' => trans('label.validate.password.max', ['values' => '32']),
                    'password_confirmation.same' => trans('label.validate.math_pass')
        ]);
        $FBID_Decrypt = "";
        //kiểm tra sự tồn tại của fb_id
        if (!isset($req->user_id)) {
            return redirect('login');
        } else {
            try {
                //giải mã fb_id
                $key = Config::get('config.encrypt_key_fb');
                $FBID_Decrypt = Crypto::decryptWithPassword($req->user_id, $key);
            } catch (Exception\WrongKeyOrModifiedCiphertextException $exc) {
                //giải mã fb_id fail thì trả về trang login
                return redirect('login');
            }
            //nếu giải mã thành công thì check fb_id trong bảng customer
            $customer = Customer::where('customer_id', $FBID_Decrypt)->first();
            if (!$customer) {
                return redirect('login');
            }
        }
        //nếu validate fail thì trả về kết quả
        if ($validator->fails()) {
            return redirect('register')
                            ->withErrors($validator)
                            ->withInput()->with('user_id', $req->user_id);
        }
        if (strpos(trim($u_name), " ") !== false) {
            return redirect('register')
                            ->withErrors($validator)
                            ->withInput()->with('user_name_wrong', 'Invalid username');
        }
        //lưu thông tin user
        $user = new User();
        $user->name = $req->name;
        $user->password = bcrypt(trim($req->password));
        $user->password_plaintext = trim($req->password);
        $user->user_name = strtolower(trim($req->user_name));
        $user->phone = $req->phone;
        $user->is_default = 1;
        $user->customer_id = $FBID_Decrypt;
        $user->status = 0;
        $user->save();
        //lu thong tin account reup
        $accountReup = new AccountReup();
        $accountReup->user_code = strtolower(trim($req->user_name)) . '_' . time();
        $accountReup->user_name = strtolower(trim($req->user_name));
        $accountReup->pass_word = trim($req->password);
        $accountReup->create_time = time();
        $accountReup->status = 1;
        $accountReup->user_id = $customer->customer_id;
        $accountReup->save();

        Auth::guard()->login($user);
        //đăng ký gói cước free
        PackageController::registerPackageForCustomer($user, 'AUTOTEST', $customer);
        return redirect('login');
    }

    public function onChangeInfo(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|onChangeInfo|request=' . json_encode($request->all()));
        $status = "danger";
        $content = array();
        $isChangePass = 0;
        try {
            //validate name
            if (!isset($request->name)) {
                array_push($content, trans('label.validate.name.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                $name = $request->name;
                if (strlen($name) > 190) {
                    array_push($content, str_replace(':values', '190', trans('label.validate.name.max')));
                    return array('status' => $status, 'content' => $content);
                }
            }
            if (!isset($request->timezone)) {
                array_push($content, trans('label.validate.timezone.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                $timezone = $request->timezone;
                $value = array('-10', '-9', '-8', '-7', '-6', '-5', '-4', '-3', '-2', '-1', '+0', '+1', '+2', '+3', '+4', '+5', '+6', '+7', '+8', '+9', '+10', '+11', '+12');
                if (!in_array($timezone, $value)) {
                    array_push($content, trans('label.validate.timezone.invalid'));
                    return array('status' => $status, 'content' => $content);
                }
            }
            if (!isset($request->phone)) {
                array_push($content, trans('label.validate.phone.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                $phone = $request->phone;
                if (strlen($phone) > 20) {
                    array_push($content, str_replace(':values', '20', trans('label.validate.phone.max')));
                    return array('status' => $status, 'content' => $content);
                }
                if (strlen($phone) < 5) {
                    array_push($content, str_replace(':values', '5', trans('label.validate.phone.min')));
                    return array('status' => $status, 'content' => $content);
                }
            }
            if (!isset($request->user_name)) {
                array_push($content, trans('label.validate.user_name.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                if ($user->user_name != $request->user_name) {
                    array_push($content, trans('label.message.notHasRole'));
                    return array('status' => $status, 'content' => $content);
                }
            }

            if (!isset($request->passwordOld)) {
                array_push($content, trans('label.validate.passwordOld.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                $oldPass = $request->passwordOld;
                if (strlen($oldPass) > 32) {
                    array_push($content, str_replace(':values', '32', trans('label.validate.passwordOld.max')));
                    return array('status' => $status, 'content' => $content);
                }
                if (strlen($oldPass) < 3) {
                    array_push($content, str_replace(':values', '3', trans('label.validate.passwordOld.min')));
                    return array('status' => $status, 'content' => $content);
                }
                if (!Auth::attempt(['user_name' => $request->user_name, 'password' => $oldPass, 'status' => 1])) {
                    array_push($content, trans('label.validate.login_fail'));
                    return array('status' => $status, 'content' => $content);
                }
//                else {
//                    $user = User::where('user_name', $req->user_name)->where('status', 1)->first();
//                }
                if (isset($request->passwordNew)) {
                    $isChangePass = 1;
                    $passwordNew = $request->passwordNew;
                    if (strlen($passwordNew) > 32) {
                        array_push($content, str_replace(':values', '32', trans('label.validate.passwordNew.max')));
                        return array('status' => $status, 'content' => $content);
                    }
                    if (strlen($passwordNew) < 3) {
                        array_push($content, str_replace(':values', '3', trans('label.validate.passwordNew.min')));
                        return array('status' => $status, 'content' => $content);
                    }
                    if (!isset($request->passwordNewConfirm)) {
                        array_push($content, trans('label.validate.passwordNew.required'));
                        return array('status' => $status, 'content' => $content);
                    }
                    if ($passwordNew != $request->passwordNewConfirm) {
                        array_push($content, trans('label.validate.new_pass_math'));
                        return array('status' => $status, 'content' => $content);
                    }
                }
            }
            if (count($content) != 0) {
                $status = "danger";
            } else {
                $status = "success";
                $accountReup = AccountReup::where('user_name', strtolower(trim($request->user_name)))->first();
                $user->name = $request->name;
                $user->timezone = $timezone;
                if ($isChangePass == 1) {
                    $user->password = bcrypt(trim($request->passwordNew));
                    $user->password_plaintext = trim($request->passwordNew);
                    $accountReup->pass_word = trim($request->passwordNew);
                }
                $user->user_name = strtolower(trim($request->user_name));
                $user->phone = $request->phone;
                $user->save();
                $accountReup->save();
            }
        } catch (\Exception $ex) {
            Log::info($ex->getTraceAsString());
            $status = "danger";
            array_push($content, trans('label.message.error'));
        }
        return array('status' => $status, 'content' => $content);
    }

    public function apilogin(Request $request) {
        Log::info('onLogin|request=' . json_encode($request->all()));
        $username = strtolower($request->username);
        $password = $request->password;

        $validator = Validator::make($request->all(), [
                    'username' => 'required',
                    'password' => 'required|min:3|max:32',
                    'username.required' => 'The User Name field is required',
                    'password.required' => 'The Password field is required',
                    'password.min' => trans('label.validate.password.min', ['values' => '3']),
                    'password.max' => trans('label.validate.password.max', ['values' => '32'])
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => 'Wrong User name or Password']);
        }
        if (Auth::attempt(['user_name' => $username, 'password' => $password])) {
            $user = Auth::user();
            if ($user->status == 1) {
                if (in_array('11', explode(",", $user->role))) {
                    $token  = $user->token;
                    $expire = $user->expire_token;
                    //kiểm tra xem nếu token ko còn hạn thì tạo token mới
                    if (($user->token != null && $user->expire_token < time()) || $user->token == null) {
                        $token = Str::random(64);
                        $user->token = $token;
                        $expire = time() + 86400 * 30;
                        if ($request->remember_me == 1) {
                            $expire = time() + 86400 * 60;
                        }
                        $user->expire_token = $expire;
                        $user->save();
                    }
                    $admin = 0;
                    if (in_array('19', explode(",", $user->role))) {
                        $admin = 1;
                    }
                    return response()->json(['status' => 1, 'message' => 'Login succesfully', 'username' => $username, 'token' => $token, 'expire' => $expire, "is_admin" => $admin]);
                } else {
                    return response()->json(['status' => 0, 'message' => 'You do not have permission to access this application']);
                }
            } else {

                return response()->json(['status' => 0, 'message' => 'Your account has been locked']);
            }
        }
        return response()->json(['status' => 0, 'message' => 'Wrong User name or Password']);
    }

    public function checkToken(Request $request) {
        Log::info('checkToken|request=' . json_encode($request->all()));
        $user = User::where("token", $request->token)->where("expire_token", ">", time())->first(["id", "user_name", "token", "expire_token", "role"]);
        $isAdmin = false;
        if ($user) {
            if (in_array('1', explode(",", $user->role)) || in_array('20', explode(",", $user->role))) {
                $isAdmin = true;
            }
            return response()->json(["is_valid" => true, "message" => "success", "username" => $user->user_name, "expire_time" => $user->expire_token, "token" => $user->token, "is_admin" => $isAdmin]);
        }
        return response()->json(["is_valid" => false, "message" => "token is invalid"]);
    }
    
    public function listUser(){
        $user = User::where("status",1)->where("role","like","%16%")->pluck("user_name");
        return response()->json($user);
    }

}
