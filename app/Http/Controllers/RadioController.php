<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Common\Youtube\YoutubeHelper;
use App\Common\Youtube\DeezerHelper;
use App\Common\Youtube\ImageHelper;
use App\Http\Models\MusicConfig;
use App\Http\Models\Spotifymusic;
use App\Http\Models\AccountInfo;
use App\Http\Models\Font;
use App\Common\Utils;
use App\Common\Network\RequestHelper;
use PHPImageWorkshop;
use Log;

class RadioController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.index');
        $offset = 0;
        if ($request->offset) {
            $offset = $request->offset;
        }
        $link = '';
        if ($request->data) {
            $link = $request->data;
        }
        $type = 'deezer';
        if ($request->type) {
            $type = $request->type;
        }
        $lyric = '';
        $id_deezer = '';
        if ($type == "local" && $link != '') {
            $res = RequestHelper::getUrl("http://cdn.soundhex.com/api/v1/timestamp/$link", 0);
            Log::info($res);
            $dataLyric = json_decode($res);
            $lyric = $dataLyric->lyric_sync;
            $link = "http://lyric.automusic.win/src/uploads/$dataLyric->url_128";
        } else {
            $id_deezer = $link;
            if ($id_deezer != '') {
                $link = "https://www.deezer.com/en/track/$link";
            }
        }

        $listTopicImg = DB::select('select topic  from image where type = 1 group by topic order by topic');
        $listImg = DB::select('select id,link  from image where type = 2 limit 30 offset ' . $offset);
        $listFontText = DB::select('select name,link  from font where status = 1 and type = 1 order by name');
        $listFontLyric = DB::select('select id,link,link_bold,name  from font where status = 1 and type = 3 order by name');
        $listGradient = DB::select('select name,color1,color2  from music_color_gradient where status = 1 order by name');
        $listArtistsImg = DB::select('select artists  from image where type = 2 group by artists order by artists');
        $listArtistsImgOther = DB::select('select topic  from image where type = 1 group by topic order by topic');
        $listArtistsImgLyric = DB::select('select topic  from image where type = 6 group by topic order by topic');
        $listTopicMusic = DB::select('select topic  from music_storage where status = 1 group by topic order by topic');
        $listArtistsMusic = DB::select('select artists  from music_storage where status = 1 group by artists order by artists');
        return view('components.radioconfig', [
            'list_categoty' => $this->loadCategory($request),
            'list_location' => $this->loadLocation($request),
            'list_language' => $this->loadLanguage($request),
            'list_outro' => $this->genOutro($user),
            'list_image' => $listImg,
            'list_topic' => $listTopicImg,
            'list_font_text' => $listFontText,
            'list_font_lyric' => $listFontLyric,
            'list_topic_music' => $listTopicMusic,
            'list_artists' => $listArtistsMusic,
            'list_artists_img' => $listArtistsImg,
            'list_artists_img_other' => $listArtistsImgOther,
            'list_artists_img_lyric' => $listArtistsImgLyric,
            'list_gradient' => $listGradient,
            'datas' => $this->loadDataForReupConfig($request),
            'link' => $link,
            'type' => $type,
            'lyric' => $lyric,
            'id_deezer' => $id_deezer,
        ]);
    }

    public function ajax(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.ajax|request=' . json_encode($request->all()));
//        Log::info(implode("','", $request->datas));
//        $offset = 0;
//        $sql = 'select id,topic,link  from image where type = 1 ';
//        if (isset($request->topic) && $request->topic != 'all') {
//            $sql .= " and topic = '" . $request->topic . "'";
//        }
//
//        if (isset($request->offset)) {
//            $offset = $request->offset;
//            $sql .= ' limit 30 offset ' . $offset;
//        }
//        $listLink = DB::select($sql);
//        Log::info(json_encode($listLink));
//        Log::info(DB::getQueryLog());
        $datas = $this->loadDataImage($request);
//        Log::info(json_encode($datas));
        return $datas;
    }

    public function create() {
        //
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.store|request=' . json_encode($request->all()));
        $content = array();
        $separate = Config::get('config.separate_text');
        $status = "danger";
        try {

            $channelId = "UCH5YbeeoQjkxk089xEMCxwA";
            $is_download = 1;
            if ($request->type_reup == 2) {
                $is_download = 0;
                //chọn kênh
                if (!isset($request->channel) || $request->channel == '-1') {
                    array_push($content, trans('label.message.notChannel'));
                } else {
                    $channelId = $request->channel;
                }
            }

            $music_type = '3';
            $source_type = '1';

            $is_get_metadata = 0;

            // <editor-fold defaultstate="collapsed" desc="source">
            $arrSourceProcess = array();
            if ($source_type == 1) {
                //nguon ngoai db
                if (!isset($request->source)) {
                    array_push($content, trans('label.messageEnterLink'));
                } else {
                    $txtSource = str_replace(array("\r\n", "\n"), $separate, trim($request->source));
                    $arraySource = explode($separate, $txtSource);
                    if (count($arraySource) > 1) {
                        array_push($content, "You have to input 1 link");
                        return array('status' => $status, 'content' => $content);
                    }
                    $arrKey = array();
                    $arrPlaylist = array();
                    $arrayChannel = array();
                    $arrayVideo = array();
                    $arrayDeezer = array();
                    $arrayDirect = array();
                    foreach ($arraySource as $source) {
                        if ($request->type_lyric_source == 'local') {
                            $pos = strpos($source, 'https://www.deezer.com');
                            if ($pos === false) {
                                array_push($arrayDirect, trim($source));
                            } else {
                                array_push($arrayDeezer, trim($source));
                            }
                        } else {

                            $pos = strpos($source, 'https://www.deezer.com');
                            if ($pos === false) {
                                array_push($content, "You have to enter deezer Id");
                                return array('status' => $status, 'content' => $content);
//                            $check = YoutubeHelper::processLink($source);
//                            if ($check['type'] == '0') {
//                                array_push($arrKey, $check['data']);
//                            } else if ($check['type'] == '1') {
//                                array_push($arrPlaylist, $check['data']);
//                            } else if ($check['type'] == '2') {
//                                array_push($arrayChannel, $check['data']);
//                            } else if ($check['type'] == '3') {
//                                array_push($arrayVideo, $check['data']);
//                            }
                            } else {
                                array_push($arrayDeezer, trim($source));
                            }
                        }
                    }

                    if (count($arrayDeezer) > 0) {
                        array_push($arrSourceProcess, array('type' => 7, 'data' => $arrayDeezer));
                    }
                    if (count($arrayVideo) > 0) {
                        array_push($arrSourceProcess, array('type' => 3, 'data' => $arrayVideo));
                    }
                    if (count($arrayChannel) > 0) {
                        array_push($arrSourceProcess, array('type' => 2, 'data' => $arrayChannel));
                    }
                    if (count($arrPlaylist) > 0) {
                        array_push($arrSourceProcess, array('type' => 1, 'data' => $arrPlaylist));
                    }
                    if (count($arrKey) > 0) {
                        array_push($arrSourceProcess, array('type' => 0, 'data' => $arrKey));
                    }
                    if (count($arrayDirect) > 0) {
                        array_push($arrSourceProcess, array('type' => 8, 'data' => $arrayDirect));
                    }
                }
            }
//            Log::info(json_encode($arrSourceProcess));
            // </editor-fold>
            // // <editor-fold defaultstate="collapsed" desc="background">
            //mix bacground
            $background_mix_type = null;
            $list_image_background_mix = '';
            $list_image_background = '';
            $background_template = '';
            $backgound_type = 0;
            if ($request->background_type == 2) {
                if (isset($request->background)) {
                    if (count($request->background) == 0) {
                        array_push($content, 'You did not select background image');
                    } else {
                        $list_image_background = json_encode($request->background);
                    }
                } else {
                    array_push($content, 'You did not select background image');
                }
            }

            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="style on background">

            if (isset($request->ck_style_text)) {
                if (!isset($request->text_data)) {
                    array_push($content, 'You did not enter Text');
                }
                if (!isset($request->font_name)) {
                    array_push($content, 'You did not enter Font');
                }
                if (!isset($request->font_size)) {
                    array_push($content, 'You did not enter Text size');
                }
                $arrayTextPos = array("LT", "MT", "RT", "LM", "MM", "RM", "LB", "MB", "RB");
                if (!isset($request->pos)) {
                    array_push($content, 'You did not enter Text position');
                } else {
                    if (!in_array($request->pos, $arrayTextPos)) {
                        array_push($content, 'Text Position invalid');
                    }
                }
                if (!isset($request->font_color)) {
                    array_push($content, 'You did not enter Text color');
                }
                if (!isset($request->pos_x)) {
                    array_push($content, 'You did not enter Pos x');
                }
                if (!isset($request->pos_y)) {
                    array_push($content, 'You did not enter Pos y');
                }

                if (!isset($request->rotation)) {
                    array_push($content, 'You did not enter Text rotation');
                } else {
                    if ($request->rotation < -180 || $request->text_rotation > 180) {
                        array_push($content, 'Text rotation must be in [-180 -> 180]');
                    }
                }
            }

            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="sort">

            $sort_object['view'] = null;
            $sort_object['like'] = null;
            $sort_object['dislike'] = null;
            $sort_object['pd'] = null;
            $sort = json_encode($sort_object);
            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="title">
            //cấu hình tiêu đề
            $title_conf["add_begin"] = null;
            $title_conf["add_end"] = null;
            $title_conf["replace_from"] = null;
            $title_conf["replace_to"] = null;
            $title_conf["replace_all"] = null;
//            Log::info(json_encode($title_conf));

            $add_increase_title_prefix = "";
            $add_increase_title_start = -1;
            $add_type_increase_title = 0;
            if (isset($request->ck_replace_title)) {
                //thay thế 1 phần
                if ($request->ck_replace_title == 1) {
                    if (isset($request->ck_title_add_begin)) {
                        if (isset($request->txt_title_replace_first) && $request->txt_title_replace_first != '') {
                            $title_conf["add_begin"] = $request->txt_title_replace_first;
                        }
                    }
                    if (isset($request->ck_title_add_end)) {
                        if (isset($request->txt_title_replace_last) && $request->txt_title_replace_last != '') {
                            $title_conf["add_end"] = $request->txt_title_replace_last;
                        }
                    }
                    if (isset($request->ck_title_replace)) {
                        if (isset($request->txt_title_replace_from) && $request->txt_title_replace_from != '') {
                            $title_conf["replace_from"] = $request->txt_title_replace_from;
                        }
                        if (isset($request->txt_title_replace_to) && $request->txt_title_replace_to != '') {
                            $title_conf["replace_to"] = $request->txt_title_replace_to;
                        }
                    }
                } else if ($request->ck_replace_title == 2) {
                    //thay thế tất cả
                    if (isset($request->txt_title_replace_all)) {
                        $replace_all_tit = $request->txt_title_replace_all;
                        if ($replace_all_tit == null) {
                            $replace_all_tit = ' ';
                        }
                        $title_conf["replace_all"] = $replace_all_tit;
                    }
                }

                if (isset($request->ck_auto_increament)) {
                    $add_type_increase_title = 1;
                    if (isset($request->auto_increament_prefix) && $request->auto_increament_prefix != '') {
                        $add_increase_title_prefix = $request->auto_increament_prefix;
                    }
                    if (isset($request->auto_increament_count) && $request->auto_increament_count != '') {
                        $add_increase_title_start = $request->auto_increament_count;
                    }
                }
            }
            // // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="des">
            //cấu hình mô tả
            $ck_des_remove_link = 0;
            $des_conf["add_begin"] = null;
            $des_conf["add_end"] = null;
            $des_conf["replace_from"] = null;
            $des_conf["replace_to"] = null;
            $des_conf["replace_all"] = null;
            $des_conf["remove_link"] = $ck_des_remove_link;
            if (isset($request->ck_replace_des)) {
                //thay thế 1 phần
                if ($request->ck_replace_des == 1) {
                    if (isset($request->ck_des_add_begin)) {
                        if (isset($request->txt_des_replace_first) && $request->txt_des_replace_first != '') {
                            $des_conf["add_begin"] = $request->txt_des_replace_first;
                        }
                    }
                    if (isset($request->ck_des_add_end)) {
                        if (isset($request->txt_des_replace_last) && $request->txt_des_replace_last != '') {
                            $des_conf["add_end"] = $request->txt_des_replace_last;
                        }
                    }
                    if (isset($request->ck_des_replace)) {
                        if (isset($request->txt_des_replace_from) && $request->txt_des_replace_from != '') {
                            $des_conf["replace_to"] = $request->txt_des_replace_from;
                        }
                        if (isset($request->txt_des_replace_to) && $request->txt_des_replace_to != '') {
                            $des_conf["replace_to"] = $request->txt_des_replace_to;
                        }
                    }
                    if (isset($request->ck_des_remove_link)) {
                        $des_conf["remove_link"] = 1;
                    }
                } else if ($request->ck_replace_des == 2) {
                    //thay thế tất cả
                    if (isset($request->txt_des_replace_all)) {
                        $replace_all_des = $request->txt_des_replace_all;
                        if ($replace_all_des == null) {
                            $replace_all_des = ' ';
                        }
                        $des_conf["replace_all"] = $replace_all_des;
                    }
                }
            }

            // // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="tag">
            //cấu hình tag

            $tag_conf["add_begin"] = null;
            $tag_conf["add_end"] = null;
            $tag_conf["replace_from"] = null;
            $tag_conf["replace_to"] = null;
            $tag_conf["replace_all"] = null;
            if (isset($request->ck_replace_tag)) {
                //thay thế 1 phần
                if ($request->ck_replace_tag == 1) {
                    if (isset($request->ck_tag_add_begin)) {
                        if (isset($request->txt_tag_replace_first) && $request->txt_tag_replace_first != '') {
                            $tag_conf["add_begin"] = $request->txt_tag_replace_first;
                        }
                    }
                    if (isset($request->ck_tag_add_end)) {
                        if (isset($request->txt_tag_replace_last) && $request->txt_tag_replace_last != '') {
                            $tag_conf["add_end"] = $request->txt_tag_replace_last;
                        }
                    }
                    if (isset($request->ck_tag_replace)) {
                        if (isset($request->txt_tag_replace_from) && $request->txt_tag_replace_from != '') {
                            $tag_conf["replace_from"] = $request->txt_tag_replace_from;
                        }
                        if (isset($request->txt_tag_replace_to) && $request->txt_tag_replace_to != '') {
                            $tag_conf["replace_to"] = $request->txt_tag_replace_to;
                        }
                    }
                } else if ($request->ck_replace_tag == 2) {
                    //thay thế tất cả
                    if (isset($request->txt_tag_replace_all)) {
                        $replace_all_tag = $request->txt_tag_replace_all;
                        if ($replace_all_tag == null) {
                            $replace_all_tag = ' ';
                        }
                        $tag_conf["replace_all"] = $replace_all_tag;
                    }
                }
            }

            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="validate nhac title,tag,des nhac mix, nguon trong db">
//            if ($title_conf["add_begin"] == null && $title_conf["add_end"] == null && $title_conf["replace_all"] == null) {
//                array_push($content, 'You must enter title');
//            }
//            if ($des_conf["add_begin"] == null && $des_conf["add_end"] == null && $des_conf["replace_all"] == null) {
//                array_push($content, 'You must enter description');
//            }
//            if ($tag_conf["add_begin"] == null && $tag_conf["add_end"] == null && $tag_conf["replace_all"] == null) {
//                array_push($content, 'You must enter tag');
//            }
// // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="nang cao">
            $cool_down_subscribe = 7 * 86400;
            if (isset($request->cool_down_subscribe)) {
                $cool_down_subscribe = $request->cool_down_subscribe * 86400;
            }
            $ck_is_subscribe = 1;
            if (isset($request->ck_is_subscribe)) {
                $ck_is_subscribe = $request->ck_is_subscribe;
            }

            //lặp lại tiêu đề trong mô tả
            $multiple_title = 0;
            if (isset($request->ck_active_multiple_title)) {
                $multiple_title = $request->multiple_title;
            }

            //copy thumb
            $waterMarkType = 0;
            $waterMarkImageUrl = "";
            $is_copy_thumbnail = 0;
            if (isset($request->ck_is_copy_thumbnail)) {
                $waterMarkType = $request->waterMarkType;
                $waterMarkImageUrl = $request->waterMarkImageUrl;
                $is_copy_thumbnail = 1;
            }

            //dịch ngôn ngữ
            $translate_language = "";
            $is_translate = 0;
            if (isset($request->ck_is_translate)) {
                $translate_language = $request->translate_language;
                $is_translate = 1;
            }

            //Thêm video intro 
            $radioVideoIntro = 0;
            $addIntro = "";
            if (isset($request->ck_is_videointro)) {
                //1:thêm vào đầu, 0:thêm vào cuối
                $radioVideoIntro = 1;
                if (isset($request->radioVideoIntro)) {
                    $radioVideoIntro = $request->radioVideoIntro;
                }
                $addIntro = $request->addIntro;
            }

            //trạng thái
            $privacy_status = "public";
            if (isset($request->privacy_status)) {
                $privacy_status = $request->privacy_status;
            }

            //Vị trí
            $location_data = "1";
            if (isset($request->location_data)) {
                $location_data = $request->location_data;
            }

            //Ngôn ngữ
            $language_data = "1";
            if (isset($request->language_data)) {
                $language_data = $request->language_data;
            }

            //sắp xếp
            $cbbPriority = 1;
            if (isset($request->cbbPriority)) {
                $cbbPriority = $request->cbbPriority;
            }

            //tự động seo
            $ck_isAutoSeo = 0;
            if (isset($request->ck_isAutoSeo)) {
                $ck_isAutoSeo = $request->ck_isAutoSeo;
            }
// // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="filter">
            $ck_filter = 0;
            if (isset($_POST['ck_filter'])) {
                $ck_filter = $_POST['ck_filter'];
            }
            //tạo regex lọc video
            $filter = array();
            $filter['filterPd_Op'] = null;
            $filter['filterPd_value'] = null;
            $filter['filterTime_Op'] = null;
            $filter['filterTime_value'] = null;
            $filter['filterView_Op'] = null;
            $filter['filterView_value'] = null;
            $filter['filterLike_Op'] = null;
            $filter['filterLike_value'] = null;
            $filter['filterDislike_Op'] = null;
            $filter['filterDislike_value'] = null;
            if ($ck_filter == 1) {
                $is_get_metadata = 1;
                if (isset($_POST['ck_filter_date'])) {
                    if (!isset($_POST['cbbFilterPdOp'])) {
                        array_push($content, trans('label.message.notFilterPd'));
                    } else {
                        $cbbFilterPdOp = $_POST['cbbFilterPdOp'];
                        if ($cbbFilterPdOp != '>' && $cbbFilterPdOp != '<' && $cbbFilterPdOp != '=') {
                            array_push($content, trans('label.message.filterPdInvalid'));
                        } else {
                            $filter['filterPd_Op'] = $cbbFilterPdOp;
                        }
                    }
                    if (!isset($_POST['cbbFilterPdMoth'])) {
                        array_push($content, trans('label.message.notFilterTimeMonth'));
                    } else {
                        $cbbFilterPdMoth = $_POST['cbbFilterPdMoth'];
                        if (!preg_match('/^(0[0-9]|1[012])$/', $cbbFilterPdMoth)) {
                            array_push($content, trans('label.message.filterTimeMonthInvalid'));
                        }
                    }
                    if (!isset($_POST['cbbFilterPdYear'])) {
                        array_push($content, trans('label.message.notFilterTimeYear'));
                    } else {
                        $cbbFilterPdYear = $_POST['cbbFilterPdYear'];
                        if (!preg_match('/^(20\\d+)$/', $cbbFilterPdYear)) {
                            array_push($content, trans('label.message.filterTimeYearInvalid'));
                        }
                    }
                    $c = count($content);
                    if ($c == 0) {
                        $filter['filterPd_value'] = $cbbFilterPdYear . $cbbFilterPdMoth;
                    }
                }

                if (isset($_POST['ck_filter_time'])) {
                    if (!isset($_POST['cbbFilterTimeOp'])) {
                        array_push($content, trans('label.message.notFilterTimeOp'));
                    } else {
                        $cbbFilterTimeOp = $_POST['cbbFilterTimeOp'];
                        if ($cbbFilterTimeOp != '>' && $cbbFilterTimeOp != '<' && $cbbFilterTimeOp != '=') {
                            array_push($content, trans('label.message.filterTimeOpInvalid'));
                        } else {
                            $filter['filterTime_Op'] = $cbbFilterTimeOp;
                        }
                    }
                    if (!isset($_POST['txtFilterTime'])) {
                        array_push($content, trans('label.message.notFilterTimeValue'));
                    } else {
                        $txtFilterTime = $_POST['txtFilterTime'];
                        if (!preg_match('/^(\\d+)$/', $txtFilterTime)) {
                            array_push($content, trans('label.message.filterTimeVauleInvalid'));
                        } else {
                            $filter['filterTime_value'] = $txtFilterTime;
                        }
                    }
                }

                if (isset($_POST['ck_filter_view'])) {
                    if (!isset($_POST['cbbFilterViewOp'])) {
                        array_push($content, trans('label.message.notFilterViewOp'));
                    } else {
                        $cbbFilterViewOp = $_POST['cbbFilterViewOp'];
                        if ($cbbFilterViewOp != '>' && $cbbFilterViewOp != '<' && $cbbFilterViewOp != '=') {
                            array_push($content, trans('label.message.filterViewOpInvalid'));
                        } else {
                            $filter['filterView_Op'] = $cbbFilterViewOp;
                        }
                    }
                    if (!isset($_POST['txtFilterView'])) {
                        array_push($content, trans('label.message.notFilterViewValue'));
                    } else {
                        $txtFilterView = $_POST['txtFilterView'];
                        if (!preg_match('/^(\\d+)$/', $txtFilterView)) {
                            array_push($content, trans('label.message.filterViewVauleInvalid'));
                        } else {
                            $filter['filterView_value'] = $txtFilterView;
                        }
                    }
                }
                if (isset($_POST['ck_filter_like'])) {
                    if (!isset($_POST['cbbFilterLikeOp'])) {
                        array_push($content, trans('label.message.notFilterLikeOp'));
                    } else {
                        $cbbFilterLikeOp = $_POST['cbbFilterLikeOp'];
                        if ($cbbFilterLikeOp != '>' && $cbbFilterLikeOp != '<' && $cbbFilterLikeOp != '=') {
                            array_push($content, trans('label.message.filterLikeOpInvalid'));
                        } else {
                            $filter['filterLike_Op'] = $cbbFilterLikeOp;
                        }
                    }
                    if (!isset($_POST['txtFilterLike'])) {
                        array_push($content, trans('label.message.notFilterLikeValue'));
                    } else {
                        $txtFilterLike = $_POST['txtFilterLike'];
                        if (!preg_match('/^(\\d+)$/', $txtFilterLike)) {
                            array_push($content, trans('label.message.filterLikeVauleInvalid'));
                        } else {
                            $filter['filterLike_value'] = $txtFilterLike;
                        }
                    }
                }

                if (isset($_POST['ck_filter_dislike'])) {
                    if (!isset($_POST['cbbFilterDislikeOp'])) {
                        array_push($content, trans('label.message.notFilterDislikeOp'));
                    } else {
                        $cbbFilterDislikeOp = $_POST['cbbFilterDislikeOp'];
                        if ($cbbFilterDislikeOp != '>' && $cbbFilterDislikeOp != '<' && $cbbFilterDislikeOp != '=') {
                            array_push($content, trans('label.message.filterDislikeOpInvalid'));
                        } else {
                            $filter['filterDislike_Op'] = $cbbFilterDislikeOp;
                        }
                    }
                    if (!isset($_POST['txtFilterDislike'])) {
                        array_push($content, trans('label.message.notFilterDislikeValue'));
                    } else {
                        $txtFilterDislike = $_POST['txtFilterDislike'];
                        if (!preg_match('/^(\\d+)$/', $txtFilterDislike)) {
                            array_push($content, trans('label.message.filterDislikeVauleInvalid'));
                        } else {
                            $filter['filterDislike_value'] = $txtFilterDislike;
                        }
                    }
                }
            }
            $metaFilter = json_encode($filter);
// </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="config auto upload">
            //cắt đầu
            $txtCutHeadFrom = 0;
//            if (!isset($request['txtCutHeadFrom']) || $request['txtCutHeadFrom'] == null || $request['txtCutHeadFrom'] == '') {
//                array_push($content, trans('label.titleCutHead') . ' ' . trans('label.messageBEthan0'));
//            } else {
//                $txtCutHeadFrom = $request['txtCutHeadFrom'];
//                if (!preg_match('/\\d+/', $txtCutHeadFrom) || $txtCutHeadFrom < 0) {
//                    array_push($content, trans('label.titleCutHead') . ' ' . trans('label.messageBEthan0'));
//                }
//            }
            $txtCutHeadTo = 0;
//            if (!isset($request['txtCutHeadTo']) || $request['txtCutHeadTo'] == null || $request['txtCutHeadTo'] == '') {
//                array_push($content, trans('label.titleCutHead') . ' ' . trans('label.messageBEthan0'));
//            } else {
//                $txtCutHeadTo = $request['txtCutHeadTo'];
//                if (!preg_match('/\\d+/', $txtCutHeadTo) || $txtCutHeadTo < 0) {
//                    array_push($content, trans('label.titleCutHead') . ' ' . trans('label.messageBEthan0'));
//                }
//            }
            //cắt đít
            $txtCutEndFrom = 0;
//            if (!isset($request['txtCutEndFrom']) || $request['txtCutEndFrom'] == null || $request['txtCutEndFrom'] == '') {
//                array_push($content, trans('label.titleCutEnd') . ' ' . trans('label.messageBEthan0'));
//            } else {
//                $txtCutEndFrom = $request['txtCutEndFrom'];
//                if (!preg_match('/\\d+/', $txtCutEndFrom) || $txtCutEndFrom < 0) {
//                    array_push($content, trans('label.titleCutEnd') . ' ' . trans('label.messageBEthan0'));
//                }
//            }
            $txtCutEndTo = 0;
//            if (!isset($request['txtCutEndTo']) || $request['txtCutEndTo'] == null || $request['txtCutEndTo'] == '') {
//                array_push($content, trans('label.titleCutEnd') . ' ' . trans('label.messageBEthan0'));
//            } else {
//                $txtCutEndTo = $request['txtCutEndTo'];
//                if (!preg_match('/\\d+/', $txtCutEndTo) || $txtCutEndTo < 0) {
//                    array_push($content, trans('label.titleCutEnd') . ' ' . trans('label.messageBEthan0'));
//                }
//            }
            //lấy từ video
//            $txtVideoFrom = 1;
//            if (!isset($request['txtVideoFrom']) || $request['txtVideoFrom'] == null || $request['txtVideoFrom'] == '') {
//                array_push($content, trans('label.titleVideoFrom') . ' ' . trans('label.messageBthan0'));
//            } else {
//                $txtVideoFrom = $request['txtVideoFrom'];
//                if (!preg_match('/\\d+/', $txtVideoFrom) || $txtVideoFrom < 1) {
//                    array_push($content, trans('label.titleVideoFrom') . ' ' . trans('label.messageBthan0'));
//                }
//            }
//
//            //lấy đến video
//            $txtVideoTo = 0;
//            if (!isset($request['txtVideoTo']) || $request['txtVideoTo'] == null || $request['txtVideoTo'] == '') {
//                array_push($content, trans('label.titleVideoTo') . ' ' . trans('label.messageBEthan0'));
//            } else {
//                $txtVideoTo = $request['txtVideoTo'];
//                if (!preg_match('/\\d+/', $txtVideoTo) || $txtVideoTo < 0) {
//                    array_push($content, trans('label.titleVideoTo') . ' ' . trans('label.messageBEthan0'));
//                }
//            }
            //thời gian dãn cách upload
            $txtCooldownFrom = 1;
            if (!isset($request['txtCooldownFrom']) || $request['txtCooldownFrom'] == null || $request['txtCooldownFrom'] == '') {
                array_push($content, trans('label.titleTimeFrom') . ' ' . trans('label.messageBthan0'));
            } else {
                $txtCooldownFrom = $request['txtCooldownFrom'];
                if (!preg_match('/\\d+/', $txtCooldownFrom) || $txtCooldownFrom < 1) {
                    array_push($content, trans('label.titleTimeFrom') . ' ' . trans('label.messageBthan0'));
                }
            }
            //thời gian dãn cách upload
            $txtCooldownTo = 2;
            if (!isset($request['txtCooldownTo']) || $request['txtCooldownTo'] == null || $request['txtCooldownTo'] == '') {
                array_push($content, trans('label.titleTimeToFull') . ' ' . trans('label.messageBthan0'));
            } else {
                $txtCooldownTo = $request['txtCooldownTo'];
                if (!preg_match('/\\d+/', $txtCooldownTo) || $txtCooldownTo < 1) {
                    array_push($content, trans('label.titleTimeToFull') . ' ' . trans('label.messageBthan0'));
                }
            }
// </editor-fold>

            $music_text_from = '';
            $music_text_to = '';
            if (isset($request->ck_mixmusic_option)) {
                if (isset($request->ck_music_text)) {
                    if (isset($request->music_text_from) && $request->music_text_from != '') {
                        $music_text_from = $request->music_text_from;
                    }
                    if (isset($request->music_text_to)) {
                        $music_text_to = $request->music_text_to;
                    }
                }
            }




            if (count($content) != 0) {
                $status = "danger";
            } else {
                $musicConfig = new MusicConfig();
                $status = "success";

                $musicConfig->user_name = $user->user_name;
                $musicConfig->status = 0;
                $musicConfig->next_time_run = time();
                $musicConfig->channel_id = $channelId;
                $accountInfo = AccountInfo::where('chanel_id', $channelId)->where('status', 1)->where('del_status', 0)->first();
                $musicConfig->channel_name = $accountInfo->chanel_name;
                $musicConfig->source_type = $source_type;
                $musicConfig->type = $music_type;
//                $musicConfig->group_type = $request->group_mix_music;
                $musicConfig->source = json_encode($arrSourceProcess);
                $musicConfig->is_get_metadata = $is_get_metadata;
                $musicConfig->filter = $metaFilter;
                $musicConfig->sort = $sort;
//                $musicConfig->choose_video_option = $choose_video_option;
//                $musicConfig->choose_video_number = $choose_video_number;
                $musicConfig->backgound_type = $backgound_type;
                $musicConfig->list_image_background = $list_image_background;
                $musicConfig->background_mix_type = $background_mix_type;
                $musicConfig->list_image_background_mix = $list_image_background_mix;
                $musicConfig->background_template = $background_template;
                $musicConfig->music_text_from = $music_text_from;
                $musicConfig->music_text_to = $music_text_to;
                $musicConfig->multiple_title = $multiple_title;
                $musicConfig->is_translate = $is_translate;
                $musicConfig->translate_language = $translate_language;
                $musicConfig->is_copy_thumbnail = $is_copy_thumbnail;
                $musicConfig->water_mark_type = $waterMarkType;
                $musicConfig->water_mark_image_url = $waterMarkImageUrl;
                $musicConfig->intro_type = $radioVideoIntro;
                $musicConfig->intro_data = $addIntro;
                $musicConfig->location_data = $location_data;
                $musicConfig->language_data = $language_data;
                $musicConfig->cate_id = 10;
                $musicConfig->privacy_status = $privacy_status;
                $musicConfig->cut_begin = $txtCutHeadFrom * 60 + $txtCutHeadTo;
                $musicConfig->cut_end = $txtCutEndFrom * 60 + $txtCutEndTo;

                $musicConfig->cool_down_upload = $txtCooldownFrom . "," . $txtCooldownTo;
                $musicConfig->create_time = time();
                $musicConfig->is_auto_seo = $ck_isAutoSeo;
                $musicConfig->subscribe_channel = $ck_is_subscribe;

                $musicConfig->title_conf = json_encode($title_conf);
                $musicConfig->des_conf = json_encode($des_conf);
                $musicConfig->tag_conf = json_encode($tag_conf);

                $musicConfig->add_increase_title_prefix = $add_increase_title_prefix;
                $musicConfig->add_increase_title_start = $add_increase_title_start;
                $musicConfig->inited = 0;
                $musicConfig->cool_down_subscribe = $cool_down_subscribe;

                $musicConfig->music_text_from = $music_text_from;
                $musicConfig->music_text_to = $music_text_to;
                $musicConfig->title_on_background = $request->text_data;
                $musicConfig->is_download = $is_download;
                if (isset($request->ck_style_text)) {
                    $style = array(
                        "font_name" => $request->font_name,
                        "font_size" => $request->font_size,
                        "font_color" => str_replace("#", "", $request->font_color),
                        "pos_x" => $request->pos_x,
                        "pos_y" => $request->pos_y,
                        "pos" => $request->pos,
                        "rotation" => $request->rotation,
                        "text_stroke_size" => $request->text_stroke_size,
                        "text_stroke_color" => $request->text_stroke_color
                    );
                    $musicConfig->title_on_background_style = json_encode($style);
                }

                if (isset($request->ck_style_lyric)) {
                    $font = Font::find($request->font_name_lyric);
                    $style_lyric = array(
                        "bg_type" => $request->background_type,
                        "g_color_s" => str_replace("#", "", $request->g_color_s),
                        "g_color_e" => str_replace("#", "", $request->g_color_e),
                        "g_type" => $request->g_type,
                        "text_color" => str_replace("#", "", $request->font_color_lyric),
                        "group_number" => $request->number_line_lyric,
                        "font_regular" => $font->link,
                        "font_bold" => $font->link_bold
                    );
                    $musicConfig->lyric_style = json_encode($style_lyric);
                } else {
                    $musicConfig->lyric_style = 'random';
                }

                //kiểm tra lyric có tồn tại không, có 3 nguồn lyric
                //clone từ deezer
                //từ tool make lyric dùng nhạc trên deezer
                //từ tool make lyric dùng nhạc local
                if ($request->type_lyric_source == 'local') {
                    if ($request->lyric_source_from_tool_make_lyric != '') {
                        $musicConfig->lyric_sync = $request->lyric_source_from_tool_make_lyric;
                        $musicConfig->has_lyric = 1;
                    }
                } else {
                    if ($request->id_deezer != '') {
                        $idDezer = $request->id_deezer;
                    } else {
                        $deezer = DeezerHelper::getTrackByLink($request->source);
                        $idDezer = $deezer[0]->id;
                    }
                    Log::info(json_encode($idDezer));
                    $res = RequestHelper::getUrl("http://54.39.49.17:6132/api/tracks/?status=1&deezer_id=" . $idDezer, 1);
//                    Log::info($res);
                    $is_lyric = 1;
                    $lyric = json_decode($res);
                    $results = $lyric->results;
                    Log::info(json_encode($results));
                    if (count($results) > 0) {
                        if (isset($results[0]->lyric_sync) && $results[0]->lyric_sync != null && $results[0]->lyric_sync != '') {
                            $musicConfig->lyric_sync = $results[0]->lyric_sync;
                        } else {
                            $is_lyric = 0;
                        }
                    } else {
                        $is_lyric = 0;
                    }
                    if ($is_lyric == 0) {
//                    array_push($content, "The song do not have lyric");
//                    return array('status' => "danger", 'content' => $content);
                        $musicConfig->has_lyric = 0;
                    }
                }
                //gan outro
                if ($request->ck_outro_music != "-1") {
                    $musicConfig->intro_type = 2;
                    $musicConfig->intro_data = $request->ck_outro_music;
                }
                $musicConfig->save();
                array_push($content, "Success");
                Log::info(json_encode($musicConfig));
            }
        } catch (\QueryException $exc) {
            Log::info("LOI INSERT===== " . $exc);
            $status = "danger";
            array_push($content, trans('label.message.error'));
            return array('status' => $status, 'content' => $content);
        }

        return array('status' => $status, 'content' => $content);
    }

    public function show($id) {
        //
    }

    public function edit($id) {
        //
    }

    public function update(Request $request, $id) {
        //
    }

    public function destroy($id) {
        //
    }

    public function ajaxGetArtistsFromTopic(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.ajaxGetArtistsFromTopic|request=' . json_encode($request->all()));
        $datas = DB::select("select artists  from music_storage where topic = '$request->topic' group by artists order by artists");
        $lstOption = '';
        foreach ($datas as $data) {
            $lstOption .= "<option style='background-image:url(http://d5.reupnet.info/auto_download/storage/music/artists_thumb/$data->artists.jpg);' value='" . $data->artists . "'>" . ucwords(str_replace("_", " ", $data->artists)) . "</option>";
        }
//        Log::info(DB::getQueryLog());
        return $lstOption;
    }

    public function ajaxGetArtistsFromName(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.ajaxGetArtistsFromName|request=' . json_encode($request->all()));
        $datas = DB::select("select artists  from music_storage where artists like '%$request->artists%' group by artists order by artists");
        $lstOption = '';
        foreach ($datas as $data) {
            $lstOption .= "<option style='background-image:url(http://d5.reupnet.info/auto_download/storage/music/artists_thumb/$data->artists.jpg);' value='" . $data->artists . "'>" . ucwords(str_replace("_", " ", $data->artists)) . "</option>";
        }
//        Log::info(DB::getQueryLog());
        return $lstOption;
    }

    public function ajaxGetSongsFromArtists(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.ajaxGetSongsFromArtists|request=' . json_encode($request->all()));
        $stringArtists = implode("','", $request->artists);
        $stringArtists = "'" . $stringArtists . "'";
        $datas = DB::select("select title,link  from music_storage where artists in($stringArtists) order by title");
        $lstOption = '';
        foreach ($datas as $data) {
            $lstOption .= "<option value='" . $data->link . "'>" . $data->title . "</option>";
        }
//        Log::info(DB::getQueryLog());
        return $lstOption;
    }

    public function ajaxPreviewLyric(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.ajaxPreviewLyric|request=' . json_encode($request->all()));
//        $path = "D:/WORK/TEAM/autowin/automusic/web_automusic/public/";
        $path = "/home/automusic.win/public_html/public/";
        $imagePath = $path . "images/6_style_text_song.jpg";
        $background_type = $request->background_type;
        $is_lyric = $request->ck_style_lyric;
        $is_style_text = $request->ck_style_text;

        if ($background_type == 1) {
            $start = $request->g_color_s;
            $end = $request->g_color_e;
            $type = $request->g_type;
            $layerBG = PHPImageWorkshop\ImageWorkshop::initFromResourceVar(ImageHelper::createGadientImage($start, $end, $type));
        } else {
            $layerBG = PHPImageWorkshop\ImageWorkshop::initFromPath($imagePath);
        }

        if ($is_style_text == 1) {
            $style_text = array(
                "font_name" => str_replace('http://automusic.win', $path, $request->font_name),
                "font_size" => $request->font_size,
                "font_color" => str_replace("#", "", $request->font_color),
                "pos_x" => $request->pos_x,
                "pos_y" => $request->pos_y,
                "pos" => $request->pos,
                "rotation" => $request->rotation,
                "text_stroke_size" => $request->text_stroke_size,
                "text_stroke_color" => $request->text_stroke_color,
                "text_data" => $request->text_data
            );

            if ($request->text_stroke_size < 1) {
                $textLayer = PHPImageWorkshop\ImageWorkshop::initTextLayer($style_text['text_data'], $style_text['font_name'], $style_text['font_size'], $style_text['font_color'], $style_text['rotation']);
            } else {
                $textLayer = PHPImageWorkshop\ImageWorkshop::initTextBorderLayer($style_text['text_data'], $style_text['font_name'], $style_text['font_size'], $style_text['font_color'], $style_text['rotation'], NULL, $style_text['text_stroke_color'], $style_text['text_stroke_size']);
            }
            $layerBG->addLayer(1, $textLayer, $style_text['pos_x'], $style_text['pos_y'], $style_text['pos']);
        }
        if ($is_lyric == 1) {
            $number_line_lyric = $request->number_line_lyric;
            $lyric = '';
            for ($i = 1; $i <= $number_line_lyric; $i++) {
                $lyric .= "Lyric of the song $i\n";
            }
            $font = Font::find($request->font_name_lyric);
            $style_lyric = array(
                "font_name" => str_replace('http://automusic.win', $path, $font->link),
                "font_size" => 50,
                "font_color" => str_replace("#", "", $request->font_color_lyric),
                "pos_x" => 10,
                "pos_y" => 10,
                "pos" => "MM",
                "rotation" => 0,
                "number_line_lyric" => $request->number_line_lyric,
                "lyric" => $lyric
            );
            $textLayer2 = PHPImageWorkshop\ImageWorkshop::initTextLayer($style_lyric['lyric'], $style_lyric['font_name'], $style_lyric['font_size'], $style_lyric['font_color'], $style_lyric['rotation']);
            $layerBG->addLayer(2, $textLayer2, $style_lyric['pos_x'], $style_lyric['pos_y'], $style_lyric['pos']);
        }
        $image = $layerBG->getResult("ffffff");

//        $outputFile = PATH_DOWNLOAD . "WTOBG-" . time() . ".jpg";
//        imagejpeg($image, $outputFile);
//        return $outputFile;

        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    public function musicspotify(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.musicspotify|request=' . json_encode($request->all()));

        // <editor-fold defaultstate="collapsed" desc="đồng bộ dữ liệu">
        //đồng bộ dữ liệu start
//        $datasScan = Spotifymusic::where("has_lyric", 0)->orderByRaw('RAND()')->limit(500)->get();
//        $datasScan = Spotifymusic::where("has_lyric", 0)->limit(500)->get();
//        $datasScan = Spotifymusic::whereNull("id_deezer")->limit(5)->get();
//        Log::info(count($datasScan));
//        foreach ($datasScan as $data) {
//            $res = RequestHelper::getUrl("http://54.39.49.17:6132/api/tracks/?isrc=" . $data->isrc, 1);
//            $is_lyric = 0;
//            $lyric = json_decode($res);
//            $id_deezer =null;
//            $results = $lyric->results;
//            if (count($results) > 0) {
//                if (isset($results[0]->lyric_sync) && $results[0]->lyric_sync != null && $results[0]->lyric_sync != '') {
//                    $is_lyric = 1;
//                }
//                $id_deezer =$results[0]->deezer_id;
//            }
//            $data->has_lyric = $is_lyric;
//            $data->id_deezer = $id_deezer;
//            Log::info(json_encode($data));
//            $data->save();
//        }
        //đồng bộ dữ liệu end
// </editor-fold>

        $datas = Spotifymusic::whereRaw('1=1');
        $queries = [];
        $limit = 100;
        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        if (isset($request->c1) && $request->c1 != '') {
            $datas = $datas->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->c1 . '%')->orWhere('artists', 'like', '%' . $request->c1 . '%')->orWhere('assign', 'like', '%' . $request->c1 . '%');
            });
            $queries['c1'] = $request->c1;
        }
        if (isset($request->c2) && $request->c2 != '-1') {
            $datas = $datas->where('has_lyric', $request->c2);
            $queries['c2'] = $request->c2;
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo status asc
            $request['sort'] = 'id';
            $request['order'] = 'desc';
            $queries['sort'] = 'id';
            $queries['order'] = 'desc';
        }
        $datas = $datas->sortable()->paginate($limit)->appends($queries);
        foreach ($datas as $data) {
            $data->has_video = 0;
            if ($data->id_deezer != null && $data->has_lyric == 1) {
                $musicconfig = DB::select("select user_name,link_download from music_config where source like '%$data->id_deezer%' and link_download is not null");
                if (count($musicconfig) > 0) {
                    $data->musicconfig = $musicconfig;
                    $data->has_video = 1;
                }
            }
        }
//        Log::info(json_encode($datas));
        $job = DB::select("select count(*) as lyric from spotifymusic where assign = '$user->user_name' and has_lyric =0");
        return view('components.musicspotify', [
            'datas' => $datas,
            'request' => $request,
            'limit' => $limit,
            'limitSelectbox' => $this->genLimit($request),
            'status_lyric' => $this->genStatusMusicSpotify($request),
            'job' => $job,
        ]);
    }

}
