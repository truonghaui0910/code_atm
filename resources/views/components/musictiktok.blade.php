@extends('layouts.master')

@section('content')

<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Tiktok Music Database ({{number_format($datas->total(), 0, '.', ',')}} songs)</h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Tiktok Music</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">

                                <h4 class="header-title m-t-0 m-b-30">Tiktok music list</h4>
                                <form id="form-search " action="/musictiktok">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-8 col-form-label">Date</label>
                                            <select id="date" class="form-control" name="date" >
                                                {!!$dateSelect!!}
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-8 col-form-label">Song name</label>
                                                <input id="name" class="form-control" type="text" name="name" value="{{$request->name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-8 col-form-label">&nbsp;</label>
                                            <div class="col-12">
                                                <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i class="fa fa-search"></i> Search</button>   
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </form>
                                <div id="channel-chart" style="overflow: auto;padding-left: 0px;padding-right: 0px;height: 600px;">
                                    <table id="spotify-table" class="table" style="width: 99.5%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;text-align: center">@sortablelink('rank','Rank')</th>
                                                <th style="width: 6%;text-align: center">@sortablelink('change','Change')</th>
                                                <th style="width: 10%">Track</th>
                                                <!--<th style="width: 10%">Label</th>-->
                                                <th style="width: 10%">Release Date</th>
                                                <th style="width: 5%">1-day video count</th>
                                                <th style="width: 15%">1-day Trend</th>
                                                <th style="width: 10%">Peak position</th>
                                                <th style="width: 10%">Peak date</th>
                                                <th style="width: 15%">Rank Trend</th>
                                                <th style="width: 5%">Day on chart</th>
                                                <th style="width: 5%">@sortablelink('velocity','7-day velocity')</th>
                                                <th style="text-align: center;width: 120px" colspan="3">Action</th>
                                            </tr>
                                        </thead>
                                        <?php $i = 1; ?>
                                        @foreach($datas as $data)
                                        <tbody>
                                            <tr>
                                                <td scope="row">{{$data->rank}}</td>
                                                <td scope="row">
                                                    @if($data->change > 0)
                                                    <i class="ti-angle-double-up color-green font-12"></i>
                                                    @elseif($data->change < 0)
                                                    <i class="ti-angle-double-down color-red font-12"></i>
                                                    @endif
                                                    {{$data->change}}
                                                </td>
                                                <td scope="row">
                                                    <div>
                                                        <div>
                                                            <img class="img-sm" src="{{$data->image_url}}">
                                                            <a target="_blank" href="https://www.tiktok.com/music/{{$data->tiktok_track_id}}">{{$data->name}}</a>
                                                        </div>

                                                    </div>
                                                </td>
                                                <!--<td scope="row" class="hide-words">{{$data->album_label}}</td>-->
                                                <td scope="row">{{substr($data->release_date,0,10)}}</td>
                                                <td scope="row">{{$data->daily_posts}}</td>
                                                <td>
                                                    <div style="width: 170px;height: 50px"><canvas id="chart-video-daily-{{$data->id}}" width="100" height="50"></canvas></div>
                                                </td>
                                                <td scope="row">{{$data->peak_rank}}</td>
                                                <td scope="row">{{substr($data->peak_date,0,10)}}</td>
                                                <td>
                                                    <div style="width: 170px;height: 50px"><canvas id="chart-rank-{{$data->id}}" width="100" height="50"></canvas></div>
                                                </td>
                                                <td scope="row">{{$data->time_on_chart}}</td>
                                                <td scope="row">{{round($data->velocity,2)}}</td>


                                                <td style="text-align: center">
                                                    @if($data->url_download != null)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make lyric' href="http://api.automusic.win/spotify/lyric/{{$data->id}}/{{$user_login->user_name}}" class="<?php echo ($data->has_lyric == 1 ? "color-b" : "") ?>"><i class="fa fa-music"></i></a>
                                                    @else
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Google search' href="http://www.google.com/search?q={{$data->name}}+{{$data->artists}}+lyrics" class="<?php echo ($data->has_lyric == 1 ? "color-b" : "") ?>"><i class="fa fa-google"></i></a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($data->has_lyric)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V1' class="color-b" href="http://automusic.win/lyricconfig?type=deezer&data={{$data->id_deezer}}" ><i class="fa fa-video-camera"></i></a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($data->has_lyric)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V2' class="color-red" href="http://studio.automusic.win/?deezerId={{$data->id_deezer}}" ><i class="fa fa-video-camera"></i></a>
                                                    @endif
                                                </td>

                                            </tr>
                                        </tbody>
                                        @endforeach
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-md-6 ">
                            <div>

                                <?php
                                $info = str_replace('_START_', $datas->firstItem() != null ? $datas->firstItem() : '0', trans('label.title.sInfo'));
                                $info = str_replace('_END_', $datas->lastItem() != null ? $datas->lastItem() : '0', $info);
                                $info = str_replace('_TOTAL_', $datas->total(), $info);
                                echo $info;
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="pull-right disp-flex">
                                <select id="cbbLimit" name="limit" aria-controls="tbl-title" class="form-control input-sm">
                                    {!!$limitSelectbox!!}
                                </select>
                                <?php if (isset($datas)) { ?>
                                    {!!$datas->links()!!}
                                <?php } ?>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

@include('dialog.log')

@endsection

@section('script')
<script type="text/javascript">
<?php
foreach ($datas as $data) {
    $id = $data->id;
    echo "var dataViewRank$id = new Array();";
    echo "var dataViewPost$id = new Array();";
    echo "var labelView$id = new Array();";
    $charts = json_decode($data->rank_stats);
    foreach ($charts as $chart) {
        $date = substr($chart->timestp, 0, 10);
        echo "dataViewRank$id.push(" . $chart->rank . ");\n";
        echo "dataViewPost$id.push(" . $chart->daily_posts . ");\n";
        echo "labelView$id.push('" . $date . "');\n";
    }
    echo "drawLineChartMini('chart-rank-$id', dataViewRank$id, labelView$id, '');";
    echo "drawLineChartMini('chart-video-daily-$id', dataViewPost$id, labelView$id, '');";
}
?>
</script>
@endsection

