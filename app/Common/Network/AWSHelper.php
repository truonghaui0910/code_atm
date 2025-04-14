<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AWSHelper
 *
 * @author Hoa Bui
 */
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class AWSHelper
{

    private static $accountId = "f9c3a8281b5ed069e736fa2108f6f106";

    public static function init()
    {
        $s3Client = new S3Client([
            'endpoint' => "https://" . self::$accountId . ".r2.cloudflarestorage.com",
            'version' => 'latest',
            'region' => 'auto',
            'credentials' => [
                'key' => '5bfdf2d1865b988d393b7e5fc33ab2e4',
                'secret' => '697bcb379abb325b9dec6926ce22250dcd17f6b8ea2afdca2a32993a0dcb8276',
            ],
        ]);
        return $s3Client;
    }

    public static function uploadFile($filePath, $type, $folder = 'resource', $bucket = 'moonaz')
    {
        $client = self::init();
        $baseName = basename($filePath);
        $keyname = "$folder/$type/$baseName";
        $result = $client->putObject(array(
            'Bucket' => $bucket,
            'Key' => $keyname,
            'SourceFile' => $filePath,
        ));
        $client->waitUntil('ObjectExists', array(
            'Bucket' => $bucket,
            'Key' => $keyname
        ));
        $url = $client->getObjectUrl($bucket, $keyname);
        $newUrl = str_replace("$bucket." . self::$accountId . ".r2.cloudflarestorage.com", "cdn.moonaz.net", $url);
        return $newUrl;
    }

    public static function deleteFile($public_url, $bucket = 'moonaz')
    {
        try {
            $client = self::init();
            // https: //victor-public.s3.ap-southeast-1.amazonaws.com/resource/image/34a0c45e4394c05e.png
            // $keyname = str_replace("https://" . $bucket . ".s3.ap-southeast-1.amazonaws.com/", "", $public_url);
            $keyname = str_replace("https://cdn.cacherecords.net/", "", $public_url);
            $result = $client->deleteObject([
                'Bucket' => $bucket,
                'Key' => $keyname
            ]);
            // if ($result['DeleteMarker']) {
            //     return true;
            // } else {
            //     return false;
            // }
            return true;
        } catch (Exception $ex) {
        }
        return false;
    }

    public static function getUploadLink($fileName, $type, $folder = 'resource', $bucket = 'moonaz')
    {
        $s3Client = self::init();
        $keyname = "$folder/$type/$fileName";
        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $keyname
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+60 minutes')->withMethod('PUT');
        $presignedUrl = (string) $request->getUri();
        $url = $s3Client->getObjectUrl($bucket, $keyname);
        // https://moonaz.0bccb32eafa137e718623dc513358a41.r2.cloudflarestorage.com/resource/image/MoonAZ%20Intro%202.mp4
        $newUrl = str_replace("$bucket." . self::$accountId . ".r2.cloudflarestorage.com", "cdn.moonaz.net", $url);
        return [$presignedUrl, $newUrl];
    }
}
