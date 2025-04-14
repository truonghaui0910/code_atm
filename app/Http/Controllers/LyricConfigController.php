<?php

namespace App\Http\Controllers;

use App\Common\Network\ProxyHelper;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Youtube\DeezerHelper;
use App\Common\Youtube\ImageHelper;
use App\Http\Models\AccountInfo;
use App\Http\Models\Bom;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\CrossPost;
use App\Http\Models\Font;
use App\Http\Models\MusicConfig;
use App\Http\Models\MusicTiktok;
use App\Http\Models\Spotifymusic;
use App\Http\Models\TiktokCharts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Log;
use PHPImageWorkshop;
use function response;
use function view;

class LyricConfigController extends Controller {

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
        $music_title = '';
        $music_artist = '';
        $lyric = '';
        $id_deezer = '';
        if ($type == "local" && $link != '') {
            $res = RequestHelper::getUrl("http://cdn.soundhex.com/api/v1/timestamp/$link", 0);
            Log::info($res);
            $dataLyric = json_decode($res);
            $lyric = $dataLyric->lyric_sync;
            $music_title = $dataLyric->title;
            $music_artist = $dataLyric->artist;
//            $link = "http://lyric.automusic.win/src/uploads/$dataLyric->url_128";
            $link = "$dataLyric->url_128";
        } else {
            $id_deezer = $link;
            if ($id_deezer != '') {
                $link = "https://www.deezer.com/en/track/$link";
            }
        }

//        list($lstChannel, $countData, $musicConfig, $id_music_config, $list_music_type,
//                $list_source_type) = $this->loadDataForReupConfig($request);
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
        return view('components.lyricconfig', [
//            'list_channel' => $lstChannel,
//            'list_channel_size' => $countData,
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
//            'musicconfig' => $musicConfig,
//            'id_music_config' => $id_music_config,
//            'list_music_type' => $list_music_type,
//            'list_source_type' => $list_source_type,
            'datas' => $this->loadDataForReupConfig($request),
            'link' => $link,
            'type' => $type,
            'lyric' => $lyric,
            'id_deezer' => $id_deezer,
            'music_title' => $music_title,
            'music_artist' => $music_artist,
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
                        array_push($arrSourceProcess, array('type' => 7, 'data' => $arrayDeezer, 'title' => $request->music_title, 'artist' => $request->music_artist));
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
                        array_push($arrSourceProcess, array('type' => 8, 'data' => $arrayDirect, 'title' => $request->music_title, 'artist' => $request->music_artist));
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
                        "text_stroke_color" => str_replace("#", "", $request->text_stroke_color)
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
//                Log::info(json_encode($musicConfig));
            }
        } catch (\QueryException $exc) {
            Log::info("LOI INSERT===== " . $exc);
            $status = "danger";
            array_push($content, trans('label.message.error'));
            return array('status' => $status, 'content' => $content);
        }

        return array('status' => $status, 'content' => $content);
    }

    public function show(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.show|request=' . json_encode($request->all()));
        $page = 1;
        if (isset($request->page)) {
            $page = $request->page;
        }
        $results = array();
        $count = 0;
        $next = null;
        $previous = null;
        $url = "http://cdn.soundhex.com/api/v1/timestamp/?page=$page";
        if (isset($request->title)) {
            $url .= "&title=" . urlencode(trim($request->title));
        }
        if (isset($request->artist)) {
            $url .= "&artist=" . urlencode(trim($request->artist));
        }

        if (isset($_GET['user-name']) && $_GET['user-name'] != "") {
            $url .= "&user-name=" . urlencode(trim($_GET['user-name']));
        }
//        Log::info($url);
        $datas = RequestHelper::callAPI2("GET", $url, []);
//        Log::info(json_encode($datas));
        if ($datas != null && $datas != "") {
//            $temp = json_decode($datas);
            $temp = $datas;
            if ($temp->count > 0) {
                $results = $temp->results;
                foreach ($results as $result) {
                    $result->disable = "";
                    $result->tooltip = "Make multi videos lyric on automusic V2";
                    $timeAdd = strtotime($result->added_time);
                    $result->added_time = gmdate("Y/m/d H:i:s", $timeAdd + 7 * 3600);
                    if (time() - $timeAdd < (60 * 60) && $user->user_name != $result->user_name) {
                        $result->disable = "disabled";
                        $result->tooltip = "Lock 30 minutes for owner";
                    }
//                    if ($result->deezer_artist_id == null) {
//                        $result->disable = "disabled";
//                        $result->tooltip = "Not found deezer_artist_id";
//                    }
                }
            }
            $count = $temp->count;
            $next = str_replace("http://cdn.soundhex.com/api/v1/timestamp/", "", $temp->next);
            $previous = str_replace("http://cdn.soundhex.com/api/v1/timestamp/", "", $temp->previous);

//            Log::info($count);
//            Log::info($next);
//            Log::info($previous);
//            Log::info(json_encode($results));
        }
        return view('components.downloadlyric', [
            "datas" => $results,
            "count" => $count,
            "next" => $next,
            "previous" => $previous,
            "request" => $request,
            'group_channel_search' => $this->loadGroupChannelForSeach($request),
            'channel_genre' => $this->loadChannelGenre($request),
            'channel_subgenre' => $this->loadChannelSubGenre($request),
            'channel_tags' => $this->loadChannelTags($request)
        ]);
    }

    public function download(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.download|request=' . json_encode($request->all()));
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=lyric_' . date('Ymd') . '.zip',
            'Expires' => '0',
            'Pragma' => 'public'
        ];
        $datas = ProxyHelper::get("http://cdn.soundhex.com/api/v1/export-lyric/$request->id");
        $file_name = 'lyric/lyric_' . uniqid() . "_$request->id.zip";
        $files = fopen($file_name, 'w');
        fwrite($files, $datas);
        fclose($files);
        $result = response()->download($file_name, "lyric_$request->id.zip", $headers);

        return $result;
    }

    public function downloadbydeezer(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.downloadbydeezer|request=' . json_encode($request->all()));
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=lyric_' . date('Ymd') . '.zip',
            'Expires' => '0',
            'Pragma' => 'public'
        ];
        $datas = ProxyHelper::get("http://cdn.soundhex.com/api/v1/export-lyric/$request->deezerid");
        $file_name = 'lyric/lyric_' . uniqid() . "_$request->deezerid.zip";
        $files = fopen($file_name, 'w');
        fwrite($files, $datas);
        fclose($files);
        $result = response()->download($file_name, "lyric_$request->deezerid.zip", $headers);

//        Log::info($datas);
        return $result;
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
        $font = Font::find($request->font_name_lyric);
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
            $lyric_sync = "";
            if ($request->type_lyric_source == 'local') {
                if ($request->lyric_source_from_tool_make_lyric != '') {
                    $lyric_sync = $request->lyric_source_from_tool_make_lyric;
                }
            } else {
                if ($request->id_deezer != '') {
                    $idDezer = $request->id_deezer;
                } else {
                    $deezer = DeezerHelper::getTrackByLink($request->source);
                    $idDezer = $deezer[0]->id;
                }

                $res = RequestHelper::getUrl("http://54.39.49.17:6132/api/tracks/?status=1&deezer_id=" . $idDezer, 1);

                $is_lyric = 1;
                $lyric = json_decode($res);
                if (!empty($lyric->results)) {
                    $results = $lyric->results;
//                Log::info(json_encode($results));
                    if (count($results) > 0) {
                        if (isset($results[0]->lyric_sync) && $results[0]->lyric_sync != null && $results[0]->lyric_sync != '') {
                            $lyric_sync = $results[0]->lyric_sync;
                        }
                    }
                }
            }

            Log::info($lyric_sync);
            //lấy font size
            $req = (object) [
                        "w" => 1000,
                        "font_size_want" => 50,
                        'font_url' => $font->link,
                        'json_lyric' => $lyric_sync
            ];
            $fontsize = RequestHelper::postClient("http://db.automusic.win/music/lyric/font", $req);
            Log::info($fontsize);
            $lyric_show = '';
            if ($lyric_sync != "") {
                $arrLyricSync = json_decode($lyric_sync);
                foreach ($arrLyricSync as $data) {
                    $line = $data->line;
                    if (Utils::isUnicode($line)) {
                        $lyric_show .= "$line\n";
                    }
                }
            }
            $number_line_lyric = $request->number_line_lyric;
            if ($lyric_show == "") {
                for ($i = 1; $i <= $number_line_lyric; $i++) {
                    $lyric_show .= "Lyric of the song $i\n";
                }
            }

            $style_lyric = array(
                "font_name" => str_replace('http://automusic.win', $path, $font->link),
                "font_size" => $fontsize,
                "font_color" => str_replace("#", "", $request->font_color_lyric),
                "pos_x" => 10,
                "pos_y" => 10,
                "pos" => "MM",
                "rotation" => 0,
                "number_line_lyric" => $request->number_line_lyric,
                "lyric" => $lyric_show
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
        DB::enableQueryLog();
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
        $sliderDate = "All";
        if (isset($request->sliderDate)) {
            $sliderDate = $request->sliderDate;
        }
        $request['sliderDate'] = $sliderDate;
        if ($sliderDate != "All") {
            $date = gmdate("Y-m-d", time() - $sliderDate * 86400);
            $datas = $datas->where("release_date", ">=", $date);
            $queries['sliderDate'] = $sliderDate;
        }
        $popularity = "0;100";
        if (isset($request->popularity)) {
            if (isset($request->popularity)) {
                $popularity = $request->popularity;
                $queries['popularity'] = $popularity;
            }
            $popularityArr = explode(";", $popularity);
            if (count($popularityArr) == 2) {
                $datas = $datas->where("max_art_popularity", ">=", $popularityArr[0])->where("max_art_popularity", "<=", $popularityArr[1]);
            }
        }
        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        if (isset($request->c1) && $request->c1 != '') {
            $datas = $datas->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->c1 . '%')->orWhere('artists', 'like', '%' . $request->c1 . '%')
                        ->orWhere('assign', 'like', '%' . $request->c1 . '%')
                        ->orWhere('art_genre', 'like', '%' . $request->c1 . '%')
                        ->orWhere('playlist_id', 'like', '%' . $request->c1 . '%');
            });
            $queries['c1'] = $request->c1;
        }
        if (isset($request->c2) && $request->c2 != '-1') {
            $datas = $datas->where('has_lyric', $request->c2);
            $queries['c2'] = $request->c2;
        }
        if (isset($request->c3) && $request->c3 != '-1') {
            $datas = $datas->where('playlist_type', $request->c3);
            $queries['c3'] = $request->c3;
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo id desc
            $request['sort'] = 'id';
            $request['order'] = 'desc';
            $queries['sort'] = 'id';
            $queries['order'] = 'desc';
        }
        $datas = $datas->sortable()->paginate($limit)->appends($queries);
        $suggests = Spotifymusic::where("is_suggest", 1)->get();
        $carts = Spotifymusic::where("cart", "like", "%$user->user_name%")->get();
        foreach ($datas as $data) {
//            $data->has_video = 0;
//            if ($data->id_deezer != null && $data->has_lyric == 1) {
//                $musicconfig = DB::select("select user_name,link_download from music_config where source like '%$data->id_deezer%' and link_download is not null");
//                if (count($musicconfig) > 0) {
//                    $data->musicconfig = $musicconfig;
//                    $data->has_video = 1;
//                }
//            }
            $followArr = explode(",", $data->art_followers);

            $formatedFollow = [];
            foreach ($followArr as $f) {
                $formatedFollow[] = number_format($f, 0, '.', ',');
            }
            $data->art_followers = implode(" <br> ", $formatedFollow);
            $data->artists = str_replace(";;", "<br>", $data->artists);
        }
//        Log::info(json_encode($datas));
        $job = DB::select("select count(*) as lyric from spotifymusic where assign = '$user->user_name' and has_lyric =0");
//        Log::info(DB::getQueryLog());
        return view('components.musicspotify', [
            'datas' => $datas,
            'request' => $request,
            'limit' => $limit,
            'limitSelectbox' => $this->genLimit($request),
            'status_lyric' => $this->genStatusMusicSpotify($request),
            'playlist_type' => $this->genSpotifyPlaylistType($request),
            'job' => $job,
            'suggests' => $suggests,
            'carts' => $carts,
            'group_channel_search' => $this->loadGroupChannelForSeach($request),
            'channel_genre' => $this->loadChannelGenre($request),
        ]);
    }

    public function musictiktok(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.musictiktok|request=' . json_encode($request->all()));
        $time = gmdate("Y-m-d", time() + (7 * 3600) - 86400);
        if (isset($request->date)) {
            $time = $request->date;
        }
        Log::info($time);
        $datas = MusicTiktok::whereRaw('1=1')->where("added_at", $time . "T00:00:00.000Z");
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
        if (isset($request->name) && $request->name != '') {
            $datas = $datas->where('name', $request->name);
            $queries['name'] = $request->name;
        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            $request['sort'] = 'rank';
            $request['order'] = 'asc';
            $queries['sort'] = 'rank';
            $queries['order'] = 'asc';
        }
        $datas = $datas->sortable()->paginate($limit)->appends($queries);

//        Log::info(json_encode($datas));
        Log::info(DB::getQueryLog());
        return view('components.musictiktok', [
            'datas' => $datas,
            'request' => $request,
            'limit' => $limit,
            'limitSelectbox' => $this->genLimit($request),
            'status_lyric' => $this->genStatusMusicSpotify($request),
            'dateSelect' => $this->genDateSelect($request)
        ]);
    }

    public function tiktokcharts(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.tiktokcharts|request=' . json_encode($request->all()));
        $time = gmdate("Y-m-d", time() + (7 * 3600) - 86400);
        if (isset($request->date)) {
            $time = $request->date;
        }
        Log::info($time);
        $datas = TiktokCharts::whereRaw('1=1');
        $queries = [];
        $limit = 100;
        if (isset($request->limit)) {
            if ($request->limit <= 1000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }

        if (isset($request->name) && $request->name != '') {
            $datas = $datas->where('name', $request->name);
            $queries['name'] = $request->name;
        }
        if (isset($request->artist) && $request->artist != '') {
            $datas = $datas->where('artist_name', $request->artist);
            $queries['artist'] = $request->artist;
        }
        if (isset($request->tags) && $request->tags != '') {
            $datas = $datas->where('tags', 'like', "%$request->tags%");
            $queries['tags'] = $request->tags;
        }
        if (isset($request->date) && $request->date != '-1') {
            $datas = $datas->where('day_report', $request->date);
            $queries['date'] = $request->date;
        }
        $country = 'us';
        if (isset($request->country) && $request->country != '') {
            $country = $request->country;
        }
        $sort = "rank_c_$country";
        $datas = $datas->where("rank_c_$country", '<>', 0);
        if (isset($request->sort)) {
            if ($request->sort == "rank_c_") {
                $queries['sort'] = "rank_c_$country";
                $request['sort'] = "rank_c_$country";
            } else {
                $queries['sort'] = $request->sort;
            }

            if (isset($request->order)) {
                $queries['order'] = $request->order;
                $request['order'] = $request->order;
            }
        } else {
            $request['sort'] = "rank_c_$country";
            $request['order'] = 'asc';
            $queries['sort'] = "rank_c_$country";
            $queries['order'] = "asc";
        }
        Log::info(json_encode($queries));

//        $request['sort'] = "rank_c_$country";
//        $request['order'] = 'desc';
//        $queries['sort'] = "rank_c_$country";
//        $queries['order'] = "desc";

        $datas = $datas->sortable()->paginate($limit)->appends($queries);

        foreach ($datas as $data) {
            $data->rank_country = $data->$sort;
            $data->short_name = $data->name;
            if (strlen($data->name) > 30) {
                $data->short_name = substr($data->name, 0, 26) . '...';
            }
            $data->short_artist = $data->artist_name;
            if (strlen($data->artist_name) > 30) {
                $data->short_artist = substr($data->artist_name, 0, 26) . '...';
            }
        }
//        Log::info(json_encode($datas));
        Log::info(DB::getQueryLog());
        return view('components.tiktokchart', [
            'datas' => $datas,
            'request' => $request,
            'limit' => $limit,
            'limitSelectbox' => $this->genLimit($request),
            'dateSelect' => $this->genDateSelect($request),
            'country' => $this->genCountryChartmetric($request),
            'date' => $this->genDateSelectWeek($request)
        ]);
    }

    public function exportTiktokCharts(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.exportTiktokCharts|request=' . json_encode($request->all()));
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 30);
        try {

            $headers = [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
                , 'Content-type' => 'text/csv'
                , 'Content-Disposition' => 'attachment; filename=tiktok_charts_' . date('Ymd') . '.csv'
                , 'Expires' => '0'
                , 'Pragma' => 'public'
            ];
            $datas = TiktokCharts::whereRaw("1=1");
            if (isset($request->name) && $request->name != '') {
                $datas = $datas->where('name', $request->name);
            }
            if (isset($request->artist) && $request->artist != '') {
                $datas = $datas->where('artist_name', $request->artist);
            }
            if (isset($request->tags) && $request->tags != '') {
                $datas = $datas->where('tags', 'like', "%$request->tags%");
            }
            if (isset($request->date) && $request->date != '-1') {
                $datas = $datas->where('day_report', $request->date);
            }
            $country = 'us';
            if (isset($request->country) && $request->country != '') {
                $country = $request->country;
            }
            $sort = "rank_c_$country";
            $datas = $datas->where("rank_c_$country", '<>', 0);
            if (isset($request->sort) && isset($request->order)) {
                $datas = $datas->orderBy("$request->sort", "$request->order");
            } else {
                $datas = $datas->orderBy("$sort", "asc");
            }

            $datas = $datas->whereNotNull("id_deezer")->get(["id_deezer", "name", "artist_name", "url_download"]);


            $lists = $datas->toArray();
            $title = array('Id Deezer', 'Name', 'Artist Name', "Url Download");
            array_unshift($lists, $title);

            $callback = function() use ($lists) {
                $FH = fopen('php://output', 'w');
                foreach ($lists as $row) {
                    fputcsv($FH, $row);
                }
                fclose($FH);
            };
            return response()->stream($callback, 200, $headers);
        } catch (Exception $ex) {
            Log::info($ex);
        }
    }

    public function spotifycharts(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.spotifycharts|request=' . json_encode($request->all()));
        $type = "regional";
        $country = "global";
        if (isset($request->type)) {
            $type = $request->type;
        }
        if (isset($request->country)) {
            $country = $request->country;
        }
        $url = "https://spotifycharts.com/$type/$country/daily/latest";

        $response = RequestHelper::get($url, 2);
        preg_match_all("/href=\"https:\/\/open.spotify.com\/track\/(\w+)/", $response, $matches);
        $arrSpotiifyId = [];
        if (count($matches) > 1) {
            $arrSpotiifyId = $matches[1];
        }
        $listSongs = [];
        foreach ($arrSpotiifyId as $spotifyId) {
            $check = Spotifymusic::where("spotify_id", $spotifyId)->first();
            if ($check) {
                $listSongs[] = $check;
            }
        }
        foreach ($listSongs as $data) {

            $followArr = explode(",", $data->art_followers);
            $formatedFollow = [];
            foreach ($followArr as $f) {
                $formatedFollow[] = number_format($f, 0, '.', ',');
            }
            $data->art_followers = implode(" | ", $formatedFollow);
        }

        return view('components.spotifycharts', [
            'datas' => $listSongs,
            'request' => $request,
            'limit' => 200,
            'limitSelectbox' => $this->genLimit($request),
            'status_lyric' => $this->genStatusMusicSpotify($request),
        ]);
    }

    public function reportBlock(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.reportBlock=' . json_encode($request->all()));
        $data = Spotifymusic::find($request->id);
        if ($data) {
            if ($data->is_block == 1) {
                $data->is_block = 0;
            } else {
                $data->is_block = 1;
            }
            $data->save();
        }
        return array('status' => 'success', "content" => $data->is_block);
    }

    public function markSuggest(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.markSuggest=' . json_encode($request->all()));
        $data = Spotifymusic::find($request->id);
        if ($data) {
            if ($data->is_suggest == 1) {
                $data->is_suggest = 0;
            } else {
                if (isset($request->note)) {
                    $data->note = $request->note;
                }
                $data->is_suggest = 1;
            }
            $data->save();
        }
        return array('status' => 'success', "content" => $data->is_suggest);
    }

    public function cartSong(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.cartSong=' . json_encode($request->all()));
        $data = Spotifymusic::find($request->id);
        $action = 0;
        if ($data) {
            if ($data->cart != null) {
                $carts = explode(",", $data->cart);

                if (($key = array_search($user->user_name, $carts)) !== false) {
                    unset($carts[$key]);
                } else {
                    $action = 1;
                    array_push($carts, $user->user_name);
                }
                $cartNew = implode(",", $carts);
                $data->cart = $cartNew;
                $data->save();
            } else {
                $action = 1;
                $data->cart = $user->user_name;
                $data->save();
            }
        }
        return array('status' => 'success', "content" => $action);
    }

    public function updateNote(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.updateNote=' . json_encode($request->all()));
        $data = Spotifymusic::find($request->id);
        if ($data) {
            $data->note = $request->note;
            $data->save();
        }
        return array('status' => 'success', "content" => $data->note);
    }

    //get list hub theo user
    public function getHubsByChannelIdOld(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.getHubsByChannelId=' . json_encode($request->all()));
        $listChannel = [];
        $limit = 10;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }
        $views = 0;
        if (isset($request->views)) {
            $views = $request->views;
        }
        $subs = 0;
        if (isset($request->subs)) {
            $subs = $request->subs;
        }
        $channels = AccountInfo::where("is_automusic_v2", 1)->where("is_music_channel", 2)->orderBy("channel_genre")
                        ->where("view_count", ">=", $views)->where("subscriber_count", ">=", $subs);
        if (isset($request->group_channel_search) && $request->group_channel_search != "-1") {
            $channels = $channels->where("group_channel_id", $request->group_channel_search);
        }
        if (isset($request->channel_genre) && $request->channel_genre != "-1") {
            $channels = $channels->where("channel_genre", $request->channel_genre);
        }

        $channels = $channels->take($limit)->get();
        Log::info("channel: " . count($channels));
        foreach ($channels as $channel) {
            $listChannel[] = $channel->chanel_id;
        }
        $req = (object) [
                    "channel_ids" => $listChannel
        ];
        $result = [];
        $res = RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/hub/check", $req);
        if ($res != null || $res != "") {
            foreach ($res as $data) {
                foreach ($channels as $channel) {
                    $channelName = "";
                    if ($data->channel_id == $channel->chanel_id) {
                        $channelName = $channel->chanel_name;
                        break;
                    }
                }
                $temp = (object) [
                            "channel_id" => $data->channel_id,
                            "channel_name" => "$channelName - $channel->channel_genre",
                            "hub" => $data->hub
                ];
                $result[] = $temp;
            }
            return $result;
        }
        return null;
    }

    //get list hub theo user (dang dung)
    public function getHubsByChannelId(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.getHubsByChannelId=' . json_encode($request->all()));
        $limit = 10;
        if (isset($request->limitChannel)) {
            $limit = $request->limitChannel;
        }
        $views = 0;
        if (isset($request->views)) {
            $views = $request->views;
        }
        $subs = 0;
        if (isset($request->subs)) {
            $subs = $request->subs;
        }
        $crossType = 1;
        if (isset($request->crossType)) {
            $crossType = $request->crossType;
        }
        $time = time();
        //danh sách những lệnh mà được tạo trong 1 ngày trở lại đây
//        $lastCrossPost = DB::select("select * from  cross_post where track_id = '$request->trackId' and track_type ='$request->trackType' and $time - UNIX_TIMESTAMP(STR_TO_DATE(create_time,'%Y-%m-%d %H:%i:%s')) < 3600 ");
        $allCrossPost = DB::select("select * from  cross_post where track_id = '$request->trackId' and track_type ='$request->trackType'");
//        Log::info("getHubsByChannelId: listcrossed " . json_encode($allCrossPost));
        $result = [];

        $res = RequestHelper::callAPI("GET", "http://api-magicframe.automusic.win/hub/get/post-type/$crossType", []);
//        $res = RequestHelper::callAPI("GET", "http://api-magicframe.automusic.win/hub/get/cross", []);
//        $res = RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/hub/check", $req);
        $arrayChannel = [];
        if ($res != null || $res != "") {
            foreach ($res as $data) {
                $arrayChannel[] = $data->channel_id;
            }
            Log::info(count($arrayChannel));
            $uniques = array_unique($arrayChannel);
//            Log::info(count($uniques));
//            Log::info(json_encode($uniques));
            DB::enableQueryLog();

            foreach ($uniques as $uni) {
                $channel = AccountInfo::where("chanel_id", $uni)->where("view_count", ">=", $views)->where("subscriber_count", ">=", $subs);
                if (isset($request->group_channel_search) && $request->group_channel_search != "-1") {
                    $channel = $channel->where("group_channel_id", $request->group_channel_search);
                }
                if (isset($request->channel_genre) && $request->channel_genre != "-1") {
                    $channel = $channel->where("channel_genre", $request->channel_genre);
                }
                if (isset($request->channel_subgenre) && count($request->channel_subgenre) > 0) {
                    $channel = $channel->where(function($q) use ($request) {
                        foreach ($request->channel_subgenre as $sub) {
                            $q->orWhere('channel_subgenre', 'like', '%' . $sub . '%');
                        }
                    });
                }
                if (isset($request->channel_tags) && count($request->channel_tags) > 0) {
                    $channel = $channel->where(function($q) use ($request) {
                        foreach ($request->channel_tags as $tag) {
                            $q->orWhere('tags', 'like', '%' . $tag . '%');
                        }
                    });
                }
                if (isset($request->channel_name) && $request->channel_name != "-1") {
                    $channel = $channel->where("chanel_name", "like", "%$request->channel_name%");
                }
                //nếu là kiểu hub boom thì chỉ lấy kênh của user hiện tại và tag là BOOM
                if ($crossType == 3) {
                    $channel = $channel->where("user_name", $user->user_code)->where("tags", "like", "%BOOM%");
                }
                $channel = $channel->first();
//                Log::info(DB::getQueryLog());
                if ($channel) {
                    //kiểm tra share crossboss
                    if ($channel->is_cross_post == 0 && $channel->user_name != $user->user_code) {
                        continue;
                    }
//                    Log::info("$channel->chanel_id $channel->chanel_name");
                    $fail = 0;
                    $success = 0;
                    $views = 0;
                    $dailyViews = 0;
                    $pos = strripos($channel->user_name, '_');
                    $username = substr($channel->user_name, 0, $pos);
                    //kiểm tra xem channel này đã được crosspost bài promos này chưa,show ra nếu có
                    $isAdd = 1;
                    if (count($result) < $limit) {
                        //kiêm tra xem bài hát này đã được thêm cross post vào channel nào trong 1 ngày trở lại dây, nếu đã thêm thì ko hiện channel đó ra nữa
                        foreach ($allCrossPost as $cross) {
                            //thời gian tạo lệnh up video mà cách thời điểm hiện tại dưới 1 tiếng thì ko hiện ra
                            //bài promo ko khóa 1h
                            if ($crossType == 1) {
                                $timeUp = strtotime("$cross->create_time GMT+07:00");
                                if (time() - $timeUp < 3600) {
                                    if ($cross->channel_id == $channel->chanel_id) {
                                        $isAdd = 0;
                                    }
                                }
                            }
                            //kiểm tra xem dã tạo được bao nhiêu lệnh và đã up đc bao nhiêu video promo này lên kênh này rồi
                            if ($cross->channel_id == $channel->chanel_id) {
                                if ($cross->video_id != null) {
                                    $success++;
                                    if ($cross->views != null) {
                                        $views = $views + $cross->views;
                                    }
                                    if ($cross->daily_views != null) {
                                        $dailyViews = $dailyViews + $cross->daily_views;
                                    }
                                } else {
                                    $fail++;
                                }
                            }
                        }
                    } else {
                        break;
                    }

                    //tạo dữ liệu hub
                    $hub = [];
                    foreach ($res as $data) {
                        if ($channel->chanel_id == $data->channel_id) {
                            $tempHub = (object) [
                                        "id" => $data->id,
                                        "name" => $data->name
                            ];
                            $hub[] = $tempHub;
                        }
                    }
                    //tạo object channel có hub
                    $tempChannel = (object) [
                                "channel_id" => $channel->chanel_id,
                                "channel_name" => "$channel->chanel_name - $channel->channel_genre - $username - S:$success,F:$fail,V:$views,D:$dailyViews",
                                "hub" => $hub
                    ];
                    if ($isAdd == 1) {
                        $result[] = $tempChannel;
                    }
                }
            }

//            Log::info(json_encode($result));
            return $result;
        }
        return null;
    }

    //tạo lệnh crosspost
    public function autoMakeLyricHub(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.autoMakeLyricHub=' . json_encode($request->all()));
        if ($request->trackId == null) {
            return array('status' => 'error', "content" => "Not Found Deezer Id");
        }
        if ($request->trackType == null) {
            return array('status' => 'error', "content" => "Not Found Music Type (Deezer or Local)");
        }
        if (!isset($request->hubs)) {
            return array('status' => 'error', "content" => "Please choose some hub");
        }

        $req = [];
        $req['uri'] = "$request->trackType:track:$request->trackId";
        $req['hubs'] = $request->hubs;
        $req['user_name'] = $user->user_name;
        $req['auto_submit'] = 0;
        //thêm dữ liệu cho crosspost promos
        $crossType = 1;
        if ($request->crossType == 2) {
            $crossType = 2;
            $campaign = CampaignStatistics::where("lyric_timestamp_id", $request->trackId)->first();
            if ($campaign) {
                $req['promo_keywords'] = $campaign->promo_keywords;
                $req['promo_artist_social'] = $campaign->artists_social;
                $req['promo_bitly_official'] = $campaign->bitly_url;
                $req['auto_submit'] = 1;
            }
        } else if ($request->crossType == 3) {
            $crossType = 3;
        }

//        $req = (object) [
//                    "uri" => "$request->trackType:track:$request->trackId",
//                    "hubs" => $request->hubs,
//                    "user_name" => $user->user_name
//        ];
//        $x = '[{"hub_id":"1","channel_id":"UCH5YbeeoQjkxk089xEMCxwA","channel_name":"Kid Run","status":1,"job_id":17802},{"hub_id":"6","channel_id":"UCH5YbeeoQjkxk089xEMCxwA","channel_name":"Kid Run","status":0,"job_id":17803},{"hub_id":"12","channel_id":"UCSLPdTzcMsgh-QSgX3dm_cw","channel_name":"john richards","status":1,"job_id":17804},{"hub_id":"20","channel_id":"UCDobf-vdZEA8W_KethMecwg","channel_name":"ronald coulter","status":1,"job_id":17805}]';
//        return array('status' => 'success', "content" => json_decode($x));
        Log::info("autoMakeLyricHub: " . json_encode($req));
        $res = RequestHelper::callAPI("POST", "http://api-magicframe.automusic.win/auto/lyric/make", $req);
        Log::info("autoMakeLyricHub: " . json_encode($res));
        if ($res == null || $res == "") {
            return array('status' => 'error', "content" => "System got error, Api return null");
        } else if ($res->status == 0) {
            return array('status' => 'error', "content" => $res->mess);
        }
        foreach ($res->rs as $data) {
            $crossPost = new CrossPost();
            $crossPost->user_make = $user->user_name;
            $channel = AccountInfo::where("chanel_id", $data->channel_id)->first();
            $pos = strripos($channel->user_name, '_');
            $userOwner = substr($channel->user_name, 0, $pos);
            $crossPost->user_owner = $userOwner;
            $crossPost->channel_id = $data->channel_id;
            $crossPost->channel_name = $channel->chanel_name;
            $crossPost->job_id = $data->job_id;
            $crossPost->cross_type = $crossType;
            $crossPost->track_id = $request->trackId;
            $crossPost->track_type = $request->trackType;
            $crossPost->create_time = gmdate("Y-m-d H:i:s", time() + 7 * 3600);
            if (isset($request->campId)) {
                $crossPost->campaign_id = $request->campId;
            }
            $crossPost->save();
            //update lại log nếu là boom
            $update = Bom::where("deezer_id", $request->trackId)->first();
            if ($update) {
                $update->user_used = $user->user_name;
                $update->last_used = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $update->count = $update->count + 1;
                $log = $update->log;
                $log .= PHP_EOL . gmdate("Y/m/d H:i:s", time() + 7 * 3600) . " $user->user_name made video";
                $update->log = $log;
                $update->save();
            }
        }
        return array('status' => 'success', "content" => $res->rs);
    }

    public function chartmetricSpotifyPlaylist(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.chartmetricSpotifyPlaylist|request=' . json_encode($request->all()));
        $genre = "";
        $country = "xx";
        if (isset($request->genre) && $request->genre != "") {
            $genre = $request->genre;
        }
        if (isset($request->country)) {
            $country = $request->country;
        }
        $offset = 0;
        if (isset($request->offset)) {
            $offset = $request->offset;
        }
        $reqGenre = "";
        if ($genre != "") {
            $reqGenre = "tagIds[]=$genre";
        }
        $link = "https://api.chartmetric.com/playlist/spotify/list?code2=$country&$reqGenre&editorial=true&majorCurator=true&indie=true&newMusicFriday=true&limit=50&offset=$offset&sortColumn=followers&sortOrderDesc=true";
        Log::info($link);
        $url = "http://54.39.49.17:6543/chartmetric/get/" . base64_encode($link);
        $response = RequestHelper::get($url, 2);
        Log::info($response);
//        Utils::write("metric.txt", $response);
        $datas = [];
        $total = 0;
        $next = null;
        $previous = null;
        if ($response != null && $response != "") {
            $obj = json_decode($response);
            $datas = !empty($obj->obj) ? $obj->obj : [];
            $total = !empty($obj->total) ? $obj->total : 0;
            $next = "chartmetricSpotifyPlaylist?country=$country&genre=$genre&offset=" . ($offset + 50);
            if ($offset == 0) {
                $previous = null;
            } else {
                $previous = "chartmetricSpotifyPlaylist?country=$country&genre=$genre&offset=" . ($offset - 50);
            }
        }

        return view('components.chartmetricspotifyplaylist', [
            'datas' => $datas,
            'request' => $request,
            'limit' => 200,
            'total' => $total,
            'next' => $next,
            'previous' => $previous,
        ]);
    }

    //update deezer_artists_id
    public function updateDeezerArtistsId(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LyricConfigController.updateDeezerArtistsId=' . json_encode($request->all()));
        if (isset($request->value)) {
            if (!is_numeric($request->value)) {
                return "Deezer artist id invalid";
            }
            $url = "http://cdn.soundhex.com/api/v1/timestamp/$request->pk/";
            $response = RequestHelper::callAPI2("PUT", $url, ["id" => $request->pk, "deezer_artist_id" => $request->value]);
            Log::info("Rs:" . json_encode($response));
            return "success";
        }
    }

}
