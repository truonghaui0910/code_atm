@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Branding</span>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Branding" type="button" class="btn btn-outline-info btn-import-brand m-r-5"><i class="fa fa-plus"></i></button>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New About Section" type="button" class="btn btn-outline-info btn-import-about"><i class="fa fa-gear"></i></button>
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Branding</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <form id="formSearchChannel" action="/branding">
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
                            <label class="col-12 col-form-label">Genre</label>
                            <div class="col-12">
                                <select class="form-control" name="channel_genre">
                                    {!!$genres!!}
                                </select>
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
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Status Design</label>
                            <div class="col-12">
                                <select class="form-control" name="status_design">
                                    {!!$statusDesign!!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Status Use</label>
                            <div class="col-12">
                                <select class="form-control" name="status_use_brand">
                                    {!!$statusUseBrand!!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Status Brand</label>
                            <div class="col-12">
                                <select class="form-control" name="status_brand">
                                    {!!$statusBrand!!}
                                </select>
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
            <h4 class="header-title m-t-0 m-b-30">BRANDED FROM {{date("Y/m/d",$monday)}} - {{date("Y/m/d",$sunday)}}</h4>
            <div class="row">
                @foreach($workeds as $work)
                <div class="col-sm-2 col-xs-12">
                    <div class="card m-b-20 text-xs-center">
                        <div class="card-header">
                            {{$work->manager}} - <b>{{$work->total}}</b> channel<?php echo $work->total>1?"s":""; ?>
                        </div>
                        <div class="card-body">
                            @foreach($workedDetails as $detail)
                            @if($work->manager == $detail->manager)
                            {{date('Y/m/d',strtotime($detail->design_date))}} : {{$detail->total}}<br>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">BRANDING ({{$datas->total()}} Jobs)</h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-boom" style="max-width: 99%">
                    {{ csrf_field() }}
                    <div class="row">
                        <table class="table mobile-table-width table-drag" style="border-collapse: inherit;table-layout: fixed;">
                            <thead class="thead-default">
                                <tr align="center">
                                    <th style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                    <th style="width: 10%;text-align: left">Manager</th>
                                    <th style="width: 10%;text-align: left">Designer</th>
                                    <th style="width: 10%;text-align: left">Channel Name</th>
                                    <th style="width: 10%;text-align: center">Genre</th>
                                    <th style="width: 15%;text-align: left">Style</th>
                                    <th style="width: 15%;text-align: center">Created</th>
                                    <th style="width: 15%;text-align: center">Finished</th>
                                    <th style="width: 15%;text-align: center">Status</th>

                                    <th style="width: 10%;text-align: right">{{trans('label.col.function')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                <tr class="odd gradeX" align="center">
                                    <td>{{$data->id}}</td>
                                    <td style="text-align: left">{{$data->manager}}</td>
                                    <td style="text-align: left">{{$data->designer}}</td>
                                    <td style="text-align: left">{{$data->channel_name}}</td>
                                    <td style="text-align: center">{{$data->genre}}</td>
                                    <td style="text-align: left" class="text-ellipsis" data-toggle='tooltip' data-placement='top' data-original-title='{{$data->style}}'><span>{{$data->style}}</span></td>
                                    <td style="text-align: center"><?php
                                    $t = strtotime($data->create_time)- 7 * 3600;
                                        echo \App\Common\Utils::calcTimeText($t);
                                    ?></td>
                                    <td style="text-align: center">{{$data->update_time}}</td>
                                    <td style="text-align: center">

                                        @if($data->status_brand==1)
                                        <span class="badge badge-danger">Online</span>
                                        @elseif($data->status_use==1)

                                        <span class="badge badge-primary">Used</span>
                                        @elseif($data->status_design==1)

                                        <span class="badge badge-success">Finished Design</span>
                                        @elseif($data->status_design==0)

                                        <span class="badge badge-info">New</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right">
                                            <span data-toggle='tooltip' data-placement='top'
                                              data-original-title='Views this brand'>
                                                <button type="button"
                                                                                     class="btn btn-edit-brand" data-brand-id="{{ $data->id }}"><i
                                                    class="ion-edit"></i></button></span>
                                        @if($data->status_design==0)
                                        <span data-toggle='tooltip' data-placement='top' data-original-title='Submit link drive of avatar and banner'><button type="button" class="color-green btn btn-submit-image" data-id="{{$data->id}}"><i class="ion-share"></i></button></span>
                                        @endif
                                        @if($data->local_avatar!=null)
                                        <a class="btn btn-outline-info" data-html='true' data-toggle='tooltip' data-placement='top' data-original-title='<span><img width="100px" src="http://automusic.win/brand_image/{{$data->local_avatar}}"/><br>Avatar</span>' target="_blank" href="/brand_image/{{$data->local_avatar}}"><i class="ion-images"></i></a>
                                        @endif
                                        @if($data->local_banner!=null)
                                        
                                        <a class="btn btn-outline-info" data-html='true' data-toggle='tooltip' data-placement='top' data-original-title='<span><img width="100px" src="http://automusic.win/brand_image/{{$data->local_banner}}"/><br>Banner</span>' target="_blank" href="/brand_image/{{$data->local_banner}}"><i class="ion-images"></i></a>
                                        @endif                                        
                                        @if($data->local_logo!=null)
                                        <a class="btn btn-outline-info" data-html='true' data-toggle='tooltip' data-placement='top' data-original-title='<span><img width="100px" src="http://automusic.win/brand_image/{{$data->local_logo}}"/><br>Logo</span>' target="_blank" href="/brand_image/{{$data->local_logo}}"><i class="ion-images"></i></a>
                                        @endif                                        

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

@include('dialog.brandimport')
@include('dialog.aboutsectionimport')
@include('dialog.brandsubmitimage')
@endsection

@section('script')
<script type="text/javascript">
    $(".btn-edit-brand").click(function (e) {
        e.preventDefault();
        var id = $(this).attr("data-brand-id");
//        $(".div_group_show").hide();
        $("#brand_id").val(id);
        $.ajax({
            type: "GET",
            url: "/brandingFind",
            data: {brand_id:id},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $("#channel_name").val(data.channel_name);
                $("#style").val(data.style);
                $("#designer").val(data.designer);
                $("#genre").val(data.genre);
                $("#channel_subgenre").val(data.sub_genre);

            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        $('#dialog_import_brand').modal({
            backdrop: false
        });
    });
    
    
    function appendSection(){
        var html = '<div class="col-12">';
            html +='<textarea class="form-control m-b-5" rows="5" name="about_section[]" spellcheck="false" style="line-height: 1.25;height: 65px"></textarea> </div>';
        $("#section-append").append(html);
    }
    $(".btn-import-about").click(function (e) {
        e.preventDefault();
        $('#dialog_import_about_section').modal({
            backdrop: false
        });
    });
    
    $(".btn-save-about-section").click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form = $("#frmAddAbout");
        var formData = form.serialize();
        
        $.ajax({
            type: "POST",
            url: "brandingSaveAboutSection",
            data: formData,
            dataType: 'json',
            success: function (data) {
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

    $(".btn-import-brand").click(function (e) {
        $("#brand_id").val(null);
        e.preventDefault();
        $('#dialog_import_brand').modal({
            backdrop: false
        });
    });

    $(".btn-save-brand").click(function (e) {
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
            url: "brandingStore",
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
        $("#brand_id_image").val(brandId);
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