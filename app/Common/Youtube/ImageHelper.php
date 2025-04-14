<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageHelper
 *
 * @author bonni
 */

namespace App\Common\Youtube;

use PHPImageWorkshop;
use Log;

class ImageHelper {

//put your code here
    public static function makeBackground($data, $imagePath) {
        $jsonStyleBgMusic = $data->style_bg_music;
        $styleBgMusic = json_decode($jsonStyleBgMusic);
        $layerBG = PHPImageWorkshop\ImageWorkshop::initFromPath($imagePath);
        $textLayer = PHPImageWorkshop\ImageWorkshop::initTextLayer($styleBgMusic->text_data, $styleBgMusic->font_name, $styleBgMusic->font_size, $styleBgMusic->text_color, $styleBgMusic->text_rotation, null, $styleBgMusic->stroke_color);
        $layerBG->addLayer(1, $textLayer, 0, 0, $styleBgMusic->text_position);
        $image = $layerBG->getResult("ffffff");
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    public static function writeTextOnBackground($is_text_preview, $is_title_song_preview, $imagePath, $style_text, $style_title) {
        Log::info("writeTextOnBackground: $style_text");
        Log::info("writeTextOnBackground2: $style_title");
        $layerBG = PHPImageWorkshop\ImageWorkshop::initFromPath($imagePath);
        if ($is_text_preview == 1) {
            $arr_style_text = json_decode($style_text);
            if ($arr_style_text->text_stroke_size < 1) {
                $textLayer = PHPImageWorkshop\ImageWorkshop::initTextLayer($arr_style_text->text_data, $arr_style_text->font_name, $arr_style_text->font_size, $arr_style_text->font_color, $arr_style_text->rotation);
            } else {
                $textLayer = PHPImageWorkshop\ImageWorkshop::initTextBorderLayer($arr_style_text->text_data, $arr_style_text->font_name, $arr_style_text->font_size, $arr_style_text->font_color, $arr_style_text->rotation, NULL, $arr_style_text->text_stroke_color, $arr_style_text->text_stroke_size);
            }
            $layerBG->addLayer(1, $textLayer, $arr_style_text->pos_x, $arr_style_text->pos_y, $arr_style_text->pos);
        }
        if ($is_title_song_preview == 1) {
            $arr_style_title = json_decode($style_title);
            if ($arr_style_title->text_stroke_size < 1) {
                $textLayer2 = PHPImageWorkshop\ImageWorkshop::initTextLayer($arr_style_title->text_data, $arr_style_title->font_name, $arr_style_title->font_size, $arr_style_title->font_color, $arr_style_title->rotation);
            } else {
                $textLayer2 = PHPImageWorkshop\ImageWorkshop::initTextBorderLayer($arr_style_title->text_data, $arr_style_title->font_name, $arr_style_title->font_size, $arr_style_title->font_color, $arr_style_title->rotation, NULL, $arr_style_title->text_stroke_color, $arr_style_title->text_stroke_size);
            }
            $layerBG->addLayer(2, $textLayer2, $arr_style_title->pos_x, $arr_style_title->pos_y, $arr_style_title->pos);
        }


        $image = $layerBG->getResult("ffffff");
//        $outputFile = PATH_DOWNLOAD . "WTOBG-" . time() . ".jpg";
//        imagejpeg($image, $outputFile);
//        return $outputFile;
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    public static function writeTextOnBackgroundForLyric($is_text_preview, $is_title_song_preview, $imagePath, $style_text, $style_title) {
        Log::info("writeTextOnBackground: $style_text");
        Log::info("writeTextOnBackground2: $style_title");
        $layerBG = PHPImageWorkshop\ImageWorkshop::initFromPath($imagePath);
        if ($is_text_preview == 1) {
            $arr_style_text = json_decode($style_text);
            if ($arr_style_text->text_stroke_size < 1) {
                $textLayer = PHPImageWorkshop\ImageWorkshop::initTextLayer($arr_style_text->text_data, $arr_style_text->font_name, $arr_style_text->font_size, $arr_style_text->font_color, $arr_style_text->rotation);
            } else {
                $textLayer = PHPImageWorkshop\ImageWorkshop::initTextBorderLayer($arr_style_text->text_data, $arr_style_text->font_name, $arr_style_text->font_size, $arr_style_text->font_color, $arr_style_text->rotation, NULL, $arr_style_text->text_stroke_color, $arr_style_text->text_stroke_size);
            }
            $layerBG->addLayer(1, $textLayer, $arr_style_text->pos_x, $arr_style_text->pos_y, $arr_style_text->pos);
        }
        if ($is_title_song_preview == 1) {
            $arr_style_title = json_decode($style_title);
            if ($arr_style_title->text_stroke_size < 1) {
                $textLayer2 = PHPImageWorkshop\ImageWorkshop::initTextLayer($arr_style_title->text_data, $arr_style_title->font_name, $arr_style_title->font_size, $arr_style_title->font_color, $arr_style_title->rotation);
            } else {
                $textLayer2 = PHPImageWorkshop\ImageWorkshop::initTextBorderLayer($arr_style_title->text_data, $arr_style_title->font_name, $arr_style_title->font_size, $arr_style_title->font_color, $arr_style_title->rotation, NULL, $arr_style_title->text_stroke_color, $arr_style_title->text_stroke_size);
            }
            $layerBG->addLayer(2, $textLayer2, $arr_style_title->pos_x, $arr_style_title->pos_y, $arr_style_title->pos);
        }


        $image = $layerBG->getResult("ffffff");
//        $outputFile = PATH_DOWNLOAD . "WTOBG-" . time() . ".jpg";
//        imagejpeg($image, $outputFile);
//        return $outputFile;
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    public static function createGadientImage($start, $end, $type) {
        $x1 = 1920;
        $y1 = 1080;
        $img = imagecreatetruecolor($x1, $y1);
        $x = 0;
        $y = 0;
        switch ($type) {
            case 3:
                $tmp = $start;
                $start = $end;
                $end = $tmp;
                $type = 1;
                break;
            case 4:
                $tmp = $start;
                $start = $end;
                $end = $tmp;
                $type = 2;
                break;
        }
        $s = array(
            hexdec(substr($start, 0, 2)),
            hexdec(substr($start, 2, 2)),
            hexdec(substr($start, 4, 2))
        );
        $e = array(
            hexdec(substr($end, 0, 2)),
            hexdec(substr($end, 2, 2)),
            hexdec(substr($end, 4, 2))
        );
        switch ($type) {
            case 1:
                $steps = $y1 - $y;
                for ($i = 0; $i < $steps; $i++) {
                    $r = $s[0] - ((($s[0] - $e[0]) / $steps) * $i);
                    $g = $s[1] - ((($s[1] - $e[1]) / $steps) * $i);
                    $b = $s[2] - ((($s[2] - $e[2]) / $steps) * $i);
                    $color = imagecolorallocate($img, $r, $g, $b);
                    imagefilledrectangle($img, $x, $y + $i, $x1, $y + $i + 1, $color);
                }
                break;
            case 2:
                $steps = $x1 - $x;
                for ($i = 0; $i < $steps; $i++) {
                    $r = $s[0] - ((($s[0] - $e[0]) / $steps) * $i);
                    $g = $s[1] - ((($s[1] - $e[1]) / $steps) * $i);
                    $b = $s[2] - ((($s[2] - $e[2]) / $steps) * $i);
                    $color = imagecolorallocate($img, $r, $g, $b);
                    imagefilledrectangle($img, $x + $i, $y, $x + $i + 1, $y1, $color);
                }
                break;
        }
//        $uid = uniqid();
//        $output = PATH_DOWNLOAD . "gradient-$uid.jpg";
//        imagejpeg($img, $output);
//        return $output;
        return $img;
    }

    public static function image_gradientrect($img, $x, $y, $x1, $y1, $start, $end) {
        if ($x > $x1 || $y > $y1) {
            return false;
        }
        $s = array(
            hexdec(substr($start, 0, 2)),
            hexdec(substr($start, 2, 2)),
            hexdec(substr($start, 4, 2))
        );
        $e = array(
            hexdec(substr($end, 0, 2)),
            hexdec(substr($end, 2, 2)),
            hexdec(substr($end, 4, 2))
        );
        $steps = $y1 - $y;
        for ($i = 0; $i < $steps; $i++) {
            $r = $s[0] - ((($s[0] - $e[0]) / $steps) * $i);
            $g = $s[1] - ((($s[1] - $e[1]) / $steps) * $i);
            $b = $s[2] - ((($s[2] - $e[2]) / $steps) * $i);
            $color = imagecolorallocate($img, $r, $g, $b);
            imagefilledrectangle($img, $x, $y + $i, $x1, $y + $i + 1, $color);
        }
        return true;
    }

}
