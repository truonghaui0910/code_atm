@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">TIKTOK CHARTS</span>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Export to csv" type="button" class=" m-l-5 btn btn-outline-info btn-export"><i class="fa fa-file-excel-o"></i></button>
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Tiktok Charts</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <form id="formFilter" action="/tiktokcharts">
                <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                <div class="row">

                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Country</label>
                            <div class="col-12">
                                <select class="form-control" name="country">
                                    {!!$country!!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Date</label>
                            <div class="col-12">
                                <select class="form-control" name="date">
                                    {!!$date!!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Tags</label>
                            <div class="col-12">
                                <input id="tags" class="form-control" type="text" name="tags" value="{{$request->tags}}">
                            </div>
                        </div>
                    </div>                    
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Song Name</label>
                            <div class="col-12">
                                <input id="name" class="form-control" type="text" name="name" value="{{$request->name}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Artist</label>
                            <div class="col-12">
                                <input id="artist" class="form-control" type="text" name="artist" value="{{$request->artist}}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">&nbsp;</label>
                            <div class="col-12">
                                <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">TIKTOK CHARTS ({{$datas->total()}})</h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-boom" style="max-width: 99%">
                    {{ csrf_field() }}
                    <div class="row">
                        <table class="table mobile-table-width table-drag">
                            <thead class="thead-default">
                                <tr align="center">
                                    <th style="width: 2%;text-align: center">@sortablelink('id','ID')</th>
                                    <th style="width: 5%;text-align: center">Rank</th>
                                    <th style="width: 10%;text-align: left">Name</th>
                                    <th style="width: 10%;text-align: left">Artist</th>
                                    <th style="width: 5%;text-align: center">Music</th>
                                    <th style="width: 10%;text-align: left">Deezer Id</th>
                                    <th style="width: 10%;text-align: center">@sortablelink('day_report','Report Date')</th>
                                    <th style="width: 5%;text-align: center">@sortablelink('time_on_chart','Time On Chart')</th>
                                    <th style="width: 5%;text-align: center">Tags</th>
                                    <th style="width: 25%;text-align: left">Country Rank</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                <tr class="odd gradeX" align="center">

                                    <td style="text-align: center">{{$data->id}}</td>
                                    <td style="text-align: center">{{$data->rank_country}}</td>
                                    <td style="text-align: left"><a data-toggle="tooltip" data-placement="top" data-original-title="{{$data->name}}" target="_blank" href="https://www.tiktok.com/music/{{$data->tiktok_track_id}}">{{$data->short_name}}</a></td>
                                    <td style="text-align: left"><span data-toggle="tooltip" data-placement="top" data-original-title="{{$data->artist_name}}">{{$data->short_artist}}</span></td>
                                    <td style="text-align: center"><a target="_blank" href="{{$data->url_download}}"><i class="ti-music-alt"></i></a></td>
                                    <td style="text-align: left"><a target="_blank" href="https://www.deezer.com/us/track/{{$data->id_deezer}}">{{$data->id_deezer}}</a></td>
                                    <td style="text-align: center">{{$data->day_report}}</td>
                                    <td style="text-align: center"><span class="cur-poiter" data-toggle="tooltip" data-placement="top" data-original-title="{{$data->rank_status}}">{{$data->time_on_chart}}</span></td>
                                    <td style="text-align: center">{{$data->tags}}</td>
                                    <td style="text-align: left">
                                        <?php
                                        if ($data->country_rank != null) {
                                            $ranks = explode(",", $data->country_rank);
                                            $ranksNum = json_decode($data->overall_rank);
                                            $number = "";
                                            foreach ($ranks as $rank) {
                                                if ($rank != "") {
                                                    foreach($ranksNum as $num){
                                                        if($rank == $num->country){
                                                            $number = $num->over_rank;
                                                            break;
                                                        }
                                                    }
                                                    echo "<span class='subgenre cur-poiter' data-toggle='tooltip' data-placement='top' data-original-title='$number'>$rank</span>";
                                                }
                                            }
                                        }
                                        ?>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="row">
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
                                </select>&nbsp;
                                <?php if (isset($datas)) { ?>
                                    {!!$datas->links()!!}
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script type="text/javascript">
    $(".btn-export").click(function() {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form = $("#formFilter").serialize();
        var url = "/exportTiktokCharts?"+form;
        window.location.href = url;
        $this.html($this.data('original-text'));


    });   
</script>
@endsection