<div class="modal fade" id="dialog_brand_channel" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-image fa-fw"></i></span> <span id="title-brand">Brand Channel</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="frmBrand" name="frmBrand" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>
                    <input type="hidden" id='banner' name ='banner' value=''/>
                    <input type="hidden" id='profile' name ='profile' value=''/>
                    <input type="hidden" id='idBrand' name ='idBrand' value=''/>
                    <input type="hidden" id='aboutSectionId' name ='aboutSectionId' value=''/>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Brand Type</label>
                                <div class="col-12">
                                    <select id="brand_type" name="brand_type" class="select2_multiple form-control brand_type">
                                        <option value="1">Manual</option>
                                        <option value="2">Auto</option>
                                    </select>    
                                </div>
                            </div>
                        </div>  
                        <div class="col-md-3 div_brand_type_2 disp-none">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Brand select</label>
                                <div class="col-12">
                                    <select id="brand_select" name="brand_select" class="select2_multiple form-control">
                                        {!!$brandingChannel!!}
                                    </select>    
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="firstName" name="firstName"  value="">     
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Language</label>
                                <div class="col-12">
                                    <select id="language" name="language" class="select2_multiple form-control">
                                        <option value="en">en</option>
                                    </select>  
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Categoty</label>
                                <div class="col-12">
                                    <select id="category" name="category" class="select2_multiple form-control">
                                        <option value="CREATOR_VIDEO_CATEGORY_MUSIC">CREATOR_VIDEO_CATEGORY_MUSIC</option>
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Country</label>
                                <div class="col-12">
                                    <select id="country" name="country" class="select2_multiple form-control">
                                        <option value="US">US</option>
                                        <option value="">None</option>
                                    </select>    
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">KW</label>
                                <div class="col-12">
                                    <textarea name="keyword" id="keyword" class="form-control" placeholder="split by comma"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                    <label class="col-6 col-form-label">About Section <i onclick="randomAboutSection()" data-html='true' data-toggle='tooltip' data-placement='top' data-original-title='Random About Section' class="fa fa-random "></i> <span id="about_section_use"></span></label>
                                <div class="col-12">
                                    <textarea name="about_section" id="about_section" class="form-control" placeholder=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <!--<label class="col-6 col-form-label">About Section</label>-->
                                <div class="col-12">
<!--                                    <div class="checkbox checkbox-primary">
                                        <input id="private" type="checkbox" name="private">
                                        <label for="private" class="m-b-22 p-l-0" style="margin-bottom: 1rem;margin-left: 5px"> Private old videos</label>
                                    </div>-->
                                    
                                    <div class="checkbox checkbox-primary">
                                        <input id="private" class='private' name="private" type="checkbox">
                                        <label for="private">
                                            Private old videos
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Channel Type</label>
                                <div class="col-12">
                                    <select id="channel_type" name="channel_type" class="select2_multiple form-control">
                                        {!!$channelType!!}
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Channel Genre</label>
                                <div class="col-12">
                                    <select id="channel_genre" name="channel_genre" class="select2_multiple form-control">
                                        {!!$channelGenre!!}
                                    </select>    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Channel SubGenre</label>
                                <div class="col-12">
                                    <select id="channel_subgenre" name="channel_subgenre[]" class="select2_multiple form-control" multiple style="height: 110px">
                                        {!!$channelSubGenre!!}
                                    </select>    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Channel Tags</label>
                                <div class="col-12">
                                    <select id="tags" name="tags[]" class="select2_multiple form-control" multiple style="height: 110px">
                                        {!!$channelTags!!}
                                    </select>    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Brand owner</label>
                                <div class="col-12">
                                    <select id="brand_user" name="brand_user" class="select2_multiple form-control">
                                        <option value="-1">--Your--</option>
                                        <option value="giangmusic">Giang Design</option>
                                        <option value="hiepmusic">Hiep Design</option>
                                    </select> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="col-12 col-form-label">&nbsp;</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <button type="button" class="btn btn-outline-success btn-set-info"><i class="fa fa-upload"></i> Set</button>
                                </div>

                            </div>

                        </div>
                    </div>
            
                    <div class="row">
                        <div class="col-md-4">
                            <label class="col-4 col-form-label">Avatar</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <input id="avatar_upload" type="file" name="avatar_upload" class="form-control" style="line-height: 1.25;content: Button">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-warning btn-submit-upload" value='avatar'><i class="fa fa-upload"></i> Upload</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-4 col-form-label">Banner</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <input id="banner_upload" type="file" name="banner_upload" class="form-control" style="line-height: 1.25;content: Button">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-warning btn-submit-upload" value="banner"><i class="fa fa-upload"></i> Upload</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-4 col-form-label">&nbsp;</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <button type="button" class="btn btn-outline-success btn-save-brand"><i class="fa fa-upload"></i> Submit</button>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-12 m-t-10">
                        <!--<img id="bannerView" class="banner" src="">-->
                        <!--<div class="banner-view"></div>-->
                        <div class="banner-view" style="background-image: url('/images/brand/bg.jpg');"></div>
                    </div>

                    <div class="col-sm-12">
                        <!--<img id="avatarView" class="avatar-round" src="">-->
                        <img id="avatarView" class="avatar-round" src="/images/brand/avatar.jpg">
                        <span class="channel-name-view">Example</span><br>

                    </div>


                </form>
            </div>
        </div>
    </div>
</div>