<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Log;
use function redirect;
use function session;

class SSOController extends Controller {

    private $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function redirectToProvider() {
        $query = http_build_query([
            'client_id' => 'DHjWahrmBBbFn0CBmojrIrhaEHQkjHZrlgUW3rTk',
//            'redirect_uri' => route('handleProviderCallback'),
            'response_type' => 'code',
        ]);

        return redirect('https://5.78.113.248:8000/o/authorize?' . $query);
    }

//    public function handleProviderCallback(Request $request)
//    {
//        $response = Http::asForm()->post('https://5.78.113.248:8000/o/token/', [
//            'grant_type' => 'authorization_code',
//            'client_id' => 'DHjWahrmBBbFn0CBmojrIrhaEHQkjHZrlgUW3rTk',
//            'client_secret' => 'wyooPoxQEbyIel3mrmZQSnmtCCgKpeyMnlNXKKERQD6JPTBpnhHmC8YUwvJZN3DbIjrIsH0sL8axJS9RISef7hzH5Pj1YQIlc44bb5vSYgP2eSNnA0xqseY2b9WfSu1T',
////            'redirect_uri' => route('handleProviderCallback'),
//            'code' => $request->code,
//        ]);
//
//        $tokens = $response->json();
//        Log::info("tokens: ".json_encode($tokens));
//
//        // Lưu token và sử dụng chúng để xác thực người dùng trong ứng dụng của bạn
//        // Ví dụ: Lưu trữ token trong session hoặc database
//
//        return redirect('/calendar');
//    }

    public function handleProviderCallback(Request $request) {
        Log::info('|handleProviderCallback|request=' . json_encode($request->all()));
//        if (empty($request->input('state')) || ($request->input('state') !== session('oauth2state'))) {
//            session()->forget('oauth2state');
//            return redirect('/')->with('error', 'Invalid state');
//        }

        try {
            $tokenResponse = $this->getAccessToken($request->input('code'));
            $tokens = json_decode($tokenResponse, true);

            // Using the access token, get the user's details.
            $userResponse = $this->getResourceOwner($tokens['access_token']);
            $userDetails = json_decode($userResponse, true);

            // Tạo hoặc cập nhật người dùng trong database
//            $authUser = User::updateOrCreate([
//                'email' => $userDetails['email'],
//            ], [
//                'name' => $userDetails['name'],
//                'email' => $userDetails['email'],
//                // Lưu thêm thông tin khác nếu cần
//            ]);
            // Đăng nhập người dùng
//            Auth::login($authUser);

            return redirect('/home');
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Failed to authenticate');
        }
    }

    private function refreshToken($refreshToken) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://5.78.113.248:8000/o/token/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'refresh_token',
            'client_id' => 'DHjWahrmBBbFn0CBmojrIrhaEHQkjHZrlgUW3rTk',
            'client_secret' => 'wyooPoxQEbyIel3mrmZQSnmtCCgKpeyMnlNXKKERQD6JPTBpnhHmC8YUwvJZN3DbIjrIsH0sL8axJS9RISef7hzH5Pj1YQIlc44bb5vSYgP2eSNnA0xqseY2b9WfSu1T',
            'refresh_token' => $refreshToken,
        ]));

        $headers = [];
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);
        Log::info("refreshToken $result");
        if (curl_errno($ch)) {
            throw new Exception('Error:' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }
    private function getAccessToken($code) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://5.78.113.248:8000/o/token/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'authorization_code',
            'client_id' => 'DHjWahrmBBbFn0CBmojrIrhaEHQkjHZrlgUW3rTk',
            'client_secret' => 'wyooPoxQEbyIel3mrmZQSnmtCCgKpeyMnlNXKKERQD6JPTBpnhHmC8YUwvJZN3DbIjrIsH0sL8axJS9RISef7hzH5Pj1YQIlc44bb5vSYgP2eSNnA0xqseY2b9WfSu1T',
            'code' => $code,
        ]));

        $headers = [];
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);
        Log::info("getAccessToken $result");
        if (curl_errno($ch)) {
            throw new Exception('Error:' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }

    private function getResourceOwner($accessToken) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://5.78.113.248:8000/o/introspect/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'token' => $accessToken,
        ]));
        $headers = [];
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'Authorization: Bearer ' . $accessToken;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);
        Log::info("getResourceOwner $result");
        if (curl_errno($ch)) {
            throw new Exception('Error:' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }

}
