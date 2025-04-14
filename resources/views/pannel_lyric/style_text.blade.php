<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">BACKGROUND TEXT STYLE</h4>
            <br>
            <div class="checkbox checkbox-primary">
                <input id="ck_style_text" class='ck_style_text'  type="checkbox" name="ck_style_text">
                <label for="ck_style_text">
                    Background text style
                </label>
            </div>
            <div class="row">
                <div class="div_ck_style_text col-md-6 disp-none">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Select style</label>
                                <div class="col-12">
                                    <select id="select_style" class="form-control input-sm" >

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Text</label>
                                <div class="col-12">
                                    <input type="text" id="text_data" name="text_data" class="form-control" placeholder="{Top music|Best music} 2019">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Font</label>
                                <div class="col-12">
                                    <select id="font_name" name="font_name" class="form-control input-sm" >
                                        @foreach($list_font_text as $data)
                                        <option value="{{$data->link}}">{{$data->name}}</option>
                                        @endforeach                     
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Text position</label>
                                <div class="col-12">
                                    <select id="pos" name="pos" class="select2_multiple form-control input-sm">
                                        <option value="LT">Left Top</option>
                                        <option value="MT">Middle Top</option>
                                        <option value="RT">Right Top</option>
                                        <option value="LM">Left Middle</option>
                                        <option value="MM">Middle Middle</option>
                                        <option value="RM">Right Middle</option>
                                        <option value="LB">Left Bottom</option>
                                        <option value="MB">Middle Bottom</option>
                                        <option value="RB">Right Bottom</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Text rotation</label>
                                <div class="col-12">
                                    <input type="number" class="form-control float-left" name="rotation" id="rotation" min="-180" max="180" value="0"/>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Position x</label>
                                <div class="col-12">
                                    <input type="number" name="pos_x" id="pos_x" class="form-control float-left" min="0" max="1920" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Position y</label>
                                <div class="col-12">
                                    <input type="number" name="pos_y" id="pos_y" class="form-control float-left"  min="0" max="1080" value="0"/>  
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Text size</label>
                                <div class="col-12">
                                    <select id="font_size" name="font_size" class="form-control input-sm" >
                                        <?php
                                        for ($i = 30; $i <= 200; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>                     
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Color</label>
                                <div class="col-12">
                                    <input type="text" name="font_color" id="font_color" class="demo1 form-control colorpicker-element" value="ffffff">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Stroke size</label>
                                <div class="col-12">
                                    <select id="text_stroke_size" name="text_stroke_size" class="form-control input-sm" >
                                        <?php
                                        for ($i = 1; $i <= 100; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>                     
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Stroke Color</label>
                                <div class="col-12">
                                    <input type="text" name="text_stroke_color" id="text_stroke_color" class="form-control colorpicker-element" value="000000">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-12">
                                    <button id="btn-preview" type="button" class="btn btn-outline-info btn-sm btn-preview" onclick="previewFunction()"><i class="fa fa-eye"></i> Preview</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 margin-r-10 ">
                    <div id="preview-image" class="disp-none">
                        <!--<div id="lyric-box"></div>-->
                        <img id="img-preview" class="" src="" style="width: 100%;" height="auto">
                    </div>
                </div>
            </div>
            <div class="div_music_type_2 ">
                <br>
                <div class="checkbox checkbox-primary">
                    <input id="ck_style_lyric" class='ck_style_lyric'  type="checkbox" name="ck_style_lyric">
                    <label for="ck_style_lyric">
                        Lyric Style 
                    </label>
                </div>
                <div class="row ">
                    <div class="div_ck_style_lyric col-md-6 disp-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Font</label>
                                    <div class="col-12">
                                        <select id="font_name_lyric" name="font_name_lyric" class="form-control input-sm" >
                                            @foreach($list_font_lyric as $data)
                                            <option value="{{$data->id}}">{{$data->name}}</option>
                                            @endforeach                     
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

<!--                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Color</label>
                                    <div class="col-12">-->
<input type="hidden" name="font_color_lyric" id="font_color_lyric" class="form-control colorpicker-element" value="ffffff">
<!--                                    </div>
                                </div>
                            </div>-->

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Number of lines</label>
                                    <div class="col-12">
                                        <input type="number" name="number_line_lyric" id="number_line_lyric" class="form-control" value="3">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <button id="btn-preview_title" type="button" class="btn btn-outline-info btn-sm" onclick="previewFunction()"><i class="fa fa-eye"></i> Preview</button>
                                    </div>
                                </div>
                            </div>
<!--                            <div class="col-md-3">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <button id="btn-preview_title" type="button" class="btn btn-outline-warning btn-sm" onclick="checkFontFunction()"><i class="fa fa-check"></i> Check Font</button>
                                    </div>
                                </div>
                            </div>-->
                        </div>



                    </div>
<!--                    <div class="form-group col-md-6 margin-r-10 ">
                        <div id="preview-image_title" class="">
                            
                            <img id="img-preview_title" class="" src="" style="width: 100%;border: 1px solid #ccc;" height="auto">
                        </div>
                    </div>-->
                </div>
            </div>
        </div>
    </div>
</div>