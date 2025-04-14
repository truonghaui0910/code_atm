@extends('layouts.master')

@section('content')

<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Chart Metric Spotify Playlist </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Chart Metric Spotify Playlist ($total)</li>
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
                    <div class="col-6">
                        <div class="m-b-15 mdi-size-filter">
                            <a  href="javascript:openFilter()" ><i class="mdi mdi-filter-variant"></i> Filter</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-md-6">

                                    </div>
                                    <div class="col-md-3">
                                        <select id="genre" class="form-control pull-right col-3" name="genre">
                                            <option value="">All</option>
                                            <option value="405029">Pop</option>
                                            <option value="405028">Latin</option>
                                            <option value="405025">Hiphop</option>
                                        </select> 
                                    </div>
                                    <div class="col-md-3">
                                        <select id="country" class="form-control pull-right col-3" name="country">
                                            <option value="xx">All</option>
                                            <option value="us">US</option>
                                            <option value="gb">UK</option>
                                        </select>
                                    </div>
                                </div>


                                <div id="channel-chart" style="overflow: auto;padding-left: 0px;padding-right: 0px;height: 600px;">
                                    <table id="spotify-table" class="fix-header-table table" style="width: 99.5%;table-layout:fixed;">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">#</th>
                                                <th style="width: 20%">Playlist</th>
                                                <th style="width: 20%">Followers</th>
                                                <th style="width: 10%">7-Day Change</th>
                                                <th style="width: 10%">28-Day Change</th>
                                                <th style="width: 10%">Genres</th>
                                                <th style="width: 10%">Personalized</th>
                                                <th style="width: 10%">Track Age</th>
                                                <th style="width: 10%">Last Updated </th>
                                            </tr>
                                        </thead>
                                        <?php $i = 1; ?>
                                        @foreach($datas as $data)
                                        <tbody>
                                            <tr>
                                                <th scope="row">{{$data->position}}</th>
                                                <td>
                                                    <div>
                                                        <div>
                                                            <img class="img-sm" src="{{$data->image_url}}">
                                                            <a target="_blank" href="https://open.spotify.com/playlist/{{$data->playlist_id}}">{{$data->name}}</a>
                                                        </div>

                                                    </div>
                                                </td>
                                                <td>{{number_format($data->followers, 0, '.', ',')}}</td>
                                                <td>
                                                    @if($data->fdiff_percent_week > 0)
                                                    <i class="ti-angle-double-up color-green font-12"></i>
                                                    @elseif($data->fdiff_percent_week < 0)
                                                    <i class="ti-angle-double-down color-red font-12"></i>
                                                    @endif
                                                    {{$data->fdiff_week}} ({{round($data->fdiff_percent_week,1)}}%)
                                                </td>
                                                <td>
                                                    @if($data->fdiff_percent_month > 0)
                                                    <i class="ti-angle-double-up color-green font-12"></i>
                                                    @elseif($data->fdiff_percent_month < 0)
                                                    <i class="ti-angle-double-down color-red font-12"></i>
                                                    @endif
                                                    {{$data->fdiff_month}} ({{round($data->fdiff_percent_month,1)}}%)
                                                </td>
                                                <td>{{$data->genre}}</td>
                                                <td>{{$data->personalized?'Yes':'No'}}</td>
                                                <td>{{$data->catalog}}</td>
                                                <td>{{explode("T", $data->last_updated)[0]}}</td>
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
                            <div class="pull-right disp-flex">
                                <ul class="pagination">
                                    @if($previous!=null)
                                    <li><a href="{{$previous}}" rel="previous">«</a></li>
                                    @endif
                                    @if($next!=null)
                                    <li><a href="{{$next}}" rel="next">»</a></li>
                                    @endif
                                </ul>
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

    $("#country").change(function () {
        var url = window.location.href;
        var href = new URL(url);
        var country = $(this).val();
        href.searchParams.set('country', country);
        window.location.href = href.toString();
    });
    $("#genre").change(function () {
        var url = window.location.href;
        var href = new URL(url);
        var genre = $(this).val();
        href.searchParams.set('genre', genre);
        window.location.href = href.toString();
    });



</script>
@endsection

