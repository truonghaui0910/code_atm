<?php

namespace App\Common\Network;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class AWSHelper {

    private static $s3Client;
    private static $accountId = "f9c3a8281b5ed069e736fa2108f6f106";

    private static function getS3Client() {
        if (self::$s3Client === null) {
            self::$s3Client = new S3Client([
                'endpoint' => "https://" . self::$accountId . ".r2.cloudflarestorage.com",
                'version' => 'latest',
                'region' => 'auto',
                'credentials' => [
                    'key' => "5bfdf2d1865b988d393b7e5fc33ab2e4",
                    'secret' => "697bcb379abb325b9dec6926ce22250dcd17f6b8ea2afdca2a32993a0dcb8276",
                ]
            ]);
        }
        return self::$s3Client;
    }


    public static function getUploadImageLink($filename, $folder = 'albums') {
        try {
            $s3 = self::getS3Client();
            $bucket = env('AWS_BUCKET');
            $key = $folder . '/' . $filename;

            // Create presigned URL for PUT upload
            $cmd = $s3->getCommand('PutObject', [
                'Bucket' => $bucket,
                'Key' => $key,
//                'ACL' => 'public-read'
            ]);

            $request = $s3->createPresignedRequest($cmd, '+1 hour');
            $presignedUrl = (string) $request->getUri();

            // Public URL
            $publicUrl = 'https://' . $bucket . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $key;
            $newUrl = "https://storage.automusic.win/$key";

            return [$presignedUrl, $newUrl];
        } catch (S3Exception $e) {
            \Log::error('AWS S3 Error: ' . $e->getMessage());
            throw new Exception('Failed to generate upload link');
        }
    }


    public static function uploadFile($filePath, $filename, $folder = 'albums') {
        try {
            $s3 = self::getS3Client();
            $bucket = env('AWS_BUCKET');
            $key = $folder . '/' . $filename;

            $result = $s3->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
                'ACL' => 'public-read'
            ]);

            return 'https://' . $bucket . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $key;
        } catch (S3Exception $e) {
            \Log::error('AWS S3 Upload Error: ' . $e->getMessage());
            throw new Exception('Failed to upload file');
        }
    }


    public static function deleteFile($fileUrl, $folder = 'albums') {
        try {
            $s3 = self::getS3Client();
            $bucket = env('AWS_BUCKET');

            // Extract filename from URL
            $filename = basename($fileUrl);
            $key = $folder . '/' . $filename;

            $s3->deleteObject([
                'Bucket' => $bucket,
                'Key' => $key
            ]);

            return true;
        } catch (S3Exception $e) {
            \Log::error('AWS S3 Delete Error: ' . $e->getMessage());
            return false;
        }
    }


    public static function fileExists($fileUrl, $folder = 'albums') {
        try {
            $s3 = self::getS3Client();
            $bucket = env('AWS_BUCKET');

            $filename = basename($fileUrl);
            $key = $folder . '/' . $filename;

            return $s3->doesObjectExist($bucket, $key);
        } catch (S3Exception $e) {
            return false;
        }
    }

}
