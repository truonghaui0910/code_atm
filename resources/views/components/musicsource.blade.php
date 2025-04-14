@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Music source</h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Music source</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<form id="formMusic" class="form-horizontal form-label-left"  method="GET">
    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <h4 class="text-dark  header-title m-t-0">MUSIC SOURCE</h4>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Genre</label>
                            <div class="col-12">
                                <select class="form-control input-sm type_reup" name="genre">
                                    <option value="latin">Latin</option>
                                    <option value="pop">Pop</option>
                                    <option value="rap">Rap</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Country</label>
                            <div class="col-12">
                                <input id="country" class="form-control" type="text" name="country" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Link</label>
                            <div class="col-12">
                                <textarea class="form-control" rows="10" name="link" style="height: 400px"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <button type="button" class="btn btn-outline-info btn-sm btn-submit-music" style="margin-left: 10px;"><i class="fa fa-upload"></i> Submit</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div id="messageValidate"></div>
                </div>

            </div>
        </div>
    </div>
</form>
@endsection

@section('script')
<script type="text/javascript">
    $(".btn-submit-music").click(function (e) {
        $("#messageValidate").html('');
        var form = $("#formMusic");
        var formData = form.serialize();
        e.preventDefault();
        console.log(formData);
        $.ajax({
            type: "POST",
            url: "/musicsourcestore",
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log(data);

                var content = data.content;
                var html = '<div class="alert alert-' + data.status + '"><ul>';
                html += '<li>' + content + '</li>';
                html += '</ul>';
                $("#messageValidate").html(html);

            },
            error: function (data) {

            }
        });
    });
</script>
@endsection

