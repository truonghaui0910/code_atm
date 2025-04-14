@extends('layouts.master')

@section('content')
<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Channel Management 5 ({{ $datas->total()}})</h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Autoseo.Top</a></li>
                <li class="breadcrumb-item active">Channel</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <div class="m-b-15 mdi-size-filter">
            <a href="#custom-modal" data-animation="flash" data-plugin="custommodal" 
               data-overlayspeed="100" data-overlaycolor="#36404a"><i class=" mdi mdi-filter-variant"></i> Filter</a></div>   
    </div>
    <div class="col-6">
        <div class="pull-right m-b-15 mdi-size-filter">
<!--            <a id="btn-upload" href="#dialog-import" data-animation="flash" data-plugin="custommodal" title="Import channel"
               data-overlayspeed="100" data-overlaycolor="#36404a"><i class="mdi mdi-cloud-upload"></i></a>&nbsp;&nbsp;-->
            <a id="btn-dowload" title="Export to cvs" href="/channel/download5" ><i class="mdi mdi-cloud-download"></i></a>
        </div> 
    </div>
</div>


<form id="formSearchChannel" action="/channel5" method="GET">
    {{ csrf_field() }}

    <input type="hidden" name="limit" id="limit" value="{{$request->limit}}">
    <input type="hidden" name="channel_name" id="channel_name" value="{{$request->channel_name}}">
    <input type="hidden" name="status" id="status" value="{{$request->status}}">

    <input type="hidden" name="cbb_sub" id="cbb_sub" value="{{$request->cbb_sub}}">
    <input type="hidden" name="txt_sub" id="txt_sub" value="{{$request->txt_sub}}">
    <input type="hidden" name="cbb_view" id="cbb_view" value="{{$request->cbb_view}}">
    <input type="hidden" name="txt_view" id="txt_view" value="{{$request->txt_view}}">
</form>

<div style="overflow: auto">
    <table id="demo-foo-filtering" class="table table-striped table-bordered toggle-circle m-b-0 default footable-loaded footable" data-page-size="7">
        <thead>
            <tr>
                <th style="width: 30%" data-toggle="true" class="footable-visible footable-first-column footable-sortable">Channel Name</th>
                <th style="width: 10%" class="footable-visible footable-sortable text-center">Subs</th>
                <th style="width: 10%" class="footable-visible footable-sortable text-center">Views</th>
                <th style="width: 10%" class="footable-visible footable-sortable text-center">Increase</th>
                <th style="width: 10%" class="footable-visible footable-sortable text-center">Status<span class="footable-sort-indicator"></span></th>
            </tr>
        </thead>

        <tbody>
            @foreach($datas as $data)
            <tr class="footable-even" style="">
                <td class="footable-visible footable-first-column">
                    <a target="_blank" href="https://www.youtube.com/channel/{{$data->channel_id}}">
                        <?php
                        $name = $data->channel_name;
                        if ($data->channel_name == '') {
                            $name = 'Scanning';
                            if ($data->status == 0) {
                                $name = 'Dead';
                            }
                        }
                        echo $name;
                        ?></a></td>
                <td class="footable-visible text-center">{{$data->subscribes}}</td>
                <td class="footable-visible text-center">{{$data->views}}</td>
                <td class="footable-visible text-center">{{$data->increasing}}</td>
                <td class="footable-visible text-center"><span class="badge label-table  <?php echo $data->status == 1 ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $data->status == 1 ? 'Active' : 'Inactive'; ?>        
                    </span></td>
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

@include('dialog.filterchannel')
@include('dialog.importchannel')

@endsection

@section('script')
<script type="text/javascript">
    $("#applyFilter").click(function () {
        Custombox.close();
        var channel_name = $("#txt_filter_channel_name").val();
        var cbb_filter_status = $("#cbb_filter_status").val();
        var cbb_filter_sub = $("#cbb_filter_sub").val();
        var txt_filter_sub = $("#txt_filter_sub").val();
        var cbb_filter_view = $("#cbb_filter_view").val();
        var txt_filter_view = $("#txt_filter_view").val();
        $("#channel_name").val(channel_name);
        $("#status").val(cbb_filter_status);
        $("#cbb_sub").val(cbb_filter_sub);
        $("#txt_sub").val(txt_filter_sub);
        $("#cbb_view").val(cbb_filter_view);
        $("#txt_view").val(txt_filter_view);
        console.log($("#formSearchChannel").serialize());

        $('#formSearchChannel').submit();
    });

    $("#import").click(function (e) {
        Custombox.close();
        var form = $("#form-import-channel");
        var formData = form.serialize();
        console.log(formData);
        var button = $(this);
        button.addClass('btn-disabled');

        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/channel/store",
            data: formData,
            dataType: 'json',
            success: function (data) {
                button.removeClass('btn-disabled');
                console.log(data);
                $.Notification.autoHideNotify('success', 'top right', 'Notify', data);
            },
            error: function (data) {
                button.removeClass('btn-disabled');
                console.log('Error:', data);
            }
        });
    });
</script>
@endsection

