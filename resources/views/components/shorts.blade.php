@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Shorts</span>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Shorts" type="button" class="btn btn-outline-info btn-import-shorts"><i class="fa fa-plus"></i></button>

            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Shorts</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<form id="formSearchChannel" action="/shorts">
    <input type="hidden" name="limit" id="limit" value="{{$limit}}">
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <h4 class="header-title m-t-0 m-b-30">SHORTS TOPIC</h4>
                <div class="row">
                    @foreach($userTopics as $topic)
                    <div class="col-sm-2 col-xs-12">
                        <div class="card m-b-20 text-xs-center">
                            <div class="card-header">
                                {{$topic->topic}}
                            </div>
                            <div class="card-body">
                                {{$topic->total}}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">LIST SHORTS ({{$datas->total()}})</h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-boom" style="max-width: 99%">
                    {{ csrf_field() }}
                    <div class="row">
                        <table class="table mobile-table-width table-drag">
                            <thead class="thead-default">
                                <tr align="center">
                                    <th style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                    <th style="width: 10%;text-align: left">Username</th>
                                    <th style="width: 10%;text-align: left">Topic</th>
                                    <th style="width: 7%;text-align: center">Videos</th>
                                    <th style="width: 7%;text-align: center">@sortablelink('views','Views')</th>
                                    <th style="width: 10%;text-align: center">Created</th>
                                    <th style="width: 5%;text-align: center">Status</th>
                                    <th style="width: 10%;text-align: right">{{trans('label.col.function')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                <?php
                                $link = "https://www.youtube.com/shorts/$data->video_id";
                                if ($data->status == 5 && $data->drive_url != null) {
                                    $arr = explode(";;", $data->drive_url);
                                    if (count($arr) == 3) {
                                        $link = "https://drive.google.com/file/d/" . $arr[2];
                                    }
                                }
                                ?>
                                <tr class="odd gradeX" align="center">
                                    <td>{{$data->id}}</td>
                                    <td class="text-left">{{$data->username}}</td>
                                    <td class="text-left"><a target="_blank" href="shorts?topic={{$data->topic}}">{{$data->topic}}</a></td>
                                    <td class="text-center"><a target="_blank" href="{{$link}}">{{$data->video_id}}</a></td>
                                    <td class="text-center">{{$data->views}}</a></td>
                                    <td>{{$data->create_time}}</td>

                                    <td>
                                        @if($data->status==0)
                                        New
                                        @elseif($data->status==1)
                                        Downloading
                                        @elseif($data->status==3)
                                        Downloaded
                                        @elseif($data->status==4)
                                        Download Error
                                        @elseif($data->status==5)
                                        Drive
                                        @endif    
                                    </td>
                                    <td style="text-align: right">
                                        <span data-toggle='tooltip' data-placement='top' data-original-title='Retry download'><button type="button" class="color-y btn btn-remove-short" data-type="retry" data-short-id="{{$data->id}}"><i class="fa fa-refresh"></i></button></span>
                                        <span data-toggle='tooltip' data-placement='top' data-original-title='Remove'><button type="button" class="color-red btn btn-remove-short" data-type="delete" data-short-id="{{$data->id}}"><i class="ion-trash-a"></i></button></span>

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
@include('dialog.importshorts')
@endsection

@section('script')
<script type="text/javascript">

    $(".btn-remove-short").click(function (e) {
        e.preventDefault();

        var boomId = $(this).attr("data-short-id");
        var type = $(this).attr("data-type");
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';

        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }

        $.ajax({
            type: "GET",
            url: "shortUpdate",
            data: {
                id: boomId,
                type: type
            },
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                if (data.status == "success") {
                    if (type == 'delete') {
                        $this.closest("tr").hide();
                    }
                }

            },
            error: function (data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);

            }
        });


    });
    $("#topic_select").change(function () {
        $("#topic").val($(this).val());
    });
    $(".btn-export").click(function () {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var url = "/exportBoom";
        window.location.href = url;
        $this.html($this.data('original-text'));


    });

    $(".btn-import-shorts").click(function (e) {
        e.preventDefault();
        clearForm();
        $('#dialog_import_shorts').modal({
            backdrop: false
        });
    });


    $(".btn-edit-song").click(function (e) {
        e.preventDefault();
        var id = $(this).attr("data-boom-id");
        $.ajax({
            type: "GET",
            url: "boom/" + id,
            data: {},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.status === 'error') {
                    $.Notification.notify(data.status, 'top center', '', data.message);
                } else {
                    $("#songName").val(data.song_name);
                    $("#deezerId").val(data.deezer_id);
                    $("#artist").val(data.artist);
                    $("#deezerArtistId").val(data.deezer_artist_id);
                    $("#bom_id").val(data.id);
                    $("#social").val(data.social);
                    $("#localId").val(data.local_id);
                    $("#genre").val(data.genre).change();
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        $('#dialog_import_bom').modal({
            backdrop: false
        });
    });

    function clearForm() {
        $("#shorts").val("");
        $("#topic").val("");
    }

    $(".btn-save-shorts").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form = $("#frmShorts");
        var formData = form.serialize();
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "shortsStore",
            data: formData,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                if (data.status == "success") {
//                    setTimeout(location.reload(), 3000);
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