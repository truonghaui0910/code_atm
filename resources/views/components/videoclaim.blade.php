@extends('layouts.master')

@section('content')
<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Video Claims ({{ $datas->total()}})</h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Channel</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">IMPORT VIDEOS</h4>
            <form id="formadd" method="POST">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">User name</label>
                            <div class="col-12">
                                <input type="text" name="user_name" id="user_name" class="form-control colorpicker-element" value="{{$user_login->user_name}}">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Video List</label>
                            <div class="col-12">
                                <textarea class="form-control" rows="5" id="list_video" name="list_video" spellcheck="false" style="line-height: 1.25;height: 300px"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="button" class="btn btn-outline-info btn-sm btn-import-video"><i class="fa fa-upload"></i> Import</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@if (in_array('1', explode(",", $user_login->role)))
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">STATISTICS</h4> 
            <button type="button" class="btn btn-outline-info btn-sm btn-scan-video" value="1"><i class="fa fa-money"></i> Scan</button>
            <button type="button" class="btn btn-outline-warning btn-sm btn-scan-video" value="2"><i class="fa fa-money"></i> ReScan</button>
            <br>
            <div id="messageValidate" class="m-t-10"></div>
            <div style="overflow: auto">
                <table id="demo-foo-filtering" class="table table-striped table-bordered toggle-circle m-b-0 default footable-loaded footable" data-page-size="7">
                    <thead>
                        <tr>
                            
                            <th style="width: 10%" data-toggle="true" class="footable-visible footable-first-column footable-sortable">User name</th>
                            <th style="width: 10%" class="footable-visible footable-sortable text-center">Views</th>
                            <th style="width: 10%" class="footable-visible footable-sortable text-center">Monthly Views</th>
                            <th style="width: 10%" class="footable-visible footable-sortable text-center">Date</th>


                        </tr>
                    </thead>

                    <tbody>
                        @foreach($statistics as $statistic)
                        <tr class="footable-even" style="">
                            <td class="footable-visible ">{{$statistic->user_name}}</td>
                            <td class="footable-visible text-center">{{$statistic->views}}</td>
                            <td class="footable-visible text-center">{{$statistic->inc_views}}</td>
                            <td class="footable-visible text-center">{{$statistic->date_scan}}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endif
<form id="formSearchChannel" action="/videoclaim" method="GET">
    {{ csrf_field() }}
    <input type="hidden" name="limit" id="limit" value="{{$request->limit}}">
</form>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div style="overflow: auto">
                <table id="demo-foo-filtering" class="table table-striped table-bordered toggle-circle m-b-0 default footable-loaded footable" data-page-size="7">
                    <thead>
                        <tr>
                            <th style="width: 3%" class="footable-visible footable-sortable text-center">@sortablelink('id','ID')</th>
                            <th style="width: 10%" data-toggle="true" class="footable-visible footable-first-column footable-sortable">User name</th>
                            <th style="width: 10%" class="footable-visible footable-sortable text-center">Video id</th>
                            <th style="width: 10%" class="footable-visible footable-sortable text-center">@sortablelink('views','Views')</th>
                            <th style="width: 10%" class="footable-visible footable-sortable text-center">Monthly views</th>
                            <th style="width: 10%" class="footable-visible footable-sortable text-center">Date scan</th>
                            <th style="width: 5%" class="footable-visible footable-sortable text-center">@sortablelink('status','Status')</th>

                        </tr>
                    </thead>

                    <tbody>
                        @foreach($datas as $data)
                        <tr class="footable-even" style="">
                            <td class="footable-visible text-center">{{$data->id}}</td>
                            <td class="footable-visible ">{{$data->user_name}}</td>
                            <td class="footable-visible footable-first-column text-center">
                                <a target="_blank" href="https://www.youtube.com/watch?v={{$data->video_id}}">{{$data->video_id}}</a>
                            </td>
                            <td class="footable-visible text-center">{{$data->views}}</td>
                            <td class="footable-visible text-center">{{$data->inc_views}}</td>
                            <td class="footable-visible text-center">{{$data->date_scan}}</td>
                            <td class="footable-visible text-center">{{$data->status}}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-4 text-xs-center">
                    <div class="form-inline ">
                        <div class="form-group">
                            <label class="control-label m-r-5">Show</label>
                            <select id="cbbLimit" class="form-control input-sm">
                                {!!$limitSelectbox!!}
                            </select>
                            <label class="control-label m-l-5"> entries</label>
                        </div>  


                    </div>
                </div>
                <div class="col-md-8 text-xs-center">
                    <div class="pull-right">

                        {!!$datas->links()!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script type="text/javascript">


    $(".btn-import-video").click(function (e) {

        var form = $("#formadd");
        var formData = form.serialize();
        console.log(formData);

        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/addvideoclaim",
            data: formData,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            },
            error: function (data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);
            }
        });
    });
    $(".btn-scan-video").click(function (e) {
        $("#messageValidate").html("");
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }

        e.preventDefault();
        $.ajax({
            type: "GET",
            url: "/scanvideoclaim",
            data: {"command": $(this).val()},
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                var content = data.content;
                var html = '<div class="alert alert-' + data.status + '"><ul>';
                html += '<li>' + content + '</li>';
                html += '</ul>';
                $("#messageValidate").html(html);
            },
            error: function (data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);
            }
        });
    });
</script>
@endsection

