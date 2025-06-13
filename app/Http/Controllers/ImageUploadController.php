<?php

namespace App\Http\Controllers;

use App\Common\Network\AWSHelper;
use App\Common\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Validator;

class ImageUploadController extends Controller {

    public function imageUpload() {

        return view('pannel_lyric.upload_image');
    }

    public function imageUploadPost(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ImageUploadController.imageUploadPost|request=' . json_encode($request->all()));

        $validate = Validator::make($request->all(), ['image' => 'required|image|mimes:jpeg,png,jpg,gif|max:6144']);
        $size = request()->image->getSize();
        Log::info("imageUploadPost" . $size);
        if ($validate->passes()) {
            $imageName = $user->user_name . '_' . time() . '.' . request()->image->getClientOriginalExtension();
            $path = "uploads";
            if (isset($request->path)) {
                $path = "$request->path";
            }
            request()->image->move(public_path($path), $imageName);
            return response()->json([
                        'message' => 'Upload successfully',
                        'uploaded_image' => "/$path/$imageName",
                        'status' => 'success',
            ]);
        } else {
            return response()->json([
                        'message' => $validate->errors()->all(),
                        'uploaded_image' => '',
                        'status' => 'error',
            ]);
        }
    }

    public function imageUploadLabelGrid(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ImageUploadController.imageUploadLabelGrid|request=' . json_encode($request->all()));
        $dimensions = '';
        if (isset($request->width) && isset($request->height)) {
            $dimensions = "|dimensions:width=$request->width,height=$request->height";
            $validate = Validator::make($request->all(), ['image' => 'required|image|mimes:jpeg,png,jpg,gif|max:6144' . $dimensions], ['image.dimensions' => "Photos must be $request->width" . "x" . "$request->height resolution"]);
        } else {

            $validate = Validator::make($request->all(), ['image' => 'required|image|mimes:jpeg,png,jpg,gif|max:6144' . $dimensions]);
        }
        $size = request()->image->getSize();
        Log::info("imageUploadPost size: " . $size);
        if ($validate->passes()) {
            $imageName = $user->user_name . '_' . time() . '.' . request()->image->getClientOriginalExtension();
            request()->image->move(public_path('labelgrid'), $imageName);
            return response()->json([
                        'message' => 'Upload successfull',
                        'uploaded_image' => "/labelgrid/$imageName",
                        'status' => 'success',
            ]);
        } else {
            return response()->json([
                        'message' => $validate->errors()->all(),
                        'uploaded_image' => '',
                        'status' => 'error',
            ]);
        }
    }

    public function downloadDrive(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ImageUploadController.downloadDrive|request=' . json_encode($request->all()));
        preg_match("/[-\w]{25,}/", $request->link, $matches);
        if (count($matches) > 0) {
            $g_id = $matches[0];
            $folder = public_path('uploads');
            $file = $user->user_name . "_drive_" . time() . '.png';
            $full_path = $folder . '/' . $file;
            shell_exec("gdown --output $full_path https://drive.google.com/uc?id=$g_id");
            $check = glob($full_path);
            if (count($check) > 0) {
                return response()->json([
                            'message' => 'Download successfull',
                            'uploaded_image' => "/uploads/$file",
                            'status' => 'success',
                ]);
            } else {
                return response()->json([
                            'message' => "Download drive error, try to share the link",
                            'uploaded_image' => '',
                            'status' => 'error',
                ]);
            }
        }
    }

    public function promoUploadPost(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ImageUploadController.promoUploadPost|request=' . json_encode($request->all()));

//        $this->validate($request, [
//            'promoUpload' => 'required|file|mimes:mp3,wav,aac,txt'
//        ]);
//        if ($request->hasFile('promoUpload')) {
//            $original_name = $request->file('promoUpload')->getClientOriginalName();
//            $extension = $request->file('promoUpload')->getClientOriginalExtension();
//            $uploadName = "$user->user_name-$original_name-" . time() . ".$extension";
//            $path = $request->file('file')->storeAs('public/promos', $uploadName);
//            return response()->json([
//                        'message' => 'Upload successfull',
//                        'uploaded_promo' => "/promos/$uploadName",
//                        'status' => 'success',
//            ]);
//        }
//        return response()->json([
//                    'message' => "Fail",
//                    'uploaded_promo' => '',
//                    'status' => 'error',
//        ]);
//        $mines = "mp3,wav,txt";
//
        $folder = "promos";
        if (isset($request->fd)) {
            $folder = $request->fd;
        }
        $validate = Validator::make($request->all(), ['promoUpload' => "required|file"], ["promoUpload.required" => "Choose a file"]);
        if ($validate->passes()) {

            if ($request->hasFile('promoUpload')) {
                $original_name = $request->file('promoUpload')->getClientOriginalName();
                $extension = $request->file('promoUpload')->getClientOriginalExtension();
                $uploadName = "$user->user_name-" . time() . "-$original_name";
                $uploadName = str_replace(" ", "-", $uploadName);
                $uploadName = Utils::slugify($uploadName);
//            $path = $request->file('promoUpload')->storeAs('public/promos', $uploadName);
                $request->file('promoUpload')->move(public_path("$folder/music/download/"), "$uploadName.$extension");
                return response()->json([
                            'message' => 'Upload successfull',
                            'uploaded_promo' => "$folder/music/download/$uploadName.$extension",
                            'status' => 'success',
                ]);
            }
//            return response()->json([
//                        'message' => 'Upload successfull',
//                        'uploaded_promo' => "/promos/$uploadName",
//                        'status' => 'success',
//            ]);
        } else {
            return response()->json([
                        'message' => $validate->errors()->all(),
                        'uploaded_promo' => '',
                        'status' => 'error',
            ]);
        }
    }

    //tạo ảnh 1920x1080 random
    public function createRandomImage($text) {
        $width = 1920;
        $height = 1080;

        // Tạo ảnh
        $image = imagecreatetruecolor($width, $height);

        // Tạo màu nền ngẫu nhiên
        $bgColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        imagefill($image, 0, 0, $bgColor);

        // Màu chữ tương phản
        $textColor = imagecolorallocate($image, 255 - ($bgColor >> 16 & 0xFF), 255 - ($bgColor >> 8 & 0xFF), 255 - ($bgColor & 0xFF));

        // Chọn font và tính toán vị trí chữ
        $font = public_path('arial.ttf'); // Đảm bảo bạn có file font trong public/fonts/
        $fontSize = rand(60, 100);
        $bbox = imagettfbbox($fontSize, 0, $font, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];
        $x = ($width - $textWidth) / 2;
        $y = ($height + $textHeight) / 2;

        // Vẽ chữ lên ảnh
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $font, $text);

        // Tạo thư mục nếu chưa có
        $path = public_path('check_claim');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Tạo tên file duy nhất
        $fileName = 'image_' . time() . '.png';
        $filePath = $path . '/' . $fileName;

        // Lưu ảnh vào thư mục public/check_claim
        imagepng($image, $filePath);

        // Giải phóng bộ nhớ
        imagedestroy($image);

        return (object) [
                    'message' => 'Image saved successfully',
                    'link' => asset('check_claim/' . $fileName),
                    'file_path' => $filePath
        ];
    }

    public function getUploadImageLink(Request $request) {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                        'filename' => 'required|string',
                        'filesize' => 'required|numeric|max:5120', // Max 5MB in KB
                        'mimetype' => 'required|string|in:image/jpeg,image/jpg,image/png,image/gif,image/webp'
            ]);

            if ($validator->fails()) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Validation failed',
                            'errors' => $validator->errors()
                                ], 422);
            }

            $filename = $request->filename;
            $filesize = $request->filesize; // in KB
            $mimetype = $request->mimetype;

            // Additional validation based on your album validation rules
            if ($filesize > 5120) { // 5MB max
                return response()->json([
                            'status' => 'error',
                            'message' => 'File size must not exceed 5MB'
                                ], 422);
            }

            // Generate unique filename to prevent conflicts
            $extension = $this->getExtensionFromMimeType($mimetype);
            $uniqueFilename = time() . '_' . uniqid() . '.' . $extension;

            // Get upload link from AWS
            list($presignedUrl, $publicUrl) = AWSHelper::getUploadImageLink($uniqueFilename);

            return response()->json([
                        'status' => 'success',
                        'presigned_url' => $presignedUrl,
                        'public_url' => $publicUrl,
                        'filename' => $uniqueFilename
            ]);
        } catch (Exception $e) {
            logger('Upload link error: ' . $e->getMessage());
            return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to generate upload link'
                            ], 500);
        }
    }

    private function getExtensionFromMimeType($mimeType) {
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        return $mimeToExt[$mimeType] ?? 'jpg';
    }

    public function validateImageFile(Request $request) {
        try {
            // Validate uploaded file
            $validator = Validator::make($request->all(), [
                        'image' => [
                            'required',
                            'image',
                            'mimes:jpeg,jpg,png,gif,webp',
                            'max:5120', // 5MB max
                            'dimensions:min_width=300,min_height=300' // Minimum dimensions
                        ]
            ]);

            if ($validator->fails()) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Image validation failed',
                            'errors' => $validator->errors()
                                ], 422);
            }

            $file = $request->file('image');

            // Get image dimensions
            $imageInfo = getimagesize($file->path());
            if (!$imageInfo) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Invalid image file'
                                ], 422);
            }

            list($width, $height) = $imageInfo;

            // Check minimum dimensions (như trong comment HTML: Minimum size: 1400x1400px)
            if ($width < 1400 || $height < 1400) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Image dimensions must be at least 1400x1400 pixels'
                                ], 422);
            }

            return response()->json([
                        'status' => 'success',
                        'message' => 'Image validation passed',
                        'file_info' => [
                            'size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                            'original_name' => $file->getClientOriginalName(),
                            'width' => $width,
                            'height' => $height
                        ]
            ]);
        } catch (Exception $e) {
            logger('Image validation error: ' . $e->getMessage());
            return response()->json([
                        'status' => 'error',
                        'message' => 'Image validation failed'
                            ], 500);
        }
    }

}
