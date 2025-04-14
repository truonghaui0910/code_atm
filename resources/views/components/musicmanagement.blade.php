@extends('layouts.master')

@section('content')

<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Music Management ({{$datas->total()}})</h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Music Management</li>
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
                    <form id="form-search " action="/musicmanagement">
                        <div class="col-md-12 filter-class">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Channel ID/Name</label>
                                        <div class="col-12">
                                            <input id="search_channel" class="form-control" type="text" name="c1" value="{{$request->c1}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Status</label>
                                        <div class="col-12">
                                            <select id="search_status" class="form-control" name="c2">
                                                {!!$status!!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">&nbsp;</label>
                                        <div class="col-12">
                                            <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i class="fa fa-search"></i> Search</button>   
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table">
                                <thead class="thead-default">
                                    <tr align="center">
                                        <th style="width: 5%;text-align: center">
                                            <div class="checkbox checkbox-primary tbl-chk">
                                                <input id="select_all" type="checkbox" name="select_all">
                                                <label for="select_all" class="m-b-22 p-l-0 " style="margin-bottom: 1rem"></label>
                                            </div>
                                        </th>
                                        <th style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                        <!--<th style="width: 10%;">@sortablelink('channel_name',trans('label.title.channel'))</th>-->
                                        <th style="width: 20%;text-align: center">Title</th>
                                        <!--<th style="width: 5%;text-align: center">@sortablelink('number_video_success','Success')</th>-->                                         
                                        <th style="width: 15%;text-align: center">Link/Last Upload</th>
                                        <!--<th style="width: 20%;text-align: center">@sortablelink('last_execute_time','Last Upload')</th>-->

                                        <th style="width: 10%;text-align: center">@sortablelink('status',trans('label.col.status'))</th>
                                        <th  style="width: 10%;text-align: center">{{trans('label.col.function')}}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($datas as $data)
                                    <tr class="odd gradeX" align="center">
                                        <td>
                                            <div class="checkbox checkbox-primary tbl-chk">
                                                <input class="checkbox-multi" type="checkbox" name="chkIdReup[]"
                                                       id="ck-video<?php echo $data->id; ?>" value="{{$data->id}}">
                                                <label class="m-b-18 p-l-0 " for="ck-video<?php echo $data->id; ?>"></label>
                                            </div> 
                                        </td>
                                        <td><?php
                                            echo $data->id;
                                            ?>
                                        </td>
                                        <!--<td style="text-align: left;">-->
                                            <?php
//                                            $info = $data->note;
//                                            if ($info != null && $info != '') {
//                                                $open = "copyToClipboard('$info')";
//                                                echo " <span data-toggle='tooltip' data-placement='top' data-original-title='" . trans('label.tooltip.copyClipboad') . "'><i onclick=$open class='fa fa-envelope'></i> </span>";
//                                            }
//                                            if ($data->channel_id != null) {
//                                                $link = 'https://www.youtube.com/channel/' . $data->channel_id;
//                                                $open = "copyToClipboard('$link')";
//                                                echo " <span data-toggle='tooltip' data-placement='top' data-original-title='" . trans('label.tooltip.copyClipboad') . "'><i onclick=$open class='fa fa-copy'></i> </span>";
//                                            }
                                            ?>
                                        <!--</td>-->
                                        <td style="text-align: left;">
                                            <?php
                                            $title = json_decode($data->title_conf);
//                                            Log::info($data->title_conf);
                                            if ($title != null && $title != '') {
                                                echo $title->replace_all;
                                                
                                            }
                                            ?>
                                        </td>
                                        <!--<td>-->
                                            <?php
//                                            if ($data->number_video_success == null) {
//                                                echo '0';
//                                            } else {
//                                                echo $data->number_video_success;
//                                            }
                                            ?>
                                        <!--</td>-->
                                        <td style="cursor: pointer">
                                            @if($data->link_download!=null)
                                            <a style="color: blue" target="__blank" href="{{$data->link_download}}">Download</a>
                                            
                                            @else

                                            <span data-toggle='tooltip' data-placement='top' data-original-title='<?php
                                            if ($data->next_time_run != 0) {
                                                echo 'Next time: ' . gmdate('d/m/Y H:i:s', $data->next_time_run + $user_login->timezone * 3600);
                                            } else {
                                                echo 'Uploading';
                                            }
                                            ?>'>
                                                  <?php
                                                      if ($data->last_execute_time != 0) {
                                                          echo gmdate('d/m/Y H:i:s', $data->last_execute_time + $user_login->timezone * 3600);
                                                      }
                                                      ?>
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            <?php
                                            $status = $data->status;
                                            if ($status == 0) {
                                                echo "New";
                                            } else if ($status == 1) {
                                                echo "Scanned";
                                            } else if ($status == 2) {
                                                echo "Running";
                                            } else if ($status == 3) {
                                                echo "Stopped";
                                            } else if ($status == 4) {
                                                echo "Error";
                                            } else if ($status == 10) {
                                                echo "Wait";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <div style="display: inline-flex;">
                                                <a  onclick="getLog('{{$data->id}}')"  class="btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5"><i class="fa fa-info"></i> Log</a>&nbsp;
                                                <!--<a target="_blank" href="/musicconfig?id={{$data->id}}" class="btn btn-warning waves-effect waves-light btn-sm btn-ssm m-b-5"><i class="fa fa-edit"></i> Edit</a>&nbsp;-->
                                                <!--<button class="btn btn-danger waves-effect waves-light btn-sm btn-ssm m-b-5"><i class="fa fa-remove"></i> Del</button>-->
                                            </div>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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


    
    function getLog(id){
    $.ajax({
    type: "GET",
            url: "/ajaxGetLog",
            data: {'id':id},
            dataType: 'json',
            success: function (data) {
            console.log(data);
            $("#log-id").val(data.message[data.message.length - 1].detail);
            },
            error: function (data) {
            //                    console.log('Error:', data);
            }
    });
    $('#dialog').modal({backdrop: false});
    }

//    function openFilter(){
//        $('.filter-class').fadeIn('slow');
////    $('.filter-class').addClass("disp-none")
//    }
    $("#search_channel").keyup(function(){
//    alert($(this).val());
    });
//    function onDownload(id, link){
////    alert(id + link);
//    $.ajax({
//    type: "GET",
//            url: "/ajaxUpdateMusicConfig",
//            data: {'id':id, 'is_download':2},
//            dataType: 'json',
//            success: function (data) {
//            console.log(data);
//            },
//            error: function (data) {
//            console.log('Error:', data);
//            }
//    });
//    window.open(link, '_blank');
//    }





</script>
@endsection

