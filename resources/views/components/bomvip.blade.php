@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Boom Vip</span>
                <!--<button data-toggle="tooltip" data-placement="top" data-original-title="Add New Bom Song" type="button" class="btn btn-outline-info btn-import-bom"><i class="fa fa-plus"></i></button>-->
                <!--<button data-toggle="tooltip" data-placement="top" data-original-title="Export to csv" type="button" class=" m-l-5 btn btn-outline-info btn-export"><i class="fa fa-file-excel-o"></i></button>-->
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Boom Vip</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">FILTER</h4>
            <form id="formSearchChannel" action="/boomvip">

                <div class="row">
                    @if($is_admin_music)
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
                            <label class="col-12 col-form-label">Month</label>
                            <div class="col-12">
                                <select class="form-control" name="month">
                                    <option value="1">1 Month</option>
                                    <option value="2">2 Months</option>
                                    <option value="3">3 Months</option>
                                    <option value="4">4 Months</option>
                                    <option value="5">5 Months</option>
                                    <option value="6">6 Months</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
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
                            <label class="col-8 col-form-label">Channel ID/Name/Email</label>
                            <div class="col-12">
                                <input id="search_channel" class="form-control" type="text" name="c1" value="{{$request->c1}}">
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
            <h4 class="header-title m-t-0 m-b-30">BOOM VIP DASHBOARD</h4>
            <div class="row">
                @foreach($listUsersObjects as $u)
                <div class="col-sm-2 col-xs-12">
                    <div class="card m-b-20 text-xs-center">
                        <div class="card-header">
                            <b>{{$u->username}}</b>
                        </div>
                        <div class="card-body">
                            @if($u->view_total == 0)
                            Promos/Total:0%
                            @else
                            Channel : <span class="color-blue">{{$u->count_channel}}</span><br>
                            UnVip : <span class="color-red">{{$u->count_channel_remove}}</span><br>
                            Videos : <span class="color-blue">{{$u->count_video}}</span><br>
                            Promos : <span class="color-blue">{{number_format($u->view_promo_total, 0, '.', ',')}}</span><br>
                            Total : <span class="color-blue">{{number_format($u->view_total, 0, '.', ',')}}</span><br>
                            Percent : <span class="color-blue">{{round($u->view_promo_total / $u->view_total * 100, 2) . '%'}}</span>
                            @endif
                            <?php
//                            if ($u->view_total == 0) {
//                                echo 'Promos/Total: 0%';
//                                echo 'Promos/Total: 0%';
//                            } else {
//                                echo 'Promos/Total: '. round($u->view_promo_total / $u->view_total * 100, 2) . '%';
//                            }
                            ?> 
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
            <h4 class="header-title m-t-0 m-b-30">BOOM VIP CHANNEL ({{$datas->total()}})</h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-boom" style="max-width: 99%">
                    {{ csrf_field() }}
                    <div class="row">
                        <table class="table mobile-table-width table-drag">
                            <thead class="thead-default">
                                <tr align="center">
                                    <th style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                    <th style="width: 10%;text-align: center">@sortablelink('channel_genre','Genre')</th>
                                    <th style="width: 10%;text-align: left">@sortablelink('user_name','Username')</th>
                                    <th style="width: 15%;text-align: left">Channel Name</th>
                                    <th style="width: 10%;text-align: center">Videos Claim/Bommix/Promos</th>
                                    <th style="width: 10%;text-align: center">Views Claim/Bommix/Promos</th>
                                    <th style="width: 10%;text-align: center">Subs</th>
                                    <th style="width: 10%;text-align: center">Total Views</th>
                                    <th style="width: 10%;text-align: center">% Claim /Promos/Total</th>
                                    <th style="width: 10%;text-align: center">@sortablelink('boomvip_time','Active')</th>
                                    <th style="width: 10%;text-align: right">{{trans('label.col.function')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                @if($data->is_boomvip==1)
                                <tr class="odd gradeX" align="center">
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->channel_genre}}</td>
                                    <td style="text-align: left"><?php echo \App\Common\Utils::userCode2userName($data->user_name); ?></td>
                                    <td style="text-align: left"><a target="_blank" href="https://www.youtube.com/channel/{{$data->chanel_id}}">{{$data->chanel_name}}</a></td>
                                    <td>{{$data->count_claim_bommix}} / {{$data->count_promo_bommix}} / {{$data->count_promo_total}}</td>
                                    <td>{{number_format($data->view_claim_bommix, 0, '.', ',')}} / {{number_format($data->view_promo_bommix, 0, '.', ',')}} / {{number_format($data->view_promo_total, 0, '.', ',')}}</td>
                                    <td>{{number_format($data->subscriber_count, 0, '.', ',')}}</td>
                                    <td>{{number_format($data->view_count, 0, '.', ',')}}</td>
                                    <td>
                                        <?php
                                        if ($data->view_count == 0) {
                                            echo '0%';
                                        } else {
                                            echo round($data->view_claim_bommix / $data->view_count * 100, 1) . '% / ';
                                            echo round($data->view_promo_bommix / $data->view_count * 100, 1) . '% / ';
                                            echo round($data->view_promo_total / $data->view_count * 100, 1) . '%';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo \App\Common\Utils::calcTimeText($data->boomvip_time); ?></td>
                                    <td><a class="cur-poiter td-info turn-off-boom-vip" data-id="{{$data->id}}"><i data-toggle="tooltip" data-placement="top" data-original-title="Turn off bom vip" class="ti-trash color-red"></i></a>
                                    </td>
                                </tr>
                                @endif
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

    $('.turn-off-boom-vip').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var id = $(this).attr("data-id");
        $.ajax({
            type: "POST",
            url: "/ajaxChannel",
            data: {"chkChannelAll": [id], "action": 23, "_token": '{{csrf_token()}}'},
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                for (var i = 0; i < data.content.length; i++) {
                    $.Notification.notify(data.status, 'top center', '', data.content[i]);

                }
//                location.reload();
            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });


</script>
@endsection