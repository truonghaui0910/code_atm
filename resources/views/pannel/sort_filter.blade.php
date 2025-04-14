<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">SORT & FILTER</h4>
            <div class="row">
                <div class="col-md-10">
                    <div class="checkbox checkbox-primary">
                        <input id="sort_video" class='sort_video'  type="checkbox" name="ck_sort_video" <?php echo ($datas[7] == 1) ? "checked" : ""; ?>>
                        <label for="sort_video">
                            Sort
                        </label>
                    </div>

                    <div class="div_sort_video m-l-15 <?php echo ($datas[7] == 1) ? "" : "disp-none"; ?>" >
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Sort by</label>
                                    <div class="col-12">
                                        <select id="sort_by" name="sort_by" class="form-control ">
                                            {!!$datas[8]!!}
<!--                                            <option value="view">View</option>
                                            <option value="like">Like</option>                        
                                            <option value="dislike">Dislike</option>                        
                                            <option value="pd">Public Date</option>                        -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Order</label>
                                    <div class="col-12">
                                        <select id="order" name="sort_order" class="form-control">
<!--                                            <option value="desc">Desc</option>
                                            <option value="asc">Asc</option>                        -->
                                            {!!$datas[9]!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-10">
                    <div class="checkbox checkbox-primary">
                        <input id="filter_video" class='filter_video' type="checkbox" name="ck_filter">
                        <label for="filter_video">
                            Filter
                        </label>
                    </div>

                    <div class="div_filter_video m-l-15 disp-none">
                        <div class="checkbox checkbox-primary m-t-5">
                            <input type="checkbox" name="ck_filter_date" id="ck_filter_date" value="1">
                            <label class="cust-checkbox" for="ck_filter_date">{{ trans('label.title.filterDate') }}</label> <br>
                        </div>
                        <div class="div_ck_filter_date m-l-15 disp-none" >
                            <div class="row margin-top-10" >
                                <div class="col-md-2">
                                    <select name="cbbFilterPdOp" class="select2_group form-control">
                                        <option value=">">{{ trans('label.value.greaterThan') }}</option>
                                        <option value="<">{{ trans('label.value.lessThan') }}</option>
                                        <option value="=">{{ trans('label.value.equal') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-1"><label class="lbl-center">{{ trans('label.title.month') }}</label></div>
                                <div class="col-md-2">
                                    <select id ="cbbFilterPdMoth" name="cbbFilterPdMoth" class="select2_group form-control">
                                        <option value="00">{{ trans('label.value.allYear') }}</option>
                                        <option value="01">01</option>
                                        <option value="02">02</option>
                                        <option value="03">03</option>
                                        <option value="04">04</option>
                                        <option value="05">05</option>
                                        <option value="06">06</option>
                                        <option value="07">07</option>
                                        <option value="08">08</option>
                                        <option value="09">09</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                </div>
                                <div class="col-md-1"><label class="lbl-center">{{ trans('label.title.year') }}</label></div>
                                <div class="col-md-2">
                                    <select id ="cbbFilterPdYear" name="cbbFilterPdYear" class="select2_group form-control">
                                        <option value="2019">2019</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="checkbox checkbox-primary m-t-5 ">
                            <input type="checkbox" name="ck_filter_time" id="ck_filter_time" value="1">
                            <label class="cust-checkbox" for="ck_filter_time">{{ trans('label.title.filterLength') }}</label> <br> 
                        </div>
                        <div class="div_ck_filter_time m-l-15 disp-none" >
                            <div class="row margin-top-10" >
                                <div class="col-md-2">
                                    <select name="cbbFilterTimeOp" class="select2_group form-control">
                                        <option value=">">{{ trans('label.value.greaterThan') }}</option>
                                        <option value="<">{{ trans('label.value.lessThan') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number"
                                           class="form-control float-left"
                                           name="txtFilterTime" id="txtFilterTime" value="300"/>
                                </div>
                                <div class="col-md-1"><label class="lbl-center">{{ trans('label.title.second') }}</label></div>                                              
                            </div>
                        </div>  
                        <div class="checkbox checkbox-primary m-t-5">
                            <input type="checkbox" name="ck_filter_view" id="ck_filter_view" value="1">
                            <label class="cust-checkbox" for="ck_filter_view">{{ trans('label.title.filterView') }}</label> <br>  
                        </div>
                        <div class="div_ck_filter_view m-l-15 disp-none" >
                            <div class="row margin-top-10" >
                                <div class="col-md-2">
                                    <select name="cbbFilterViewOp" class="select2_group form-control">
                                        <option value=">">{{ trans('label.value.greaterThan') }}</option>
                                        <option value="<">{{ trans('label.value.lessThan') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number"
                                           class="form-control float-left"
                                           name="txtFilterView" id="txtFilterView" value="1000"/>
                                </div>
                                <div class="col-md-1"><label class="lbl-center">views</label></div>                                              
                            </div>
                        </div>  
                        <div class="checkbox checkbox-primary m-t-5">
                            <input type="checkbox" name="ck_filter_like" id="ck_filter_like" value="1">
                            <label class="cust-checkbox" for="ck_filter_like">{{ trans('label.title.filterLike') }}</label> <br> 
                        </div>
                        <div class="div_ck_filter_like m-l-15 disp-none" >
                            <div class="row margin-top-10" >
                                <div class="col-md-2">
                                    <select name="cbbFilterLikeOp" class="select2_group form-control">
                                        <option value=">">{{ trans('label.value.greaterThan') }}</option>
                                        <option value="<">{{ trans('label.value.lessThan') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number"
                                           class="form-control float-left"
                                           name="txtFilterLike" id="txtFilterLike" value="100"/>
                                </div>
                                <div class="col-md-1"><label class="lbl-center">likes</label></div>                                              
                            </div>
                        </div>   
                        <div class="checkbox checkbox-primary m-t-5">
                            <input type="checkbox" name="ck_filter_dislike" id="ck_filter_dislike" value="1">
                            <label class="cust-checkbox" for="ck_filter_dislike">{{ trans('label.title.filterDislike') }}</label> <br>  
                        </div>
                        <div class="div_ck_filter_dislike m-l-15 disp-none" >
                            <div class="row margin-top-10" >
                                <div class="col-md-2">
                                    <select name="cbbFilterDislikeOp" class="select2_group form-control">
                                        <option value="<">{{ trans('label.value.lessThan') }}</option>
                                        <option value=">">{{ trans('label.value.greaterThan') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number"
                                           class="form-control float-left"
                                           name="txtFilterDislike" id="txtFilterDislike" value="100"/>
                                </div>
                                <div class="col-md-1"><label class="lbl-center">dislikes</label></div>                                              
                            </div>
                        </div>                           

                    </div>                    
                </div>

            </div>


        </div>
    </div>
</div>