<?php
namespace App\Common\Network; 

class GoogleOtp
{
    // Tạo mã OTP từ secret key
    public static function getOtpCode(string $secret): string
    {
        $timeSlice = floor(time() / 30); // Thời gian chia đoạn 30 giây
        $secretKey = self::base32Decode($secret); // Giải mã Base32
        if($secretKey==null){
            return null;
        }
        $timeBytes = pack('N*', 0) . pack('N*', $timeSlice); // Định dạng thời gian

        // Tính toán giá trị hash HMAC với SHA1
        $hash = hash_hmac('sha1', $timeBytes, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;

        // Lấy 4 byte từ vị trí offset
        $binary = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;

        // Lấy mã OTP gồm 6 chữ số
        return str_pad($binary % 1000000, 6, '0', STR_PAD_LEFT);
    }

    // Giải mã Base32 cho secret key
    private static function base32Decode(string $input): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $buffer = 0;
        $bufferSize = 0;
        try {
            foreach (str_split(strtoupper($input)) as $char) {
                $value = strpos($alphabet, $char);
                if ($value === false) {
                    return null;
                }

                $buffer = ($buffer << 5) | $value;
                $bufferSize += 5;

                if ($bufferSize >= 8) {
                    $bufferSize -= 8;
                    $output .= chr(($buffer & (0xFF << $bufferSize)) >> $bufferSize);
                }
            }
            
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
            return null;
        }


        return $output;
    }
}

//// Sử dụng class GoogleOtp để lấy OTP
//$secret = "6cewmo4w5dl263fmorflp5xihej4vdd2"; // Thay bằng secret thực tế
//echo "Google Authenticator OTP: " . GoogleOtp::getOtpCode($secret);
?>
