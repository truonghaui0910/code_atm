@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title "><span class="m-t-7 m-r-10">Reports</span>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Sync now" type="button" class="btn btn-outline-info btn-sync-promos btn-sm"><i class="fa fa-refresh"></i></button>
                <!--<button data-toggle="tooltip" data-placement="top" data-original-title="Export to csv" type="button" class=" m-l-5 btn btn-outline-info btn-export"><i class="fa fa-file-excel-o"></i></button>-->
            </h4>&nbsp;
            <span>{{$lastSync}}</span>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Tableau</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">FILTER</h4>
            <form id="formTableau" action="/tableau">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Promo Campaign</label>
                            <div class="col-12">
                                <select class="form-control search_select" id="promo_id" name="promo_id" data-show-subtext="true" data-live-search="true">
                                    {!!$listPromosOption!!}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Minimum View</label>
                            <div class="col-12">
                                <input id="minimum_view" class="form-control" type="number" name="minimum_view" value="{{$request->minimum_view}}">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">&nbsp;</label>
                            <div class="col-12">
                                <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i class="fa fa-filter"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Cover Art Mix <i data-toggle="tooltip" data-placement="top" data-original-title="If you do not choose an image, the system will automatically, if selected, wrap report needs 1 mix and 2 lyric, Update report needs 1 mix, 1 lyric." class="fa fa-question-circle"></i></label>
                            <div class="col-12 div-select-image">
                                <select multiple id="art_thumb_mix" class="form-control ck_artists" name="art_thumb_mix[]" style="height: 200px" >
                                    @if(count($datas)>0)

                                    @foreach($datas as $data)
                                    @if($data->video_type==5)
                                    <option class="bg-img" style="background-image:url( https://i.ytimg.com/vi/{{$data->video_id}}/hq720.jpg);padding-left: 115px;" value="{{$data->video_id}}">{{number_format($data->views, 0, '.', ',')}}</option>
                                    @endif
                                    @endforeach
                                    @endif
                                </select>                             
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Cover Art Lyric <i data-toggle="tooltip" data-placement="top" data-original-title="If you do not choose an image, the system will automatically, if selected, wrap report needs 1 mix and 2 lyric, Update report needs 1 mix, 1 lyric." class="fa fa-question-circle"></i></label>
                            <div class="col-12">
                                <select multiple id="art_thumb_lyric" class="form-control ck_artists" name="art_thumb_lyric[]" style="height: 200px" >
                                    @foreach($datas as $data)
                                    @if($data->video_type==2)
                                    <option class="bg-img" style="background-image:url( https://i.ytimg.com/vi/{{$data->video_id}}/hq720.jpg);padding-left: 115px;" value="{{$data->video_id}}">{{number_format($data->views, 0, '.', ',')}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Template <i data-toggle="tooltip" data-placement="top" data-original-title="Choose a template" class="fa fa-question-circle"></i></label> 
                            <div class="col-12">
                                <select class="form-control" id="template" name="template" >
                                    <option value="MOONSHOTS">MOONSHOTS</option>
                                    <option value="DUNN DEAL PR">DUNN DEAL PR</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">&nbsp;</label>
                            <div class="col-12">
                                <button data-type="2" data-toggle="tooltip" data-placement="top" data-original-title="Export to wrap report pdf" type="button" class=" m-l-5 btn btn-outline-info btn-export-pdf"><i class="fa fa-file-pdf-o"></i> PDF</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Last Report <i data-toggle="tooltip" data-placement="top" data-original-title="Select the date of the old report to calculate the% increase" class="fa fa-question-circle"></i></label> 
                            <div class="col-12">
                                <select class="form-control search_select" id="date_caculate" name="date_caculate" data-show-subtext="true" data-live-search="true">
                                    {!!$listDate!!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">&nbsp;</label>
                            <div class="col-12">
                                <button data-type="1" data-toggle="tooltip" data-placement="top" data-original-title="Export to campaign update pdf" type="button" class=" m-l-5 btn btn-outline-info btn-export-pdf"><i class="fa fa-file-pdf-o"></i> PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@if(count($lastReport)> 0)
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">LAST REPORT</h4>
            <div class="row">
                <table class="table mobile-table-width table-drag">
                    <thead class="thead-default">
                        <tr align="center">
                            <th style="width: 5%;text-align: left">Date</th>
                            <th style="width: 5%;text-align: center">PromoID</th>
                            <th style="width: 5%;text-align: center">PromoName</th>
                            <th style="width: 7%;text-align: center">Videos</th>
                            <th style="width: 7%;text-align: center">Videos Lyric</th>
                            <th style="width: 7%;text-align: center">Videos Mix</th>
                            <th style="width: 10%;text-align: center">Views</th>
                            <th style="width: 10%;text-align: center">Views Lyric</th>
                            <th style="width: 10%;text-align: center">Views Mix</th>
                            <th style="width: 10%;text-align: center">Watch Time(h)</th>
                            <th style="width: 10%;text-align: center">Suggested</th>
                            <th style="width: 12%;text-align: center">% Suggested</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lastReport as $data)
                        <tr class="odd gradeX" align="center">
                            <td>{{$data->create_date}}</td>
                            <td>{{$data->promo_id}}</td>
                            <td>{{$data->promo_name}}</td>
                            <td>{{$data->videos}}</td>
                            <td>{{$data->videos_lyric}}</td>
                            <td>{{$data->videos_mix}}</td>
                            <td>{{number_format($data->views, 0, '.', ',')}}</td>
                            <td>{{number_format($data->views_lyric, 0, '.', ',')}}</td>
                            <td>{{number_format($data->views_mix, 0, '.', ',')}}</td>
                            <td>{{number_format(round($data->watch_time_hours,2), 0, '.', ',')}}</td>
                            <td>{{number_format($data->suggested_traffic_views, 0, '.', ',')}}</td>
                            <td>
                                @if($data->views_athena>0)
                                {{round($data->suggested_traffic_views / $data->views_athena)* 100}}%
                                @else
                                0%
                                @endif
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>  
            </div>
        </div>
    </div>
</div>
@endif
@if(count($promoStats)>0)
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">PROMO STATS OF <span class="color-red">{{$promoStats[0]->promo_name}}</span></h4>
            <div class="row">

                <div class="col-sm-4 col-xs-12">
                    <div class="card m-b-20 text-xs-center">
                        <div class="card-header">
                            ALL
                        </div>
                        <div class="card-body">
                            Current Views : <span class="color-blue">{{number_format($promoStats[0]->views, 0, '.', ',')}}</span><br>
                            Bulk Views : <span class="color-blue">{{number_format($promoStats[0]->views_athena, 0, '.', ',')}}</span><br>
                            Suggested Views : <span class="color-blue">{{number_format($promoStats[0]->suggested_traffic_views, 0, '.', ',')}}</span><br>
                            % Suggested : <span class="color-blue">
                                @if($promoStats[0]->views_athena>0)
                                {{round($promoStats[0]->suggested_traffic_views / $promoStats[0]->views_athena * 100)}}%
                                @else
                                0%
                                @endif
                            </span><br>
                            Watch Time(h) : <span class="color-blue">{{number_format(round($promoStats[0]->watch_time_hours), 0, '.', ',')}}</span><br>
                            Videos : <span class="color-blue">{{number_format($promoStats[0]->videos, 0, '.', ',')}}</span><br>
                            @if(count($lastReport)> 0)
                            % Increase Views : <span class="color-blue">
                                @if($lastReport[0]->views >0)
                                {{round(($promoStats[0]->views - $lastReport[0]->views) / $lastReport[0]->views * 100)}}%</span><br>
                            @else
                            N/A
                            @endif
                            % Increase WatchTime : <span class="color-blue">
                                @if($lastReport[0]->watch_time_hours > 0)
                                {{round(($promoStats[0]->watch_time_hours - $lastReport[0]->watch_time_hours) / $lastReport[0]->watch_time_hours * 100)}}%</span><br>
                            @else
                            N/A
                            @endif
                            @else
                            % Increase Views : <span class="color-blue">N/A</span><br>
                            % Increase WatchTime : <span class="color-blue">N/A</span><br>
                            @endif


                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <div class="card m-b-20 text-xs-center">
                        <div class="card-header">
                            MIX
                        </div>
                        <div class="card-body" style="min-height: 160px">
                            Current Views : <span class="color-blue">{{number_format($promoStats[0]->views_mix, 0, '.', ',')}}</span><br>
                            Bulk Views : <span class="color-blue">{{number_format($promoStats[0]->views_mix_athena, 0, '.', ',')}}</span><br>
                            Suggested Views : <span class="color-blue">{{number_format($promoStats[0]->suggested_traffic_views_mix, 0, '.', ',')}}</span><br>
                            % Suggested : <span class="color-blue">
                                @if($promoStats[0]->views_mix_athena>0)
                                {{round($promoStats[0]->suggested_traffic_views_mix / $promoStats[0]->views_mix_athena * 100)}}%
                                @else
                                0%
                                @endif
                            </span><br>
                            Watch Time(h) : <span class="color-blue">{{number_format(round($promoStats[0]->watch_time_hours_mix), 0, '.', ',')}}</span><br>
                            Videos : <span class="color-blue">{{number_format($promoStats[0]->count_mix, 0, '.', ',')}}</span><br>
                            @if(count($lastReport)> 0)
                            % Increase Views : <span class="color-blue">
                                @if($lastReport[0]->views_mix > 0)
                                {{round(($promoStats[0]->views_mix - $lastReport[0]->views_mix) / $lastReport[0]->views_mix * 100)}}%</span><br>
                            @else
                            N/A
                            @endif
                            % Increase WatchTime : <span class="color-blue">
                                @if($lastReport[0]->watch_time_hours_mix > 0)
                                {{round(($promoStats[0]->watch_time_hours_mix - $lastReport[0]->watch_time_hours_mix) / $lastReport[0]->watch_time_hours_mix * 100)}}%</span><br>
                            @else
                            N/A
                            @endif
                            @else
                            % Increase Views : <span class="color-blue">N/A</span><br>
                            % Increase WatchTime : <span class="color-blue">N/A</span><br>
                            @endif                            

                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <div class="card m-b-20 text-xs-center">
                        <div class="card-header">
                            LYRIC
                        </div>
                        <div class="card-body" style="min-height: 160px">
                            Current Views : <span class="color-blue">{{number_format($promoStats[0]->views_lyric, 0, '.', ',')}}</span><br>
                            Bulk Views : <span class="color-blue">{{number_format($promoStats[0]->views_lyric_athena, 0, '.', ',')}}</span><br>
                            Suggested Views : <span class="color-blue">{{number_format($promoStats[0]->suggested_traffic_views_lyric, 0, '.', ',')}}</span><br>
                            % Suggested : <span class="color-blue">
                                @if($promoStats[0]->views_lyric_athena>0)
                                {{round($promoStats[0]->suggested_traffic_views_lyric / $promoStats[0]->views_lyric_athena * 100)}}%
                                @else
                                0%
                                @endif
                            </span><br>
                            Watch Time(h) : <span class="color-blue">{{number_format(round($promoStats[0]->watch_time_hours_lyric), 0, '.', ',')}}</span><br>
                            Videos : <span class="color-blue">{{number_format($promoStats[0]->count_lyric, 0, '.', ',')}}</span><br>
                            @if(count($lastReport)> 0)
                            % Increase Views : <span class="color-blue">
                                @if($lastReport[0]->views_lyric > 0)
                                {{round(($promoStats[0]->views_lyric - $lastReport[0]->views_lyric) / $lastReport[0]->views_lyric * 100)}}%</span><br>
                            @else
                            N/A
                            @endif
                            % Increase WatchTime : <span class="color-blue">
                                @if($lastReport[0]->watch_time_hours_lyric > 0)
                                {{round(($promoStats[0]->watch_time_hours_lyric - $lastReport[0]->watch_time_hours_lyric) / $lastReport[0]->watch_time_hours_lyric * 100)}}%</span><br>
                            @else
                            N/A
                            @endif
                            @else
                            % Increase Views : <span class="color-blue">N/A</span><br>
                            % Increase WatchTime : <span class="color-blue">N/A</span><br>
                            @endif   
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">VIDEO INFORMATION 
                <button data-toggle="tooltip" data-placement="top" data-original-title="Export to csv" type="button" class=" m-l-5 btn btn-outline-info btn-export btn-sm"><i class="fa fa-file-excel-o"></i></button>
            </h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-boom" style="max-width: 99%">
                    {{ csrf_field() }}
                    <div class="row">
                        <table class="table mobile-table-width table-drag">
                            <thead class="thead-default">
                                <tr align="center">
                                    <th style="width: 5%;text-align: left">Video ID</th>
                                    <th style="width: 25%;text-align: left">Video Title</th>
                                    <th style="width: 5%;text-align: center">@sortablelink('video_type','Video Type')</th>
                                    <th style="width: 20%;text-align: left">Channel Name</th>
                                    <th style="width: 10%;text-align: center">@sortablelink('views','Views')</th>
                                    <th style="width: 10%;text-align: center">@sortablelink('views_athena','Bulk Views')</th>
                                    <th style="width: 10%;text-align: center">@sortablelink('suggested_traffic_views','Suggested')</th>
                                    <th style="width: 10%;text-align: center">% Suggested</th>
                                    <th style="width: 10%;text-align: center">@sortablelink('watch_time_hours','Watch Time(h)')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                <tr class="odd gradeX" align="center">
                                    <td style="text-align: left">{{$data->video_id}}</td>
                                    <td style="text-align: left">
                                        <a target="_blank" href="https://www.youtube.com/watch?v={{$data->video_id}}">
                                            <img class="video-img_thumb" src="https://i.ytimg.com/vi/{{$data->video_id}}/default.jpg">
                                            {{$data->video_title_short}}
                                        </a>
                                    </td>
                                    <td style="text-align: center">
                                        @if($data->video_type==2)
                                        Lyric
                                        @elseif($data->video_type==5)
                                        Mix
                                        @elseif($data->video_type==6)
                                        Short
                                        @else
                                        None
                                        @endif
                                    </td>
                                    <td style="text-align: left"><a target="_blank" href="https://www.youtube.com/channel/{{$data->channel_id}}">{{$data->channel_name}}</a></td>
                                    <td>{{number_format($data->views, 0, '.', ',')}}</td>
                                    <td>{{number_format($data->views_athena, 0, '.', ',')}}</td>
                                    <td>{{number_format($data->suggested_traffic_views, 0, '.', ',')}}</td>
                                    <td>
                                        @if($data->views_athena>0)
                                        {{round($data->suggested_traffic_views / $data->views_athena,2)* 100}}%
                                        @else
                                        0%
                                        @endif
                                    </td>
                                    <td>{{round($data->watch_time_hours,2)}}</td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script type="text/javascript">

    $(".btn-export").click(function () {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var promoId = $("#promo_id").val();
        var minimum_view = $("#minimum_view").val();

        var url = "/tableauExportCsv?promo_id=" + promoId + "&minimum_view=" + minimum_view + "&art_thumb_mix=" + art_thumb_mix + "&art_thumb_lyric=" + art_thumb_lyric ;
        window.open(url, '_blank');
        $this.html($this.data('original-text'));


    });

    $(".btn-export-pdf").click(function (e) {
        e.preventDefault();
        console.log($("#formTableau").serialize());
        var promoId = $("#promo_id").val();
        var minimum_view = $("#minimum_view").val();
        var date_caculate = $("#date_caculate").val();
        var type = $(this).attr("data-type");
                var art_thumb_mix = $("#art_thumb_mix").val();
        var art_thumb_lyric = $("#art_thumb_lyric").val();
        //        window.location.href = "/tableauExportPdf?promo_id=" + promoId;
//        window.open("/tableauExportPdf?type=" + type + "&promo_id=" + promoId + "&minimum_view=" + minimum_view + "&date_caculate=" + date_caculate + "&art_thumb_mix=" + art_thumb_mix + "&art_thumb_lyric=" + art_thumb_lyric, '_blank');
        window.open("/tableauExportPdf?type=" + type + "&"+$("#formTableau").serialize(), '_blank');
    });
    $(".btn-sync-promos").click(function (e) {
        e.preventDefault();
        $(this).attr("disabled", true);
        $.ajax({
            type: "GET",
            url: "/runSync",
            data: {},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $.Notification.autoHideNotify('success', 'top center', 'Notify', 'The sync is running, it takes about 10 minutes');
            },
            error: function (data) {
                console.log(data);
            }
        });
    });
</script>
@endsection