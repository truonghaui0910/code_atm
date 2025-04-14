@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Studio Drive</span>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Drive Link" type="button" class="btn btn-outline-info btn-import-studio-drive m-r-5"><i class="fa fa-plus"></i></button>
                
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Studio Drive</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <form id="formSearchChannel" action="/studio/drive">
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
            <h4 class="header-title m-t-0 m-b-30">Drive ({{$datas->total()}})</h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-boom" style="max-width: 99%">
                    {{ csrf_field() }}
                    <div class="row">
                        <table class="table mobile-table-width table-drag" style="border-collapse: inherit;table-layout: fixed;">
                            <thead class="thead-default">
                                <tr align="center">
                                    <th style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                    <th style="width: 15%;text-align: left">Manager</th>
                                    <th style="width: 15%;text-align: left">Channel Name</th>
                                    <th style="width: 35%;text-align: center">Video</th>
                                    <th style="width: 10%;text-align: center">Created</th>
                                    <th style="width: 5%;text-align: center">Status</th>
                                    <th style="width: 10%;text-align: right">{{trans('label.col.function')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                <tr class="odd gradeX" align="center">
                                    <td>{{$data->id}}</td>
                                    <td style="text-align: left">{{$data->username}}</td>
                                    <td style="text-align: left">{{$data->channel_name}}</td>
                                    <td style="text-align: center" ><a target="_blank" href="https://drive.google.com/file/d/{{$data->drive_video_id}}/view">{{$data->drive_video_id}}</a></td>
                                    <td style="text-align: center">{{$data->created}}</td>
                                    <td style="text-align: center">
                                        @if($data->status==0)
                                            <span class="badge badge-primary">New</span>
                                        @elseif($data->status==1)
                                            <span class="badge badge-warning">Running</span>
                                        @elseif($data->status==2)
                                            <span class="badge badge-danger">Error</span>
                                        @elseif($data->status==3)
                                        <span class="badge badge-success">Success</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right">
                                        <span data-toggle='tooltip' data-placement='top' data-original-title='delete'><button type="button" class="color-red btn btn-delete-drive" data-id="{{$data->id}}"><i class="fa fa-trash"></i></button></span>
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
@include('dialog.importstudiodrive')
@endsection

@section('script')
<script type="text/javascript">

    
    

    $(".btn-import-studio-drive").click(function (e) {
        e.preventDefault();
        $('#dialog_import_studio_drive').modal({
            backdrop: false
        });
    });

    $(".btn-save-studio-drive").click(function (e) {
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
            url: "/studio/drive/save",
            data: formData,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
            },
            error: function (data) {
                console.log('Error:', data);
                $this.html($this.data('original-text'));

            }
        });
    });
    
    $(".btn-delete-drive").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var id = $this.attr("data-id");
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: "/studio/drive/update",
            data: {id:id},
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                $this.closest("tr").hide();
            },
            error: function (data) {
                console.log('Error:', data);
                $this.html($this.data('original-text'));

            }
        });
    });



//    var channelNames = [
//        { name: "channel1", avatar: "https://yt3.ggpht.com/ytc/AIdro_nchJSx2XoNYjCd2I4rm4ex4mKUCLemS_v46iCHDFIpSQA=s240-c-k-c0x00ffffff-no-rj", subscribers: 1000 },
//        { name: "channel2", avatar: "https://yt3.ggpht.com/ytc/AIdro_nchJSx2XoNYjCd2I4rm4ex4mKUCLemS_v46iCHDFIpSQA=s240-c-k-c0x00ffffff-no-rj", subscribers: 1500 },
//        { name: "channel3", avatar: "https://yt3.ggpht.com/ytc/AIdro_nchJSx2XoNYjCd2I4rm4ex4mKUCLemS_v46iCHDFIpSQA=s240-c-k-c0x00ffffff-no-rj", subscribers: 2000 },
//        { name: "channel4", avatar: "https://yt3.ggpht.com/ytc/AIdro_nchJSx2XoNYjCd2I4rm4ex4mKUCLemS_v46iCHDFIpSQA=s240-c-k-c0x00ffffff-no-rj", subscribers: 2500 }
//    ];
    var channelNames;
    $.get("/loadChannels", function (data) {
        channelNames = data;
    }, "json");
    var currentIndex = -1;

    $('#channel_name').on('input', function() {
        var input = $(this).val().toLowerCase();
        var filteredChannels = channelNames.filter(function(channel) {
            return channel.name.toLowerCase().includes(input);
        });

        var $channelList = $('#channel_list');
        $channelList.empty();
        currentIndex = -1;

        if (filteredChannels.length > 0 && input !== "") {
            filteredChannels.forEach(function(channel) {
                $channelList.append(
                    `<li>
                        <img src="${channel.avatar}" alt="Avatar" class="channel-avatar">
                        <div class="channel-info">
                            <span class="channel-name">${channel.name}</span>
                            <span class="channel-subscribers">${number_format(channel.subscribers, 0, '.', ',')} subscribers</span>
                        </div>
                    </li>`
                );
            });
            $channelList.show();
        } else {
            $channelList.hide();
        }
    });

    $(document).on('click', '#channel_list li', function() {
        $('#channel_name').val($(this).find('.channel-name').text());
        $('#channel_list').hide();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#channel_name').length) {
            $('#channel_list').hide();
        }
    });

    $(document).on('keydown', function(e) {
        var $channelList = $('#channel_list');
        var $items = $channelList.find('li');

        if ($channelList.is(':visible')) {
            if (e.key === "ArrowDown") {
                currentIndex = (currentIndex + 1) % $items.length;
                $items.removeClass('active').eq(currentIndex).addClass('active');
            } else if (e.key === "ArrowUp") {
                currentIndex = (currentIndex - 1 + $items.length) % $items.length;
                $items.removeClass('active').eq(currentIndex).addClass('active');
            } else if (e.key === "Enter") {
                e.preventDefault();
                if (currentIndex >= 0) {
                    var selectedItem = $items.eq(currentIndex).find('.channel-name').text();
                    $('#channel_name').val(selectedItem);
                    $channelList.hide();
                }
            }
        }
    });





</script>
@endsection