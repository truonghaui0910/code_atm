@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">CONFIG CHANNEL</h4>
            <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-4 col-form-label">Channel Name</label>
                        <div class="col-12">
                            <input id="channel_name" class="form-control" type="text" name="channel_name" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-4 col-form-label">Avatar</label>
                        <div class="col-12">
                            <select class="form-control input-sm type_reup" name="genre">
                                <option value="latin">Latin</option>
                                <option value="pop">Pop</option>
                                <option value="rap">Rap</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="div_bg_image">
                            <select id="background" name="background"  class="image-picker">
                                <?php
                                foreach ($list_image as $image) {
                                    echo '<option data-img-src=" http://51.75.243.130/resize.php?filename=' . $image->link . '" value="' . $image->link . '">' . $image->id . '</option>';
                                }
                                ?>
                            </select>            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" value="30" class="btn btn-t btn-sm btn-loadding" id="btnLoadMore" onclick="getBackgroundByOffset()"
                                data-loading-text="<i class='fa fa-spinner fa-spin '></i> {{ trans('label.button.loadding') }}">
                            <i class="fa fa-refresh fa-fw"></i>Load More
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-4 col-form-label">Banner</label>
                        <div class="col-12">
                            <select class="form-control input-sm type_reup" name="genre">
                                <option value="latin">Latin</option>
                                <option value="pop">Pop</option>
                                <option value="rap">Rap</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="div_bg_image">
                            <select id="background" name="background"  class="image-picker">
                                <?php
                                foreach ($list_image as $image) {
                                    echo '<option data-img-src=" http://51.75.243.130/resize.php?filename=' . $image->link . '" value="' . $image->link . '">' . $image->id . '</option>';
                                }
                                ?>
                            </select>            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" value="30" class="btn btn-t btn-sm btn-loadding" id="btnLoadMore" onclick="getBackgroundByOffset()"
                                data-loading-text="<i class='fa fa-spinner fa-spin '></i> {{ trans('label.button.loadding') }}">
                            <i class="fa fa-refresh fa-fw"></i>Load More
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-4 col-form-label">Description</label>
                        <div class="col-12">
                            <textarea class="form-control" rows="5" name="des"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(".image-picker").imagepicker();
</script>
@endsection

