<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Common\Youtube;
/**
 * Description of VideoInfo
 *
 * @author bonni
 */
class VideoInfo {

    //put your code here
    public $id;
    public $publishDate;
    public $status;
    public $copyright;
    public $lengthVideo;
    public $resVideo;
    public $title;
    public $length;
    public $duration;
    public $like;
    public $dislike;
    public $views;
    public $type;
    public $artist;
    function __construct($id, $title = null) {
        $this->id = $id;
        $this->title = $title;
    }

    public function idString() {
        return $this->id;
    }

    //old to new
    public static function asc_pd($a, $b) {
        return $a->publishDate > $b->publishDate;
    }

    //new to old
    public static function desc_pd($a, $b) {
        return $a->publishDate < $b->publishDate;
    }

    //old to new
    public static function asc_view($a, $b) {
        return $a->views > $b->views;
        //strcmp($a->views, $b->views);
    }

    //new to old
    public static function desc_view($a, $b) {
        return $b->views > $a->views;
        // return strcmp($b->views, $a->views);
    }

    //old to new
    public static function asc_like($a, $b) {
        return $a->like > $b->like;
    }

    //new to old
    public static function desc_like($a, $b) {
        return $a->like < $b->like;
    }

    //old to new
    public static function asc_dislike($a, $b) {
        return $a->dislike > $b->dislike;
    }

    //new to old
    public static function desc_dislike($a, $b) {
        return $a->dislike < $b->dislike;
    }

}