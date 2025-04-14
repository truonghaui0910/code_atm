@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Intro</span>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Intro" type="button" class="btn btn-outline-info btn-import-intro m-r-5"><i class="fa fa-plus"></i></button>

            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Intro</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <form id="formSearchChannel" action="/intros">
                <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                <div class="row">

                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Users</label>
                            <div class="col-12">
                                <select class="form-control" name="c5">
                                    {!!$listUser!!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Intro Name</label>
                            <div class="col-12">
                                <input class="form-control" type="text" name="intro_name" value="{{$request->intro_name}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Channel Name</label>
                            <div class="col-12">
                                <input class="form-control" type="text" name="channel_name" value="{{$request->channel_name}}">
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
            <h4 class="header-title m-t-0 m-b-30">INTRO ({{$datas->total()}} files)</h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-boom" style="max-width: 99%">
                    {{ csrf_field() }}
                    <div class="row">
                        <table class="table mobile-table-width table-drag">
                            <thead class="thead-default">
                                <tr align="center">
                                    <th style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                    <th style="width: 10%;text-align: left">Username</th>
                                    <th style="width: 10%;text-align: center">App</th>
                                    <th style="width: 10%;text-align: left">Intro Name</th>
                                    <th style="width: 10%;text-align: center">Channel Type</th>
                                    <th style="width: 10%;text-align: left">Channel Name</th>
                                    <th style="width: 10%;text-align: center">Video Type</th>
                                    <th style="width: 10%;text-align: center">Intro Type</th>
                                    <th style="width: 10%;text-align: right">{{trans('label.col.function')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                <tr class="odd gradeX" align="center">
                                    <td>{{$data->id}}</td>
                                    <td style="text-align: left">{{$data->username}}</td>
                                    <td style="text-align: center">{{$data->app}}</td>
                                    <td style="text-align: left">{{$data->intro_name}}</td>
                                    <td style="text-align: center">{{$data->channel_type}}</td>
                                    <td style="text-align: left">{{$data->channel_name}}</td>
                                    <td style="text-align: center">{{$data->video_type}}</td>
                                    <td style="text-align: center">{{$data->intro_type}}</td>

                                    <td style="text-align: right">
                                        <span data-toggle='tooltip' data-placement='top' data-original-title='Edit'><button type="button" class="color-green btn btn-edit-intro" data-id="{{$data->id}}"><i class="fa fa-edit"></i></button></span>
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

@include('dialog.importintro')
@include('dialog.brandsubmitimage')
@endsection

@section('script')
<script type="text/javascript">

    $(".btn-submit-upload").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var type = $(this).val();
        var form_file = new FormData();
        var file = $("#thumb_upload").val();
        if (file != '') {
            console.log(file);
        } else {
            $.Notification.notify('error', 'top center', '', "You must choose a image");
            return;
        }

        form_file.append('image', $("#thumb_upload")[0].files[0]);
        form_file.append('_token', '{{csrf_token()}}');
        form_file.append('path', 'intro/thumb');
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/image-upload",
            data: form_file,
            contentType: false,
            processData: false,
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                if (data.status == 'success') {
                    var url = "http://automusic.win/" + data.uploaded_image;
                    $("#intro_thumb").val(url);
                    $.Notification.notify(data.status, 'top center', '', data.message);
                } else {
                    $.Notification.notify(data.status, 'top center', '', data.message[0]);
                }
            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    function clearForm(){
        $("#intro_id").val("");
        $("#app").val("MOONAZ").change();
        $("#channel_type").val("BOOM").change();
        $("#channel_id").val("-1").change();
        $("#video_type").val("LONG").change();
        $("#intro_type").val("TEXT").change();
        $("#intro_name").val("");
        $("#intro_thumb").val("");
        $("#intro_link").val("");
    }

    $(".btn-import-intro").click(function (e) {
        e.preventDefault();
        $('#dialog_import_intro').modal({
            backdrop: false
        });
    });
    $(".btn-edit-intro").click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var id = $(this).attr("data-id");
        $.ajax({
            type: "GET",
            url: "introsFind",
            data: {"id": id},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $this.html($this.data('original-text'));
                    $("#intro_id").val(id);
                    $("#app").val(data.app).change();
                    $("#channel_type").val(data.channel_type).change();
                    $("#channel_id").val(data.channel_id).change();
                    $("#video_type").val(data.video_type).change();
                    $("#intro_type").val(data.intro_type).change();
                    $("#intro_name").val(data.intro_name);
                    $("#intro_thumb").val(data.intro_thumb);
                    $("#intro_link").val(data.intro_link);
                $('#dialog_import_intro').modal({
                    backdrop: false
                });

            },
            error: function (data) {
                console.log('Error:', data);
                $this.html($this.data('original-text'));

            }
        });

    });

    $(".btn-save-intro").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form = $("#frmAdd");
        var formData = form.serialize();
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "introsStore",
            data: formData,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
//                if (data.status === "success") {
//                    setTimeout(function () {
//                        location.reload();
//                      }, 3000);
//                }
            },
            error: function (data) {
                console.log('Error:', data);
                $this.html($this.data('original-text'));

            }
        });
    });

    $(".btn-submit-image").click(function (e) {
        e.preventDefault();
        var brandId = $(this).attr("data-id");
        $("#banner").val("");
        $("#avatar").val("");
        $("#brand_id").val(brandId);
        $('#dialog_brand_submit_image').modal({
            backdrop: false
        });
    });

    $(".btn-submit-brand-image").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form = $("#frmSubmitBrandImage");
        var formData = form.serialize();
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "brandingUpdate",
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                if (data.status === "success") {
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                }
            },
            error: function (data) {
                console.log('Error:', data);
                $this.html($this.data('original-text'));

            }
        });
    });

</script>
@endsection