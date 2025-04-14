@extends('layouts.master')

@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Campaign</span>
                    <form id="frm" method="GET" action="/campaign2" class="m-r-10">
                        <select class="form-control input-sm select-status-campaign w-100px" name="status">
                            {!! $status !!}
                        </select>
                    </form>
                    <!--                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Campaign" type="button" class="btn btn-outline-info btn-import-campaign"><i class="fa fa-plus"></i></button>-->
                    <a href="https://dash.360promo.net/" target="_blank" data-toggle="tooltip" data-placement="top"
                        data-original-title="Add New Campaign From 360promo" type="button" class="btn btn-outline-info"><i
                            class="fa fa-plus"></i></a>

                    <input id="scan_artists" type="text" name="scan_artists" class="form-control m-l-5"
                        placeholder="Artists">
                    <input id="scan_songname" type="text" name="scan_songname" class="form-control m-l-5"
                        placeholder="Song Name">
                    <input id="channel" type="text" name="channel" class="form-control m-l-5" placeholder="channel">
                    <button data-toggle="tooltip" data-placement="top" data-original-title="Scan by artitst or song name"
                        type="button" class=" m-l-5 btn btn-outline-info btn-scan-artists"><i
                            class="fa fa-search"></i></button>
                    <button data-toggle="tooltip" data-placement="top" data-original-title="Stop scan" type="button"
                        class=" m-l-5 btn btn-outline-danger btn-stop-scan-artists" onclick="stopScanArtists()"><i
                            class="fa fa-remove"></i></button>
                    <button data-toggle="tooltip" data-placement="top" data-original-title="Export to csv" type="button"
                        class=" m-l-5 btn btn-outline-info btn-export-artists"><i class="fa fa-file-excel-o"></i></button>

                </h4>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                    <li class="breadcrumb-item active">Campaign</li>
                </ol>
                <div class="clearfix"></div>
            </div>
            <div class="progress progress-md m-b-20 ">
                <div class="progress-bar-scan progress-bar progress-bar-striped progress-bar-animated disp-none"
                    role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%
                </div>
            </div>
        </div>
    </div>
    @if (count($datas) > 0)
        @php
            $listCampignid = [];
        @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="chart-all">
                        <div class="row">
                            <div class="col-md-1">
                                <select class="form-control col-md-12 input-sm select-video-type" name="video_type">
                                    <option value="all">Mix-Lyric-Short</option>
                                    <option value="5">Mix</option>
                                    <option value="2">Lyric</option>
                                    <option value="6">Short</option>
                                    <option value="1">Official</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control col-md-2 input-sm select-video-type" id="rev_client"
                                    name="rev_client">
                                    <option value="-1">--All--</option>
                                    <option value="antifragile">AntiFragile</option>
                                    <option value="empire">Empire</option>
                                    <option value="french_montana">French_Montana</option>
                                    <option value="wmg">WMG</option>
                                </select>
                            </div>
                            <div class="col-md-3 pull-right text-right">
                                <!--<button id="download-report-campaign" class="btn btn-sm"><i class="fa fa-download" data-toggle="tooltip" data-placement="top" data-original-title="Download report"></i></button>-->
                                <!--                        <button class="btn btn-sm btn-range-chart" value="7">7 days</button>
                            <button class="btn btn-sm btn-range-chart" value="30">30 days</button>
                            <button class="btn btn-sm btn-range-chart" value="90">90 days</button>
                            <button class="btn btn-sm btn-range-chart" value="0">All</button>
                            <input id="range_chart" type="hidden" value="30">-->


                                <div class="input-group">

                                    <span class="input-group-addon report_usd_dialog_open" data-id="0"><i
                                            class="fa  fa-usd " data-toggle="tooltip" data-placement="top"
                                            data-original-title="$ Report"></i></span>

                                    <span class="input-group-addon scan-campain-all" data-id="0"><i
                                            class="fa fa-refresh " data-toggle="tooltip" data-placement="top"
                                            data-original-title="Rescan views"></i></span>
                                    <!--<span class="input-group-addon"><i id="download-report-listvideo-client" class="fa fa-download" data-toggle="tooltip" data-placement="top" data-original-title="Download list video revshare client"></i></span>-->
                                    <!--                            <span class="input-group-addon"><i id="download-report-client-campaign" class="fa fa-download" data-toggle="tooltip" data-placement="top" data-original-title="Download report revshare client"></i></span>-->
                                    <!--<span class="input-group-addon"><i id="download-report-campaign" class="fa fa-download" data-toggle="tooltip" data-placement="top" data-original-title="Download report"></i></span>-->
                                    <span class="input-group-addon"><i id="config-comment" class="fa fa-commenting"
                                            data-toggle="tooltip" data-placement="top"
                                            data-original-title="Config Comments"></i></span>
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control has-feedback-left" id="date_rage_picker"
                                        value="Last 30 days">
                                </div>
                                <!--<span class="fa fa-calendar form-control-feedback left" aria-hidden="true" style="color: #000"></span>-->
                                <input type="hidden" id="userName" value="-1" />
                                <input type="hidden" id="startDate" />
                                <input type="hidden" id="endDate" />


                            </div>
                            <!--                    <div id="control-chart" class="col-md-12">
                            <canvas id="chart-all"></canvas>
                        </div>-->
                        </div>
                    </div>

                    <input type="hidden" id="list-campaign-id" value="{{ json_encode($listCampignid) }}" />
                    <input type="hidden" id="next-campaign-id" />
                    <input type="hidden" id="prev-campaign-id" />
                    <input type="hidden" id="filter_id" value="{{ $request->filter_id }}" />

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <h4 class="card-title">{{ $datas->total() }} Campaigns @if ($is_admin_music)
                            - {{ $countSubmission }} Submission - {{ $count_promo }} Promos -<span class="color-green">
                                ${{ number_format($money, 0, '.', ',') }}</span>
                        @endif
                    </h4>
                    <div class="row">
                        @foreach ($genres as $genre)
                            <div class="col-sm-3 col-xs-12 ">
                                <div class="card m-b-20 text-xs-center">
                                    <div class="card-header">
                                        {{ $genre->genre }} - {{ $genre->total }} campaigns
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">VICTOR TARGET/WEEK :
                                            {{ number_format($genre->victor_target, 0, '.', ',') }} <i
                                                class="fa fa-pencil-square-o" data-container="body" data-toggle="popover"
                                                data-placement="top" data-html="true"
                                                data-content='<input type="text" id="genre_target" class="form-control input-sm"  onkeypress="return validateInputTarget(event)"><br><button type="button" class="btn btn-success  btn-sm waves-effect m-b-5 submit-genre-target" onclick="submitGenreTarget({{ $genre->genre_id }})">Submit</button>'></i>
                                        </h6>
                                        <h6 class="card-title">LA TARGET/WEEK :
                                            {{ number_format($genre->targetWeek, 0, '.', ',') }}</h6>
                                        <!--<h6 class="card-title">LA REMAINING/WEEK : {{ number_format($genre->progressWeek, 0, '.', ',') }}</h6>-->
                                        <h6 class="card-title">LA TARGET/TOTAL :
                                            {{ number_format($genre->targetTotal, 0, '.', ',') }}</h6>
                                        <!--<h6 class="card-title">LA REMAINING/TOTAL : {{ number_format($genre->progressTotal, 0, '.', ',') }}</h6>-->
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
                    <div style="overflow: auto;padding-right: 2px;">
                        <form id="form-campaign">
                            <table id="tbl-campaign" class="table mobile-table-width table-drag">
                                <thead class="thead-default">
                                    <tr align="center">
                                        <th style="width: 5%;text-align: center">
                                            <div class="checkbox checkbox-primary tbl-chk">
                                                <input id="select_all_campaign" type="checkbox"
                                                    name="select_all_campaign">
                                                <label for="select_all_campaign" class="m-b-22 p-l-0"
                                                    style="margin-bottom: 1rem"></label>
                                            </div>
                                        </th>
                                        <th style="width: 5%;text-align: center">@sortablelink('id', 'ID')</th>
                                        <th style="width: 20%;">@sortablelink('campaign_name', 'Campaign Name')</th>
                                        <th style="width: 7%;">Client</th>
                                        <!--<th style="width: 10%;text-align: center">@sortablelink('wake_position', 'Position')</th>-->
                                        <th style="width: 10%;text-align: center">@sortablelink('genre', 'Genre')</th>
                                        <th style="width: 10%;text-align: center">@sortablelink('campaign_start_date', 'Start Date')</th>
                                        <th style="width: 10%;text-align: center">@sortablelink('views_official', 'Official')</th>
                                        <th style="width: 10%;text-align: center">@sortablelink('views_compi', 'Mix')</th>
                                        <th style="width: 10%;text-align: center">@sortablelink('views_lyric', 'Lyric')</th>
                                        <th style="width: 10%;text-align: center">@sortablelink('views_short', 'Short')</th>
                                        <th style="width: 10%;text-align: center">Total</th>
                                        <th class="disp-none">Dayleft</th>
                                        <th class="disp-none">Percent</th>
                                        <th class="disp-none">Type</th>
                                        <th class="disp-none">KPI Month</th>
                                        <th class="disp-none">Label</th>
                                        <th class="disp-none">Customer</th>
                                        <th style="width: 10%;text-align: right">{{ trans('label.col.function') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $index => $data)
                                        <tr class="<?php echo $index % 2 == 0 ? 'odd' : 'even'; ?> gradeX" align="center">
                                            <td class="<?php echo $data->is_alert == 1 ? 'campaign_warning' : ''; ?>">
                                                <div class="checkbox checkbox-primary tbl-chk">
                                                    <input class="disp-none" type="checkbox" name="chk_position[]"
                                                        value="{{ $data->id }}" checked="checked">
                                                    <input class="checkbox-multi chk-view-chart" type="checkbox"
                                                        name="chk_campaign[]" id="ck-chart<?php echo $data->id; ?>"
                                                        value="{{ $data->id }}">
                                                    <label class="m-b-18 p-l-0" for="ck-chart<?php echo $data->id; ?>"></label>
                                                </div>
                                            </td>
                                            <td class="<?php echo $data->is_alert == 1 ? 'campaign_warning' : ''; ?>">{{ $data->id }}</td>
                                            <td style="text-align: left">
                                                <span class="font-18 <?php echo $data->campaign_type == 'premium' ? 'color-red' : ''; ?>">
                                                    @if ($data->type == 1)
                                                        [PROMOS]
                                                    @elseif($data->type == 4)
                                                        [REVSHARES]
                                                    @elseif($data->type == 5)
                                                        [SUBMISSION]
                                                    @endif
                                                    {{ $data->campaign_name_short }}
                                                </span><br>
                                                @if($data->some_note!=null)
                                                <span class="font-13 text-muted">{{$data->some_note}}</span><br>
                                                @endif
                                                @if ($data->crypto_view_run == 1)
                                                    <span class="badge label-table badge-danger" data-toggle="tooltip"
                                                        data-placement="top" title="" data-original-title="">Crypto
                                                        Running</span>
                                                @elseif($data->crypto_view_run == 2)
                                                    <span class="badge label-table badge-success" data-toggle="tooltip"
                                                        data-placement="top" title="" data-original-title="">Crypto
                                                        Done</span>
                                                @endif
                                                @if ($data->adsense_view_run == 1)
                                                    <span class="badge label-table badge-danger" data-toggle="tooltip"
                                                        data-placement="top" title=""
                                                        data-original-title="">Adsense Running</span>
                                                @elseif($data->adsense_view_run == 2)
                                                    <span class="badge label-table badge-success" data-toggle="tooltip"
                                                        data-placement="top" title=""
                                                        data-original-title="">Adsense Done</span>
                                                @endif
                                                <i class="ion-information-circled cur-poiter" data-toggle="tooltip"
                                                    data-placement="top" data-original-title="Show Official Information"
                                                    onclick="showTarget('{{ json_encode($data->progress_official) }}','{{ json_encode($data->progress_official_like) }}','{{ json_encode($data->progress_official_cmt) }}','{{ json_encode($data->progress_official_sub) }}')"></i>
                                                @if ($data->official_alert)
                                                    <i class="fa fa-bell-o cur-poiter color-red" data-toggle="tooltip"
                                                        data-placement="top" data-original-title="Check Official"></i>
                                                @endif
                                                @if ($data->tier == 1)
                                                    <i class="fa fa-heart cur-poiter color-red" data-toggle="tooltip"
                                                        data-placement="top" data-original-title="Tier 1"></i>
                                                @endif
                                                @if ($data->comment_setting_alert == 1)
                                                    <i onclick="editCampaign('{{ $data->id }}')"
                                                        class=" fa fa-commenting cur-poiter color-red"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="Check auto commnent setting"></i>
                                                @endif
                                                @if ($data->status == 2)
                                                    <i class="fa fa-exclamation-triangle cur-poiter color-y"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="Upcoming campaign"></i>
                                                @endif
                                                @if ($data->campaign_type == 'premium')
                                                    <i class="ion-ios7-star cur-poiter color-red" data-toggle="tooltip"
                                                        data-placement="top" data-original-title="Priority campaign"></i>
                                                @endif
                                                @if (count($data->targetView) > 0)
                                                    @foreach ($data->targetView as $targetV)
                                                        <!--                                    <div class="progress progress-custom progress-md m-b-20 ">
                                            <div class="color-cus progress-bar progress-bar-custom {{ $targetV->color }}" role="progressbar" style="width: {{ $targetV->width }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{ $targetV->name }} ({{ $targetV->start }} &rarr; {{ $targetV->end }}): {{ $targetV->views }} / {{ $targetV->target }} - <b>{{ $targetV->percent }}% {{ $targetV->dayLeftText }}</b></div>
                                        </div>-->
                                                    @endforeach
                                                @endif

                                                @if ($data->progress_mix != null)
                                                    <div class="progress progress-custom progress-lg m-b-20 ">
                                                        <div class="color-cus progress-bar progress-bar-custom progress-bar-lg {{ $data->progress_mix->color }}"
                                                            role="progressbar"
                                                            style="width: {{ $data->progress_mix->percent }}%;"
                                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            {{ $data->progress_mix->name }}
                                                            ({{ $data->progress_mix->start }} &rarr;
                                                            {{ $data->progress_mix->end }}):
                                                            {{ $data->progress_mix->views }} /
                                                            {{ $data->progress_mix->target }} -
                                                            <b>{{ $data->progress_mix->percent }}%
                                                                {{ $data->progress_mix->dayLeftText }}</b></div>
                                                    </div>
                                                @endif
                                                @if ($data->progress_official != '' && $data->type != 5)
                                                    <div class="progress progress-custom progress-lg m-b-20 ">
                                                        <div class="color-cus progress-bar progress-bar-custom progress-bar-lg {{ $data->progress_official->color }}"
                                                            role="progressbar"
                                                            style="width: {{ $data->progress_official->percent }}%;"
                                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            {{ $data->progress_official->name }}
                                                            {{ $data->progress_official->achieved }} /
                                                            {{ $data->progress_official->target }} -
                                                            <b>{{ $data->progress_official->percent }}% </b></div>
                                                    </div>
                                                @endif

                                                @if ($data->progress_money != null)
                                                    <div class="progress progress-custom progress-lg m-b-20 ">
                                                        <div class="color-cus progress-bar progress-bar-custom progress-bar-lg {{ $data->progress_money->color }}"
                                                            role="progressbar"
                                                            style="width: {{ $data->progress_money->percent }}%;"
                                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            {{ $data->progress_money->name }}
                                                            ${{ $data->progress_money->achieved }} /
                                                            ${{ $data->progress_money->target }} -
                                                            <b>{{ $data->progress_money->percent }}% </b></div>
                                                    </div>
                                                @endif
                                                @if ($data->progress_checklist != null && $data->type != 5)
                                                    <div class="progress progress-custom progress-lg m-b-20 ">
                                                        <div class="color-cus progress-bar progress-bar-custom progress-bar-lg {{ $data->progress_checklist->color }}"
                                                            role="progressbar"
                                                            style="width: {{ $data->progress_checklist->percent }}%;"
                                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            {{ $data->progress_checklist->name }}
                                                            {{ $data->progress_checklist->achieved }} /
                                                            {{ $data->progress_checklist->target }} -
                                                            <b>{{ $data->progress_checklist->percent }}% </b></div>
                                                    </div>
                                                @endif



                                            </td>
                                            <td style="text-align: left">{{ strtoupper($data->revshare_client) }}</td>
                                            <!--<td>{{ $data->wake_position }}</td>-->
                                            <td>{{ $data->genre }}</td>
                                            <td>{{ $data->campaign_start_date }}</td>
                                            <td>{{ number_format($data->views_official, 0, '.', ',') }}</td>
                                            <td>{{ number_format($data->views_compi, 0, '.', ',') }}</td>
                                            <td>{{ number_format($data->views_lyric, 0, '.', ',') }}<div
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-original-title="{{ $data->lyric1000 }}/15 lyric videos > 1000 views"
                                                    class="cur-poiter <?php echo $data->lyric1000 >= 15 ? 'color-green' : 'color-red'; ?>"><b>{{ $data->lyric1000 }}</b>
                                                </div>
                                            </td>
                                            <td>{{ number_format($data->views_short, 0, '.', ',') }}</td>
                                            <td>{{ number_format($data->views_compi + $data->views_lyric + $data->views_short, 0, '.', ',') }}
                                            </td>
                                            <td>{{ $data->curr_dayleft }}</td>
                                            <td>{{ $data->curr_percent }}</td>
                                            <td>{{ $data->type }}</td>
                                            <td>{{ $data->period }}</td>
                                            <td>{{ $data->label }}</td>
                                            <td>{{ $data->campaign_type }}</td>
                                            <td style="text-align: right;">
                                                <!--<a class="cur-poiter td-info color-h" target="_blank"  href="{{ $data->audio_url }}"><i data-toggle="tooltip" data-placement="top" data-original-title="Download  music" class="ti-download"></i></a>-->
                                                @if (in_array('20', explode(',', $user_login->role)) || in_array('1', explode(',', $user_login->role)))
                                                    @if ($data->lyric_timestamp_id == null)
                                                        @if ($data->audio_url == null)
                                                            <i class="mdi mdi-music-note-off  color-red font-22  m-l-5 cur-poiter"
                                                                data-toggle="tooltip" data-placement="top"
                                                                data-original-title="This campaign has no music yet"></i>
                                                        @else
                                                            <i onclick="getListUser('{{ $data->id }}')"
                                                                class="ti-music-alt  color-h font-22  m-l-5 cur-poiter"
                                                                data-toggle="tooltip" data-placement="top"
                                                                data-original-title="Assign make lyric"></i>
                                                        @endif
                                                    @else
                                                        <i onclick="getListChannel({{ $data->id }})"
                                                            class="btn-show-channel ti-user color-h font-22  m-l-5 cur-poiter"
                                                            data-toggle="tooltip" data-placement="top"
                                                            data-original-title="Assign manual channel"
                                                            data-cam-id="{{ $data->id }}"
                                                            data-music-id="{{ $data->lyric_timestamp_id }}"
                                                            data-songname="{{ $data->song_name }}"></i>
                                                    @endif
                                                @endif
                                                <i onclick="editCampaign('{{ $data->id }}')"
                                                    class="ti-pencil-alt color-h font-22 m-l-5 cur-poiter"
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-original-title="Edit Campaign"></i>
                                                <i class="btn-show-hub ti-video-clapper color-h font-22  m-l-5 cur-poiter"
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-original-title="Crosspost video promos"
                                                    data-music-id="{{ $data->lyric_timestamp_id }}"
                                                    data-music-type="local" data-cross-type="2"
                                                    data-songname="{{ $data->song_name }}"
                                                    data-camp-id="{{ $data->id }}"></i>

                                                <a onclick="detailCampaign('{{ $data->id }}')"
                                                    class="ti-view-list  color-h font-22  m-l-5 cur-poiter"
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-original-title="Video List"></a>
                                                <!--<i onclick="checkList('{{ $data->id }}')" class="ti-check color-h font-1rem m-l-5 cur-poiter scale-02" data-toggle="tooltip" data-placement="top" data-original-title="Tier Checklist"></i>-->
<!--                                                <i onclick="chartsCampaign({{ $data->id }})"
                                                    class="ti-stats-up color-h font-1rem m-l-5 cur-poiter scale-02"
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-original-title="Chart"></i>-->
                                                <span class="cur-poiter td-info color-h font-22  m-l-5 cur-poiter scan-campain-all"
                                                    data-id="{{ $data->id }}"><i data-toggle="tooltip"
                                                        data-placement="top" data-original-title="Scan view list video"
                                                        class="ti-reload"></i></span>
                                                <!--<i data-toggle="tooltip" data-placement="top" data-original-title="Report by channel manager" class="ti-files cur-poiter td-info color-h report-user" data-id="{{ $data->id }}"></i>-->
                                                @if ($data->artis_info_email != null)
                                                    <a href="/360promo?email={{ $data->artis_info_email }}"
                                                        target="__blank" class="cur-poiter td-info color-h font-22  m-l-5 cur-poiter"><i
                                                            data-toggle="tooltip" data-placement="top"
                                                            data-original-title="Open 360Promo manager"
                                                            class=" ti-layers"></i></a>
                                                            <a onclick="loginDash('{{ $data->artis_info_email }}')"
                                                        class="cur-poiter td-info color-h font-22  m-l-5 cur-poiter"><i
                                                            data-toggle="tooltip" data-placement="top"
                                                            data-original-title="Login to 360Promo by {{ $data->artis_info_email }}"
                                                            class="ti-new-window"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        @php
                                            $listCampignid[] = $data->id;
                                        @endphp
                                    @endforeach
                                </tbody>

                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div id="loading-list-video" class="disp-none" style="text-align: left;"><i
                            class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
                            <div class="m-t-10 m-b-10"><span id="detail-name" class="d-flex align-items-center"></span>&nbsp;<span id="detail-list-video"></span>
                    </div>
                    <div id="detail"></div>
                </div>
            </div>
        </div>
    @endif



    @include('dialog.musicchannel')
    @include('dialog.importcampaign')
    @include('dialog.checklist')
    @include('dialog.assignmakelyric')
    @include('dialog.assignchannel')
    @include('dialog.multiupload')
    @include('dialog.report_campaign_rev')
    @include('dialog.import_comments')
    
    <div class="modal fade" id="allocationModal" tabindex="-1" aria-labelledby="allocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allocationModalLabel">Submission percent</h5>
                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                            data-id="filterModal" data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal"
                            style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001;">
                            
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                fill="currentColor" class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </button>
                </div>
                <div class="modal-body">
                    <form id="allocationForm">
                        <div id="allocationRows">
                            <!-- Rows will be added here dynamically -->
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <button type="button" id="addRow" class="btn btn-primary">Add Row</button>
                            </div>
                            <div class="col-md-6 text-right">
                                Total: <span id="totalPercent">0</span>
                                <div><span class="font-12 text-mute">Số tiền sẽ được chia</span></div>
                            </div>
                        </div>
                        <input type="hidden" id="dataChannels" value="{{$dataChannels}}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @if($is_admin_music)
                    <button type="button" class="btn btn-success" id="submitForm">Submit</button>
                    @endif
                </div>
            </div>
        </div>
    </div>    
@endsection

@section('script')
    <script type="text/javascript">

    //đếm ký tự
    function setupCharacterCounter(textboxSelector, charCountSelector, initialValue = '') {
        var $textbox = $(textboxSelector);
        var $charCount = $(charCountSelector);

        // Function to update the character count
        function updateCharCount() {
            var charCount = $textbox.val().length;
            $charCount.text(charCount);
        }

        // Set initial value if provided (e.g., from database)
        if (initialValue !== '') {
            $textbox.val(initialValue);
            updateCharCount(); // Update the count after filling
        }

        // Realtime character counting
        $textbox.on('input', function() {
            updateCharCount();
        });

        // Initial character count for pre-filled data
        updateCharCount();
    }

    initSummernote("#summernote_press_release",true,"")
    
    //<editor-fold defaultstate="collapsed" desc="Submission config">
    $("#btn-configs-sub").click(function(){
        $("#allocationModal").modal("show");
    });
        var totalMoney = 0;
        var dataChannels  = $("#dataChannels").val();
        var savedAllocations = [];
            listChannels =JSON.parse(dataChannels);
            const channels = [...new Set(listChannels.map(item => item.channel_name))];

            // Tạo map của channel đến user
            const channelToUser = listChannels.reduce((map, item) => {
                map[item.channel_name] = item.user;
                return map;
            }, {});

            function createRow(channel = '', user = '', money = 0) {
                return `
                    <div class="row allocation-row m-b-5">
                        <div class="col-md-4">
                            <select class="form-control form-select channel select_pick" data-show-subtext="true" data-live-search="true" data-container="body">
                                <option value="">Select Channel</option>
                                ${channels.map(ch => `<option data-subtext="${channelToUser[ch]}" value="${ch}" ${ch === channel ? 'selected' : ''}>${ch}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control user" value="${user}" readonly>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="number" class="form-control percent" value="${money}" min="0"  placeholder="Money">
                                <span class="input-group-addon">$</span>   
                            </div> 
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                        </div>
                    </div>
                `;
            }

            function updateTotal() {
                $('#totalPercent').removeClass("color-red");
                let total = 0;
                $('.percent').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#totalPercent').text('$'+total.toFixed()+'/$'+totalMoney);
                if(total>=totalMoney){
                     $('#totalPercent').addClass("color-red");
                }
                return total;
            }

            function loadSavedAllocations() {
                $('#allocationRows').empty();
                if (savedAllocations.length > 0) {
                    savedAllocations.forEach(allocation => {
                        $('#allocationRows').append(createRow(allocation.channel_name, allocation.user, allocation.money));
                    });
                } else {
                    $('#allocationRows').append(createRow());
                }
                updateTotal();
                $('.select_pick').selectpicker('render');
            }

            // Load saved allocations when modal is shown
            $('#allocationModal').on('show.bs.modal', loadSavedAllocations);

            // Add row button
            $('#addRow').click(function(e) {
                e.preventDefault();
                $('#allocationRows').append(createRow());
                updateTotal();
                $('.select_pick').selectpicker('render');
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.allocation-row').remove();
                updateTotal();
            });

            // Update total on percent change
            $(document).on('input', '.percent', function() {
                updateTotal();
            });
            // Auto-fill user when channel is selected
            $(document).on('change', '.channel', function() {
                const selectedChannel = $(this).val();
                if(selectedChannel!=""){
                    const userInput = $(this).closest('.allocation-row').find('.user');
                    userInput.val(channelToUser[selectedChannel] || '');
                }
            });
            
            // Form submission
            $('#submitForm').click(function() {
                const total = updateTotal();
                if (total > totalMoney) {
                    $.Notification.autoHideNotify('error', 'top center', "Notify", `The total amount cannot exceed $${totalMoney}`);
                    return;
                }

                const allocations = [];
                $('.allocation-row').each(function() {
                    const row = $(this);
                    allocations.push({
                        user: row.find('.user').val(),
                        channel_name: row.find('.channel').val(),
                        money: parseFloat(row.find('.percent').val())
                    });
                });

                // Hiển thị loading indicator
                $('#submitForm').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
                $.ajax({
                    type: "POST",
                    url: "/campaign2/submissionPercent",
                    data: {
                        cam_id: $("#cam_id").val(),
                        data: allocations,
                        _token:  '{{ csrf_token() }}'

                    },
                    dataType: 'json',
                    success: function(data) {
                        logger('submissionPercentr', data);
                        $('#submitForm').prop('disabled', false).text('Submit');
                        $.Notification.autoHideNotify(data.status, 'top center', "Notify", data.message);

                    },
                    error: function(data) {
                        logger('Error:', data);
                    }
            

                });  
            });  
    
    //</editor-fold>

        
        function loginDash(email){
            $.ajax({
                type: "GET",
                url: "/360promo/getInfoUser",
                data: {
                    "email": email
                },
                dataType: 'json',
                success: function(data) {
                    logger('getInfo', data);
                    $("#password").val(data.pass_text);
                    let newWindow = open(`https://dash.360promo.net/login?u=${data.email}&p=${data.pass_text}`, 'example2', 'width=1920,height=1080')
                    newWindow.focus();
                },
                error: function(data) {
                    logger('Error:', data);
                }
            });
        }
        
        $(function() {
            var filterId = $("#filter_id").val();
            if (filterId != "") {
//                $('[type="search"]').val(filterId).change();
                editCampaign(filterId);
            }
        });

        //2023/08/28 ctlr + f
        window.onkeydown = function(e) {
            if (e.keyCode == 70 && e.ctrlKey) {
                e.preventDefault();
                $('input[type="search"]').focus();
            }
        }

        //2023/06/13 cấu hình multy comment
        $("#config-comment").click(function() {
            $.ajax({
                type: "GET",
                url: "/getComments",
                data: {

                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    var html = '';
                    html += `<table id="tbl-autocomment-result" class="table text-center" style="border-collapse: inherit;table-layout:fixed"><thead><tr>
                        <th class='text-center' style="width:2%">#</th>
                        <th class='text-center' style="width:5%">Campaign</th>
                        <th class='text-center' style="width:10%">Video</th>
                        <th class='text-center' style="width:25%">Gmail</th>
                        <th class='text-center' style="width:5%">Job_id</th>
                        <th class='text-center' style="width:5%">Status</th>
                        <th class='text-center' style="width:10%">Time</th>
                        <th class='text-center style="width:20%"'>Comment</th></tr></thead>`;
                    var i = 0;
                    $.each(data, function(key, value) {
                        i = i + 1;
                        html += `<tr><td scope="row">${i}</td>
                            <td class='text-center'>${value.campaign_id}</td>
                            <td class='text-center'>${value.video_id}</td>
                            <td class='text-center'>${value.gmail}</td>
                            <td class='text-center'>${value.job_id}</td>
                            <td class='text-center'>${value.status_text}</td>
                            <td class='text-center'>${value.last_run}</td>
                            <td class="text-ellipsis">${value.content}</td>
                    </tr>`;
                    });
                    html += '</table>';
                    $("#import_comments_result").html(html);
                    $('#tbl-autocomment-result').DataTable({
                        searching: true,
                        ordering: false,
                        processing: false,
//                        select: true,
                        responsive: false,
                        paging: true,
                        lengthMenu: [
                            [10, 20, 50, 100, 500, 1000],
                            [10, 20, 50, 100, 500, 1000]
                        ]
                    });
                },
                error: function(data) {
                    console.log(data);
                }
            });
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_import_comments').modal({
                backdrop: false
            });
        });

        $(".btn-import-comment").click(function() {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/importCommnents",
                data: $("#form-import-comments").serialize(),
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        $(".report_usd_dialog_open").click(function(e) {
            e.preventDefault();
            getReportRev($("#period_rev").val());

        });

        function getReportRev(period, show = 1) {
            $("#report_campaign_rev_content").html("");
            $("#report_user_rev_content").html("");
            $("#report_user_rev_content_detail").html("");
            $("#report_campaign_rev_loading").show();
            $("#dialog_report_campaign_rev_title").text("Report Revenue");
            $(".modal-dialog").addClass("modal-80");
            if (show == 1) {
                $('#dialog_report_campaign_rev').modal({
                    backdrop: false
                });
            }
            $.ajax({
                type: "GET",
                url: "/reportCampaignRevenue",
                data: {
                    'period': period
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    var html = '<div class="row">';
                    html += '<div class="col-md-12">';
                    @if ($is_admin_music)
                        html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Period</th>
                            <th class='text-center'>Campaign</th>
                            <th class='text-center'>Amount</th>
                            <th class='text-center'>Customer Paid</th>
                            <th class='text-center'>Expense</th>
                            <th class='text-center'>KPI</th>
                            <th class='text-center'>Debt</th>
                            <th class='text-center'>Current Profit</th>
                            <th class='text-center'>Last Profit</th>
                            <th class='text-center'>Status Pay</th>
                            <th class='text-center'>Detail</th>`;
                    @else
                        html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Period</th>
                            <th class='text-center'>Campaign</th>
                            <th class='text-center'>KPI</th>
                            <th class='text-center'>Status Pay</th>
                            <th class='text-center'>Detail</th>`;
                    @endif
                    var i = 0;
                    $.each(data.report, function(key, value) {
                        i = i + 1;
                        var checked = "";
                        if (value.is_paid == 1) {
                            checked = "checked";
                        }
                        @if ($is_admin_music)
                            html +=
                                `<tr><td scope="row">${i}</td>
                            <td><b>${value.period_text}</b></td>
                            <td>${value.count}</td>
                            <td>$${ number_format(value.amount, 0, '.', ',')}</td>
                            <td>$${number_format(value.paid, 0, '.', ',')}</td>
                            <td>$${number_format(value.spent, 0, '.', ',')}</td>
                            <td>$${number_format(value.payment, 0, '.', ',')}</td>
                            <td>$${number_format(value.debt, 0, '.', ',')}</td>
                            <td>$${number_format(value.curr_profit, 0, '.', ',')}</td>
                            <td>$${number_format(value.last_profit, 0, '.', ',')}</td>
                            <td>
                                <div class="checkbox checkbox-primary tbl-chk">
                                    <input id="chk_pay_${i}" class="chk_status_pay" type="checkbox" onclick="changeStatusPay('${value.period}')" ${checked}>
                                    <label for="chk_pay_${i}" class="m-b-18 p-l-0"></label>
				</div>
                            </td>   
                            <td><button id="btn${value.period}" type="button" onclick='getReportPeriodRevDetail("${value.period}")' class="btn btn-outline-info btn-sm"> Detail</button></td></tr>`;
                        @else
                            if (value.is_show == 1) {

                                html +=
                                    `<tr><td scope="row">${i}</td>
                            <td><b>${value.period_text}</b></td>
                            <td>${value.count}</td>
                            <td>$${number_format(value.payment, 0, '.', ',')}</td>
                            <td>
                                <div class="checkbox checkbox-primary tbl-chk">
                                    <input id="chk_pay_${i}" class="chk_status_pay" type="checkbox" onclick="changeStatusPay('${value.period}')" ${checked} @if (!$is_admin_music) disabled @endif>
                                    <label for="chk_pay_${i}" class="m-b-18 p-l-0"></label>
				</div>
                            </td>  
                            <td><button id="btn${value.period}" type="button" onclick='getReportPeriodRevDetail("${value.period}")' class="btn btn-outline-info btn-sm"> Detail</button></td></tr>`;
                            }
                        @endif
                    });

                    html += '</table></div></div>';

                    $("#report_campaign_rev_content").html(html);
                    $("#report_campaign_rev_loading").hide();


                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function getReportPeriodRevDetail(period) {
            $("#report_user_rev_content").html("");
            $("#report_user_rev_content_detail").html("");
            var $this = $("#btn" + period);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/getReportPeriodRevDetail",
                data: {
                    'period': period
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    console.log(data);
                    var html = '';
                    html += `<span class="m-l-15">Detail of <b>${period}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                        <th class='text-center'>#</th>
                        <th class='text-left'>User</th>
                        <th class='text-center'>Role</th>
                        <th class='text-center'>KPI</th>
                        <th class='text-center'>Detail</th>`;
                    var i = 0;
                    $.each(data.users, function(key, value) {
                        i = i + 1;
                        html += `<tr><td scope="row">${i}</td>
                            <td class='text-left'>${value.user_name}</td>
                            <td>${value.role}</td>
                            <td>$${value.kpi}</td>
                            <td><button id="btn${value.user_name}" type="button" onclick='getReportUserRevDetail("${value.period}","${value.user_name}")' class="btn btn-outline-info btn-sm"> Detail</button></td>
                    </tr>`;
                    });
                    html += '</table>';
                    $("#report_user_rev_content").html(html);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function getReportUserRevDetail(period, username) {
            $("#report_user_rev_content_detail").html("");
            var $this = $("#btn" + username);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/getReportUserRevDetail",
                data: {
                    'period': period,
                    'username': username
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    var html = '';
                    html += `<span class="m-l-15">Detail of <b>${username}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-left'>User</th>
                            <th class='text-center'>Campaign</th>
                            <th class='text-center'>Type</th>
                            <th class='text-center'>Genre</th>
                            <th class='text-center'>Views</th>
                            <th class='text-center'>Views Mix</th>
                            <th class='text-center'>Views Lyric</th>
                            <th class='text-center'>Views Short</th>
                            <th class='text-center'>KPI</th>`;
                    var i = 0;
                    $.each(data.users, function(key, value) {
                        i = i + 1;
                        html += `<tr>
                                <td scope="row">${i}</td>
                                <td class='text-left'>${value.username}</td>
                                <td>${value.campaign_id}</td>
                                <td>${value.type}</td>
                                <td>${value.genre}</td>
                                <td>${number_format(value.views_money, 0, '.', ',')}/${ number_format(value.views_money_total, 0, '.', ',')}</td>
                                <td>${number_format(value.views_mix, 0, '.', ',')}</td>
                                <td>${number_format(value.views_lyric, 0, '.', ',')}</td>
                                <td>${number_format(value.views_short, 0, '.', ',')}</td>
                                <td><b>$${value.money}</b></td></tr>`;
                    });
                    html += '</table>';
                    html +=
                        `<table class="table"><tr><td><strong>PROMO: $${data.camTypeMoney.PROMOS.toFixed(2)}</strong></td><td class="text-right"><strong>SUBMISSION: $${data.camTypeMoney.SUBMISSION.toFixed(2)}</strong></td></tr><table>`;
                    $("#report_user_rev_content_detail").html(html);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function changeStatusPay(period) {
            $.ajax({
                type: "GET",
                url: "/changeStatusPay",
                data: {
                    'period': period,
                    'rev_type': 1
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'error') {
                        $.Notification.notify('error', 'top center', 'Notification', data.message);
                    }

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        $("#official_video").change(function() {
            $(".official-sync").show();
            var link = $(this).val();
            $.ajax({
                type: "GET",
                url: "/videoinfo",
                data: {
                    'link': link
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $(".official-sync").hide();
                    $("#start_view_official").val(data.view);
                    $("#start_like_official").val(data.like);
                    $("#start_cmt_official").val(data.comment);
                    $("#start_sub_official").val(data.channel_sub);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        $('#tbl-campaign').DataTable({
            oSearch: {"sSearch": $("#filter_id").val()},
            searching: true,
            ordering: false,
            processing: false,
            select: true,
            responsive: false,
            paging: false,
            lengthMenu: [
                [500],
                [500]
            ],
            searchPanes: {
                //            viewTotal: true,
                viewCount: true,
                //            controls: false,
                collapse: true,
                //            initCollapsed: true,
                columns: [14, 4, 11, 12, 13, 15, 16]
            },
            dom: 'Plfrtip',
            columnDefs: [{
                    "targets": [14, 15, 16],
                    "visible": false,
                    "searchable": true
                },
                {
                    "targets": [3, 11, 12, 13],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [6, 7, 8, 9],
                    "visible": true,
                    "searchable": false
                },
                {
                    searchPanes: {
                        show: true,
                        header: 'KPI Month'
                    },
                    targets: [14]
                },
                {
                    searchPanes: {
                        header: 'Genre'
                    },
                    targets: [4]
                },
                {
                    searchPanes: {
                        header: 'Day Left',
                        options: [{
                                label: 'Time up',
                                value: function(rowData, rowIdx) {
                                    return rowData[11] <= 0;
                                }
                            },
                            {
                                label: 'Under 1 day',
                                value: function(rowData, rowIdx) {
                                    return rowData[11] < 1 && rowData[11] > 0;
                                }
                            },
                            {
                                label: '1 day - 2 days',
                                value: function(rowData, rowIdx) {
                                    return rowData[11] < 2 && rowData[11] >= 1;
                                }
                            },
                            {
                                label: '2 day - 7 days',
                                value: function(rowData, rowIdx) {
                                    return rowData[11] < 7 && rowData[11] >= 2;
                                }
                            },
                            {
                                label: 'Over 7 days',
                                value: function(rowData, rowIdx) {
                                    return rowData[11] >= 7;
                                }
                            }
                        ]
                    },
                    targets: [11]
                },
                {
                    searchPanes: {
                        show: true,
                        header: 'Views',
                        options: [{
                                label: '0% to 50%',
                                value: function(rowData, rowIdx) {
                                    return rowData[12] < 50;
                                }
                            },
                            {
                                label: '50% to 70%',
                                value: function(rowData, rowIdx) {
                                    return rowData[12] < 70 && rowData[12] >= 50;
                                }
                            },
                            {
                                label: '70% to 95%',
                                value: function(rowData, rowIdx) {
                                    return rowData[12] < 95 && rowData[12] >= 70;
                                }
                            },
                            {
                                label: '95% to 120%',
                                value: function(rowData, rowIdx) {
                                    return rowData[12] < 120 && rowData[21] >= 95;
                                }
                            },
                            {
                                label: 'Over 120%',
                                value: function(rowData, rowIdx) {
                                    return rowData[12] >= 120;
                                }
                            }
                        ]
                    },
                    targets: [12]
                },
                {
                    searchPanes: {
                        header: 'Type',
                        options: [{
                                label: 'PROMOS',
                                value: function(rowData, rowIdx) {
                                    return rowData[13] == 1;
                                }
                            },
                            //                        {
                            //                            label: 'REVSHARES',
                            //                            value: function(rowData, rowIdx) {
                            //                                return rowData[12] ==4;
                            //                            }
                            //                        },
                            {
                                label: 'SUBMISSION',
                                value: function(rowData, rowIdx) {
                                    return rowData[13] == 5;
                                }
                            }
                        ]
                    },
                    targets: [13]
                },
                {
                    searchPanes: {
                        show: true,
                        header: 'Label'
                    },
                    targets: [15]
                },
                {
                    searchPanes: {
                        show: true,
                        header: 'Customer'
                    },
                    targets: [16]
                },
            ]

        });
        $("div.toolbar").html('<b>Custom tool bar! Text/images etc.</b>');

        function submitGenreTarget(id) {
            var genre_target = $("#genre_target").val();
            $.ajax({
                type: "GET",
                url: "/submitGenreTarget",
                data: {
                    'id': id,
                    'genre_target': genre_target
                },
                dataType: 'json',
                success: function(data) {
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                    if (data.status == "success") {
                        location.reload();
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        var options = {};
        options.autoApply = true;
        options.autoUpdateInput = false;
        options.startDate = moment().subtract(29, 'days');
        options.endDate = moment();
        $("#startDate").val(options.startDate.format('YYYYMMDD'));
        $("#endDate").val(options.endDate.format('YYYYMMDD'));
        options.ranges = {
            //            'Today': [moment(), moment()],
            //            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                'month')],
            'Life Time': ['20210601', moment()]
        };
        $('#date_rage_picker').daterangepicker(options, function(start, end, label) {
            if (label === "Custom Range") {
                $("#date_rage_picker").val(start.format('YYYY/MM/DD') + ' to ' + end.format('YYYY/MM/DD'));
            } else {
                $("#date_rage_picker").val(label);
            }
            $("#startDate").val(start.format('YYYYMMDD'));
            $("#endDate").val(end.format('YYYYMMDD'));
            drawChart();
        });

        $("#target_total").change(function() {
            var duration = $("#campaign_duration").val();
            $("#m" + duration).val($(this).val()).change();
        });

        function showTarget(view, like, cmt, sub) {
            var view = JSON.parse(view);
            var like = JSON.parse(like);
            var cmt = JSON.parse(cmt);
            var sub = JSON.parse(sub);
            var html = '';
            if (view != '') {
                html += '<div class="progress progress-custom progress-big m-b-20 text-ellipsis">';
                html +=
                    `<div class="color-cus progress-bar progress-bar-custom progress-bar-big ${view.color}" role="progressbar" style="width:${view.percent}%;">${view.name}  ${view.achieved} / ${view.target} <b>${view.percent}%</b></div>`;
                html += '</div>';

            }
            if (like != '') {
                html += '<div class="progress progress-custom progress-big m-b-20 text-ellipsis">';
                html +=
                    `<div class="color-cus progress-bar progress-bar-custom progress-bar-big ${like.color}" role="progressbar" style="width:${like.percent}%;">${like.name}  ${like.achieved} / ${like.target} <b>${like.percent}%</b></div>`;
                html += '</div>';
            }
            if (cmt != '') {
                html += '<div class="progress progress-custom progress-big m-b-20 text-ellipsis">';
                html +=
                    `<div class="color-cus progress-bar progress-bar-custom progress-bar-big ${cmt.color}" role="progressbar" style="width:${cmt.percent}%;">${cmt.name}  ${cmt.achieved} / ${cmt.target} <b>${cmt.percent}%</b></div>`;
                html += '</div>';
            }
            if (sub != '') {
                html += '<div class="progress progress-custom progress-big m-b-20 text-ellipsis">';
                html +=
                    `<div class="color-cus progress-bar progress-bar-custom progress-bar-big ${sub.color}" role="progressbar" style="width:${sub.percent}%;">${sub.name}  ${sub.achieved} / ${sub.target} <b>${sub.percent}%</b></div>`;
                html += '</div>';
            }

            $("#content-dialog").html(html);
            $("#dialog-list-tile").text("Official Progress");
            $('#dialog_music_channel').modal({
                backdrop: false
            });
        }
        //    changeTarget();

        //thay đổi giá trị target thì tính remaining
        function changeTarget() {
            $(".imput-target").change(function() {
                var target = '';
                for (var i = 1; i <= 4; i++) {
                    if (i < 4) {
                        target += $("#w" + i).val() + ",";
                    } else {
                        if (camDuration > 1) {
                            target += $("#w" + i).val() + ",";
                        } else {
                            target += $("#w" + i).val();
                        }
                    }
                }
                if (camDuration > 1) {
                    target += $("#m").val();
                }
                var arrTarget = target.split(",");
                var total = shortNumber2Number($("#target_total").val());
                var totalUse = 0;
                var arrTargetValue = [];
                for (var i = 0; i < arrTarget.length; i++) {
                    if (arrTarget[i] != '') {
                        //                    totalUse += shortNumber2Number(arrTarget[i]);
                        arrTargetValue.push(shortNumber2Number(arrTarget[i]));
                    }
                }
                let max_val = arrTargetValue.reduce(function(accumulator, element) {
                    return (accumulator > element) ? accumulator : element
                });
                //            $("#remaining").html('('+number2shortNumber(total - max_val) +' remaining)');
                $("#target").val(target);
            });
        }
        var camDuration = 1;

        //    $("#campaign_duration").change(function() {
        //        $("#target").val("");
        //        $('.target-m').remove();
        //        camDuration = $(this).val();
        //        if (camDuration > 1) {
        //            var div = '<div class="col-md-2 target-m">';
        //            div += '<div class="form-group row">';
        //            div += '            <label class="col-12 col-form-label">Reach to</label>';
        //            div += '            <div class="col-12">';
        //            div += '                <div class="input-group">';
        //            div += '                    <span class="input-group-addon input-group-addon-custom">Month' + camDuration + '</span>';
        //            div += '                    <input type="text" id="m" name="m" class="form-control imput-target" onkeypress="return validateInputTarget(event)">';
        //            div += '                </div>';
        //            div += '            </div>';
        //            div += '        </div>';
        //            div += '    </div>';
        //            $(div).insertAfter(".target-w4");
        //            changeTarget();
        //
        //        }
        //    });
        $("#download-report-campaign").click(function(e) {
            e.preventDefault();
            window.location.href = "/downloadReportCampaign?start=" + $("#startDate").val() + "&end=" + $(
                "#endDate").val();

        });
        $("#download-report-client-campaign").click(function(e) {
            e.preventDefault();
            window.location.href = "/downloadReportClientCampaign?start=" + $("#startDate").val() + "&end=" + $(
                "#endDate").val();

        });
        $("#download-report-listvideo-client").click(function(e) {
            e.preventDefault();
            var rev_client = $("#rev_client").val();
            if (rev_client == -1) {
                $.Notification.autoHideNotify('error', 'top center', 'Notify', 'Please choose Revshare client');
                return;
            }
            window.location.href = "/downloadListvideosRevshareClient?rev_client=" + rev_client + "&start=" + $(
                "#startDate").val() + "&end=" + $("#endDate").val();

        });
        $(".report-user").click(function(e) {
            e.preventDefault();
            $("#content-dialog").html("");
            $("#dialog-loading").show();
            $.ajax({
                type: "GET",
                url: "/reportCampaignUser",
                data: {
                    'id': $(this).attr("data-id")
                },
                dataType: 'json',
                success: function(data) {
                    //                console.log(data);
                    var html = '';
                    var html = '<div class="row">';
                    html += '<div class="col-md-12">';
                    html +=
                        '<table class="table" style="border-collapse: inherit"><thead><tr><th>#</th><th>User</th><th>Type</th><th>Videos</th><th>Views</th>';
                    var i = 0;
                    $.each(data.report, function(key, value) {
                        i = i + 1;
                        html += '<tr><td scope="row">' + i + '</td><td>' + value.username +
                            '</td><td>' + value.video_type_text + '</td><td>' + value.videos +
                            '</td><td>' + number_format(value.views, 0, ',', '.') +
                            '</td></tr>';
                    });
                    html += '</table></div>';

                    html += '</table></div></div>';

                    $("#content-dialog").html(html);
                    $("#dialog-loading").hide();

                    $("#dialog-list-tile").text(data.name);
                },
                error: function(data) {
                    console.log(data);
                }
            });
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_music_channel').modal({
                backdrop: false
            });
        });

        function videoSync() {
            $(".video-sync").click(function(e) {
                e.preventDefault();
                var $this = $(this);
                var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
                if ($(this).html() !== loadingText) {
                    $this.data('original-text', $(this).html());
                    $this.html(loadingText);
                }
                var id = $(this).attr("data-id");
                $.ajax({
                    type: "GET",
                    url: "/reSynVideo",
                    data: "id=" + id,
                    dataType: 'json',
                    success: function(data) {
                        $this.html($this.data('original-text'));
                        $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data
                        .message);
                    },
                    error: function(data) {
                        $this.html($this.data('original-text'));
                        //                but.button('reset');
                    }
                });
            });
        }
        $(".scan-campain-all").click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var id = $(this).attr("data-id");
            $.ajax({
                type: "GET",
                url: "/scanViewPromoCampaign",
                data: "id=" + id,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                    //                but.button('reset');
                }
            });
        });

        var progress;
        progress = setInterval(function() {
            checkProgress();
        }, 1000);

        function checkProgress() {
            $.ajax({
                type: "GET",
                url: "/getProgressScan",
                data: {},
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.percent == 100 || data.percent == 0) {
                        clearInterval(progress);
                        $(".progress-bar-scan").html(data.thread + " threads - " + data.percent + "%");
                        $(".progress-bar-scan").css({
                            "width": data.percent + "%"
                        });
                        if (data.percent == 100) {
                            stopScanArtists();
                            notify("Just finished scanning the artist, please check",
                                "https://automusic.win/campaign2", "");
                        }
                    } else if (data.percent > 0) {
                        $(".progress-bar-scan").show();
                        $(".progress-bar-scan").html(data.thread + " threads - " + data.percent + "%");
                        $(".progress-bar-scan").css({
                            "width": data.percent + "%"
                        });

                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function stopScanArtists() {
            var $this = $(".btn-stop-scan-artists");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/deleteProgressScan",
                data: {},
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        }

        $(".btn-export-artists").click(function() {

            var artists = $("#scan_artists").val();
            var songname = $("#scan_songname").val();
            if (artists == "" && songname == "") {
                $.Notification.notify("error", 'top center', '', "Please enter Artists or Song Name");
                return;
            }
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var url = "/exportScanArtist?scan_artists=" + encodeURIComponent(artists) + "&scan_songname=" +
                encodeURIComponent(songname);
            window.location.href = url;
            $this.html($this.data('original-text'));


        });

        $(".btn-scan-artists").click(function() {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }

            var channel = $("#channel").val();
            var artists = $("#scan_artists").val();
            var songname = $("#scan_songname").val();
            var form = new FormData();
            form.append("artists", artists);
            form.append("song_name", songname);
            form.append("channel", channel);
            form.append('_token', '{{ csrf_token() }}');
            $.ajax({
                type: "POST",
                url: "/makeCommandScanVideo",
                data: form,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    if (data.status == "success") {
                        progress = setInterval(function() {
                            checkProgress();
                        }, 1000);
                        $.ajax({
                            url: "https://automusic.win/callScanClaimByCondition",
                            data: {}
                        });

                    }
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });

        //    $(".table-drag").tableDnD();
        //    $(".table-drag").tableDnD({
        //        onDragClass: "myDragClass",
        //        onDragStart: function(table, row) {
        //
        //        },
        //        onDragStop: function(table, row) {
        //            var formData = $("#form-campaign").serialize();
        //            $.ajax({
        //                type: "GET",
        //                url: "updatePositionWakeCampaign",
        //                data: formData,
        //                dataType: 'json',
        //                success: function(data) {
        //                    //                 console.log(data);
        //                },
        //                error: function(data) {
        //                    console.log(data);
        //                }
        //            });
        //        }
        //    });
        var listChartId = [];
        var listChartTotal = [];
        var charts;
        <?php
        foreach ($datas as $index => $data) {
            echo "listChartTotal.push($data->id);";
            if ($index < 5) {
                echo "listChartId.push($data->id);";
                echo " $('#ck-chart$data->id').prop('checked', true);";
            }
        }
        ?>

        //    drawChart();
        $(".btn-range-chart").click(function(e) {
            e.preventDefault();
            $("#range_chart").val($(this).val());
            drawChart();
        })

        $(".select-video-type").change(function() {
            drawChart();
        });

        $('#select_all_campaign').change(function() {
            var check = $(this).is(':checked');
            var checkboxes = $(this).closest('.table').find('.checkbox-multi');
            checkboxes.prop('checked', check);
            if (!check) {
                listChartId = [];
            } else {
                listChartId = listChartTotal;
            }

            drawChart();
        });

        $(".chk-view-chart").change(function() {

            var id = $(this).val();
            id = parseInt(id);
            if (this.checked) {
                listChartId.push(id);
            } else {
                const index = listChartId.indexOf(id);
                if (index > -1) {
                    listChartId.splice(index, 1);
                }
            }
            drawChart();
        });

        function drawChart(id = 0) {
            //        console.log(listChartId);
            //    var colors = ['#3f33f1', '#ef00ff', '#49eabe', '#c8ea0a', '#ea710a', '#eaf909', '#f336ca', '#7a00ff','#2bb4e8'];
            var colors = ['#0071d1', '#63b300', '#e6990e', '#912fc0', '#d94d6c', '#0f8071', '#f270b9', '#f2c428', '#2fa5cb',
                '#5b54d5', '#00b4a2', '#eb3c97'
            ];
            var video_type = $(".select-video-type").val();
            var range_chart = $("#range_chart").val();
            var start = $("#startDate").val();
            var end = $("#endDate").val();
            $.ajax({
                type: "GET",
                url: "getCampaignChart",
                data: {
                    'ids': listChartId,
                    "video_type": video_type,
                    "range_chart": range_chart,
                    "start": start,
                    "end": end
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    var html =
                        '<canvas id="chart-all"></canvas>';
                    $("#control-chart").html(html);
                    var datasets = new Array();
                    var label = new Array();
                    $.each(data.campaigns, function(key, campaign) {
                        var viewsData = new Array();
                        $.each(campaign.data, function(k, value) {
                            var temp = {
                                "x": value.date,
                                "y": value.views
                            };
                            viewsData.push(temp);
                        });
                        var dataset = {
                            label: campaign.campaign_name,
                            data: viewsData,
                            fill: false,
                            borderColor: colors[key],
                            backgroundColor: colors[key],
                            borderWidth: 1.5
                        };
                        datasets.push(dataset);
                    });

                    drawLineCharts('chart-all', data.dates, datasets);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }



        $("#group_channel_search").change(function() {
            refreshJsTree();
        });
        $("#channel_genre").change(function() {
            refreshJsTree();
            $.ajax({
                type: "GET",
                url: "loadSubgenre",
                data: {
                    "channel_genre": $(this).val()
                },
                dataType: 'text',
                success: function(data) {
                    $("#channel_subgenre").html(data);
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
        $("#channel_subgenre").change(function() {
            refreshJsTree();
        });
        $("#channel_name").change(function() {
            refreshJsTree();
        });
        $("#limitChannel").change(function() {
            refreshJsTree();
        });
        $("#views").change(function() {
            refreshJsTree();
        });
        $("#subs").change(function() {
            refreshJsTree();
        });

        function refreshJsTree() {
            $('#jstree-channel').jstree("destroy").empty();
            var form = $("#filter_channel").serialize();
            console.log(form);
            $.ajax({
                type: "GET",
                url: "/getHubsByChannelId",
                data: form + "&crossType=2",
                dataType: 'json',
                success: function(data) {
                    $("#dialog-loading").hide();
                    jsonObj = [];
                    $.each(data, function(key, value) {
                        var item = {};
                        item["id"] = value.channel_id;
                        item["parent"] = "#";
                        item["text"] = value.channel_name;
                        item["icon"] = "ti-user";
                        jsonObj.push(item);
                        if (value.hub.length > 0) {
                            $.each(value.hub, function(k, v) {
                                item = {};
                                item["id"] = v.id;
                                item["parent"] = value.channel_id;
                                item["parentname"] = value.channel_name;
                                item["text"] = v.name;
                                item["icon"] = "ti-server";
                                jsonObj.push(item);
                            });
                        }
                    });
                    $('#jstree-channel').jstree({
                        "core": {
                            'themes': {
                                'responsive': false
                            },
                            "check_callback": true,
                            'data': jsonObj
                        },
                        'types': {
                            'default': {
                                'icon': 'ti-user'
                            },
                            'file': {
                                'icon': 'fa fa-file'
                            }
                        },
                        'plugins': ['types', 'checkbox']
                        //                "checkbox" : {
                        //					 "three_state": false,
                        //					"keep_selected_style" : false
                        //				},
                        //                "plugins": ["checkbox"]

                    }).bind("loaded.jstree", function(event, data) {
                        // you get two params - event & data - check the core docs for a detailed description
                        $(this).jstree("open_all");
                        $(this).jstree("check_all");
                    });
                },
                error: function(data) {

                }
            });
        }

        $(".btn-submit-multi-music").click(function() {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var hubs = [];
            var selectedNodes = $('#jstree-channel').jstree("get_selected", true);
            $.each(selectedNodes, function(key, value) {
                if (value.parent != "#") {
                    var item = {};
                    item["channel_id"] = value.parent;
                    item["channel_name"] = value.original.parentname;
                    item["hub_id"] = value.id;
                    hubs.push(item);
                }
            });
            var trackId = $("#track-id").val();
            var trackType = $("#track-type").val();
            var crossType = $("#cross-type").val();
            var campId = $("#camp-id").val();
            $.ajax({
                type: "GET",
                url: "/autoMakeLyricHub",
                data: {
                    'trackId': trackId,
                    'trackType': trackType,
                    "crossType": crossType,
                    "campId": campId,
                    'hubs': hubs
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    console.log(data.content);
                    var mess = data.content;
                    if (data.status == 'success') {
                        mess = '<ol>';
                        $.each(data.content, function(key, value) {
                            //                            console.log(value);
                            mess += '<li>' + value.channel_name + ' &rarr; ' + (value.status ==
                                1 ?
                                "success" : "fail") + '  </li>';
                        });
                    }
                    $.Notification.notify(data.status, 'top center', '', mess);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });
        $(".btn-show-hub").click(function(e) {
            e.preventDefault();
            var musicId = $(this).attr("data-music-id");
            var musicType = $(this).attr("data-music-type");
            var crossType = $(this).attr("data-cross-type");
            var campId = $(this).attr("data-camp-id");
            $("#track-id").val(musicId);
            $("#track-type").val(musicType);
            $("#cross-type").val(crossType);
            $("#camp-id").val(campId);
            var songName = $(this).attr("data-songname");
            $("#dialog-loading").show();
            refreshJsTree();
            $("#dialog-multi-upload-title").html("Config auto upload videos for " + songName);
            $(".dialog-icon").html('<i class="ti-music-alt"></i>');
            $('#dialog_multi_upload').modal({
                backdrop: false
            });
        });

        $("#channel_genre_assign").change(function() {
            genreAssign = $(this).val();
            $.ajax({
                type: "GET",
                url: "loadSubgenre",
                data: {
                    "channel_genre": $(this).val()
                },
                dataType: 'text',
                success: function(data) {
                    $("#channel_subgenre_assign").html(data);
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
            subGenreAssign = [];
            getListChannel(camIdAssign, 0);

        });
        var camIdAssign;
        var genreAssign;
        var subGenreAssign;

        function getListChannel(id, show = 1) {

            camIdAssign = id;
            $(".formAssignChannel_content").html("");
            $(".dialog_assign_channel_loadding").show();
            $("#channel_cam_id").val(id);
            $("#dialog_assign_channel_title").html(`Assign Task For ${id}`);
            $.ajax({
                type: "GET",
                url: "/getListChannelManualPromos",
                data: {
                    "id": camIdAssign
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $(".dialog_assign_channel_loadding").hide();
                    var html = "";
                    html += '<div class="col-md-12">';
                    html +=
                        '<table class="table" style="border-collapse: inherit"><thead><tr><th style="width: 5%;text-align: center">#</th>';
                    html += '<th style="width: 5%"><div class="checkbox checkbox-primary">';
                    html += '<input id="select_all" type="checkbox" name="select_all">';
                    html += '<label for="select_all" class="m-b-22 p-l-0" style="margin-bottom: 1rem"></label>';
                    html += '</div></th><th>User</th><th></th>';
                    var i = 0;
                    $.each(data.users, function(key, value) {
                        i = i + 1;
                        html += '<tr><td scope="row">' + i + '</td>';
                        html += '<td>';
                        html += '<div class="checkbox checkbox-primary">';
                        html +=
                            '<input class="checkbox-multi" type="checkbox" name="chkChannelAll[]" id="ck-video' +
                            value.user_name + '" value="' + value.user_name + '" >';
                        html += '<label class="m-b-18 p-l-0" for="ck-video' + value.user_name +
                            '"></label></div>';
                        html += '</td>';
                        html += '<td>' + value.user_name + '</td>';
                        html += `<td>
                                ${value.start_campaign}
                                <span class="badge badge-success">Lyric ${value.lyric_video} task</span>
                                <span class="badge badge-success">Mix ${value.promo_mix} task</span>
                            </td></tr>`;

                    });
                    html += '</table></div>';
                    $(".formAssignChannel_content").html(html);
                    $('#select_all').change(function() {
                        var checkboxes = $(this).closest('.table').find('.checkbox-multi');
                        checkboxes.prop('checked', $(this).is(':checked'));
                    });
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
            if (show == 1) {
                $(".modal-dialog").removeClass("modal-80");
                $('#dialog_assign_channel').modal({
                    backdrop: false
                });
            }
        }

        function assgignChannel(type) {
            var $this = $("#btn-assign-" + type);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            var formData = $("#formAssignChannel").serialize();
            $.ajax({
                type: "POST",
                url: "/channelAssign",
                data: formData + "&type=" + type,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                    getListChannel(camIdAssign, 0);
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        }

        function getListUser(id) {

            $.ajax({
                type: "GET",
                url: "/lyricGetListUser",
                data: {
                    "id": id
                },
                dataType: 'text',
                success: function(data) {
                    $(".formAssignLyric_content").html(data);
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
            $('#dialog_assign_lyric').modal({
                backdrop: false
            });
        }

        function assgignMakeLyric() {
            var $this = $(".btn-assign-lyric");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            var formData = $("#formAssignLyric").serialize();
            $.ajax({
                type: "GET",
                url: "/lyricAssign",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        }

        $(".btn-submit-upload").click(function(e) {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var type = $(this).val();
            var form_file = new FormData();
            var file = $("#" + type + "_upload").val();
            if (file != '') {
                console.log(file);
            } else {
                $.Notification.notify('error', 'top center', '', "You must choose a file");
                return;
            }

            form_file.append('promoUpload', $("#" + type + "_upload")[0].files[0]);
            form_file.append('_token', '{{ csrf_token() }}');
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/promo-upload",
                data: form_file,
                contentType: false,
                processData: false,
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.notify('success', 'top center', '', data.message);
                    var url = "http://automusic.win/" + data.uploaded_promo;
                    $("#" + type + "_url").val(url);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                    //                but.button('reset');
                }
            });
        });

        function nextChartsCampaign(data) {
            var nextId = $("#" + data + "-campaign-id").val();
            chartsCampaign(nextId, 0);
        }

        function chartsCampaign(id, show = 1) {
            $("#content-dialog").html("");
            $("#dialog-loading").show();
            var listIdStr = $("#list-campaign-id").val();
            var listId = JSON.parse(listIdStr);
            //        console.log(listId);
            var total = listId.length;
            $.each(listId, function(k, v) {

                if (v == id) {
                    if (k == 0) {
                        $("#next-campaign-id").val(listId[1]);
                        $("#prev-campaign-id").val(listId[total - 1]);
                    } else if (k == total - 1) {
                        $("#next-campaign-id").val(listId[0]);
                        $("#prev-campaign-id").val(listId[total - 2]);
                    } else {
                        $("#next-campaign-id").val(listId[k + 1]);
                        $("#prev-campaign-id").val(listId[k - 1]);
                    }
                    //                console.log("curr:"+id+" next:"+$("#next-campaign-id").val()+" prev:"+$("#prev-campaign-id").val());
                }
            });
            $.ajax({
                type: "GET",
                url: '/getcampaignstatistics',
                data: {
                    'id': id
                },
                dataType: 'json',
                success: function(data) {
                    var html =
                        '<div class="row"><div class="col-md-12"><div class="control-chart"><i onclick="nextChartsCampaign(\'prev\')" class="ion-chevron-left float-left cur-poiter"></i><i onclick="nextChartsCampaign(\'next\')" class="ion-chevron-right float-right cur-poiter m-r-10"></i></div><canvas id="chart-video-daily"></canvas></div></div>';
                    $("#content-dialog").html(html);
                    $("#dialog-loading").hide();
                    var dataChart = JSON.parse(data.views_detail);
                    var label = new Array();
                    //                var dataDailyCampaign = new Array();
                    var dataDailyLyric = new Array();
                    var dataDailyMix = new Array();
                    var dataDailyOfficial = new Array();
                    var dataDailyShort = new Array();
                    var datasets = new Array();
                    $.each(dataChart, function(key, value) {
                        if (value.hasOwnProperty('oficial')) {
                            label.push(value.date);
                            dataDailyOfficial.push(value.oficial);
                            dataDailyLyric.push(parseInt(value.lyric_athen));
                            dataDailyMix.push(parseInt(value.compi_athen));
                            dataDailyShort.push(parseInt(value.short_athen));
                        }

                    });
                    var dataset1 = {
                        label: 'Lyric Daily Views',
                        data: dataDailyLyric,
                        fill: false,
                        backgroundColor: 'rgb(25, 165, 253)',
                        borderColor: 'rgb(25, 165, 253)',
                        borderWidth: 1
                    };
                    datasets.push(dataset1);
                    var dataset2 = {
                        label: 'Official Video Daily Views',
                        data: dataDailyOfficial,
                        fill: false,
                        backgroundColor: 'rgb(255, 99, 132)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 1
                    };
                    datasets.push(dataset2);
                    var dataset3 = {
                        label: 'Mix Daily Views',
                        data: dataDailyMix,
                        fill: false,
                        backgroundColor: 'rgb(82,187,86)',
                        borderColor: 'rgb(82,187,86)',
                        borderWidth: 1
                    };
                    datasets.push(dataset3);
                    var dataset4 = {
                        label: 'Short Daily Views',
                        data: dataDailyShort,
                        fill: false,
                        backgroundColor: '#f78829',
                        borderColor: '#f78829',
                        borderWidth: 1
                    };
                    datasets.push(dataset4);
                    drawLineCharts('chart-video-daily', label, datasets);
                    $("#dialog-list-tile").text(data.campaign_name);
                },
                error: function(data) {
                    console.log(data);
                }
            });
            if (show == 1) {
                $(".modal-dialog").addClass("modal-80");
                $('#dialog_music_channel').modal({
                    backdrop: false
                });
            }
        }

        $(".campaign_select").change(function() {
            $("#campaign_name_add").val($(this).val());
        });
        $(".select-status-campaign").change(function() {
            $("#frm").submit();
        });
        //charts('.campaign_detail', '/getcampaignstatistics');

        $(".btn-import-campaign").click(function(e) {
            e.preventDefault();
            clearForm();
            $("#dialog_import_campaign_title").html('Add Campaign');
            $('#dialog_import_campaign').modal({
                backdrop: false
            });
        });

        function editCampaign(id,dialog=1) {
            clearForm();
            $("#fulfillment-data").html("");
            $.ajax({
                type: "GET",
                url: "/getcampaignstatistics",
                data: {
                    "id": id
                },
                dataType: 'json',
                success: function(data) {
                    $("#dialog-loading-add-campaign").hide();
                    console.log(data);
                    if(data.short_text!=null){
                        var shortText = JSON.parse(data.short_text);
                        $("#some_note").val(shortText.some_note);
                        $("#hashtags").val(shortText.hashtags);
                        $("#content_drive").val(shortText.content_drive);
                        $("#hook").val(shortText.hook);
                         setupCharacterCounter('#hook', '#hookCount', shortText.hook);
                    }
                    $("#campaign_name").val(data.campaign_name);
                    $("#campaign_start_date").val(data.start_date_edit);
                    $("#campaign_start_time").val(data.campaign_start_time);
                    $("#channelName").val(data.channel_name);
                    $("#artists_channel").val(data.artists_channel);
                    $("#genre").val(data.genre).change();
                    $("#status").val(data.status).change();
                    $("#seller").val(data.seller).change();
                    $("#period").val(data.period).change();
                    $("#label").val(data.label);
                    $("#artist").val(data.artist);
                    $("#songname").val(data.song_name);
                    $("#artists_channel").val(data.artists_channel);
                    $("#artists_playlist").val(data.artists_playlist);
                    $("#spotify_id").val(data.spotify_id);
                    $("#artists_social").val(data.artists_social);
                    $("#desc_video").val(data.desc_video);
                    $("#official_video").val(data.official_video);
                    $("#bitly_url").val(data.bitly_url);
                    $("#guest_artist_1").val(data.guest_artist_1);
                    $("#guest_artist_2").val(data.guest_artist_2);
                    $("#guest_artist_3").val(data.guest_artist_3);
                    $("#promo_keywords").val(data.promo_keywords);
                    $("#audio_url").val(data.audio_url);
                    if (data.audio_url != null) {
                        $("#link_audio").html("<a href='" + data.audio_url + "' target='_blank'>Download</a>")
                    }
                    $("#lyric_url").val(data.lyric_url);
                    $("#lyrics").val(data.lyrics);
                    $("#deezer_artist_id").val(data.deezer_artist_id);
                    $("#tier").val(data.tier).change();
                    $("#cam_id").val(id);
                    $("#campaign_duration").val(data.duration).change();
                    $("#target_total").val(data.target);
                    $("#cam_type").val(data.type).change();
                    if (data.type == 4 && data.revshare_client != null) {
                        $("#revshare_client").val(data.revshare_client).change();
                    }
                    $("#money").val(data.money);
                    $("#amount_paid").val(data.amount_paid);
                    $("#bass_percent").val(data.bass_percent);

                    $("#remaining").html("");
                    var official = JSON.parse(data.official);
                    if (official != null) {
                        $("#start_view_official").val(official.start_view);
                        $("#start_like_official").val(official.start_like);
                        $("#start_cmt_official").val(official.start_cmt);
                        $("#start_sub_official").val(official.start_sub);
                        $("#target_view").val(official.target_view);
                        $("#target_like").val(official.target_like);
                        $("#target_cmt").val(official.target_cmt);
                        $("#target_sub").val(official.target_sub);
                        $("#crypto_usd").val(official.crypto_usd);
                        $("#crypto_view").val(official.crypto_view);
                        $("#crypto_like").val(official.crypto_like);
                        $("#crypto_sub").val(official.crypto_sub);
                        $("#crypto_cmt").val(official.crypto_cmt);

                        $("#crypto_usd_last").val(official.crypto_usd_last);
                        $("#crypto_view_last").val(official.crypto_view_last);
                        $("#crypto_like_last").val(official.crypto_like_last);
                        $("#crypto_sub_last").val(official.crypto_sub_last);
                        $("#crypto_cmt_last").val(official.crypto_cmt_last);

                        $("#crypto_cmt_link").val(official.crypto_cmt_link);
                        $("#crypto_cmt_content").val(official.crypto_cmt_content);
                        $("#crypto_cmt_content_finish").val(official.crypto_cmt_content_finish);
                        $("#cmt_schedule").val(official.cmt_schedule);
                        $("#cmt_number").val(official.cmt_number);
                        $("#adsense_usd").val(official.adsense_usd);
                        $("#adsense_view").val(official.adsense_view);

                        $("#crypto_group_comment").val(official.crypto_group_comment);

                        $("#crypto_view_run").val(official.crypto_view_run).change();
                        $("#crypto_like_run").val(official.crypto_like_run).change();
                        $("#crypto_sub_run").val(official.crypto_sub_run).change();
                        $("#crypto_cmt_run").val(official.crypto_cmt_run).change();
                        $("#adsense_view_run").val(official.adsense_view_run).change();
                        $("#facebook_usd").val(official.facebook_usd);
                        $("#cmt_count").html(`(${data.number_comment})`);
                        $("#cmt_count_finish").html(`(${data.number_comment_finish})`);
                    }

                    var artistInfo = JSON.parse(data.artist_info);
                    if (artistInfo != null) {
                        $("#artist_picture_href").attr("href", artistInfo.picture);
                        $("#artist_picture").attr("src", artistInfo.picture);
                        $("#artis_info_name").html(artistInfo.name);
                        $("#artis_info_email").html(artistInfo.email);
                        $("#hometown").html(artistInfo.hometown);
                        $("#city").html(artistInfo.city);
                        $("#describe_yourself").text(artistInfo.describe_yourself);
                        $("#style_music").text(artistInfo.style_music);
                        $("#bio").text(artistInfo.bio);
                        $("#related").text(artistInfo.related);
                        $("#similar").text(artistInfo.similar);
                        $("#biggest_media_coverage").text(artistInfo.biggest_media_coverage);
                        $("#popular_songs").text(artistInfo.popular_songs);
                        $("#biggest_collaborators").text(artistInfo.biggest_collaborators);
                        $("#publishing_company").html(artistInfo.publishing_company);
                    }
                    var releaseInfo = JSON.parse(data.release_info);
                    if (releaseInfo != null) {
                        if (releaseInfo.pic_single != null) {
                            $("#div_pic_single").show();
                            $("#pic_single_href").attr("href", releaseInfo.pic_single);
                            $("#pic_single").attr("src", releaseInfo.pic_single);
                        }
                        if (releaseInfo.pic_album != null) {
                            $("#div_pic_album").show();
                            $("#pic_album_href").attr("href", releaseInfo.pic_album);
                            $("#pic_album").attr("src", releaseInfo.pic_album);
                        }
                        if (releaseInfo.pic_logo != null) {
                            $("#div_pic_logo").show();
                            $("#pic_logo_href").attr("href", releaseInfo.pic_logo);
                            $("#pic_logo").attr("src", releaseInfo.pic_logo);
                        }
                        if (releaseInfo.pic_main_profile != null) {
                            $("#div_pic_main_profile").show();
                            $("#pic_main_profile_href").attr("href", releaseInfo.pic_main_profile);
                            $("#pic_main_profile").attr("src", releaseInfo.pic_main_profile);
                        }
                        if (releaseInfo.pic_banner != null) {
                            $("#div_pic_banner").show();
                            $("#pic_banner_href").attr("href", releaseInfo.pic_banner);
                            $("#pic_banner").attr("src", releaseInfo.pic_banner);
                        }
                        $("#div_pic_press").html(data.pic_press_html);
                        $("#song_quote").html(releaseInfo.song_quote);

                        $("#smart_link").val(releaseInfo.smart_link);
                        $("#font").html(releaseInfo.font);
                        $("#producer").text(releaseInfo.producer);
                        $("#producer_collaborators").text(releaseInfo.producer_collaborators);
                        $("#guest_artists").text(releaseInfo.guest_artists);
                        $("#engagement").html(releaseInfo.engagement);
                        $("#official_is_video_clean").html(releaseInfo.official_is_video_clean);
                        $("#official_video_url").html(releaseInfo.official_video_url);
                        $("#official_director").html(releaseInfo.official_director);
                        $("#official_intended_release_date").html(releaseInfo.official_intended_release_date);
                        if (releaseInfo.official_shoot_pics != null) {
                            $("#div_official_shoot_pics").show();
                            $("#official_shoot_pics_href").attr("href", releaseInfo.official_shoot_pics);
                            $("#official_shoot_pics").attr("src", releaseInfo.official_shoot_pics);
                        } else if (releaseInfo.lyric_video_bg != null) {
                            $("#div_lyric_video_bg").show();
                            $("#lyric_video_bg").html(releaseInfo.lyric_video_bg);
                        }
                        $("#album_name").html(releaseInfo.album_name);
                        $("#album_release_date").html(releaseInfo.album_release_date);
                        $("#album_quote").html(releaseInfo.album_quote);
                        $("#album_guests").text(releaseInfo.album_guests);
                        $("#album_producers").text(releaseInfo.album_producers);
                        $("#album_stream_link").html(releaseInfo.album_stream_link);
                        $("#album_previous_name").html(releaseInfo.album_previous_name);
                        $("#album_upc").html(releaseInfo.album_upc);
                        $("#viral_video").html(data.viral_video_html);
                        $("#press_release").text(releaseInfo.press_release);
                        var press_release = releaseInfo.press_release;
                        if(press_release==null){
                            press_release='';
                        }

                        $("#summernote_press_release").summernote("destroy");
                        $("#summernote_press_release").html(press_release);
                        initSummernote('#summernote_press_release', true,'');
                        var markup = $('#summernote_press_release').summernote('code');
                        $("#press_release").val(markup);
                        if(releaseInfo.audio_clean!=null){
                            $("#audio_clean").html(`<a href="${releaseInfo.audio_clean}" target="_blank">${releaseInfo.audio_clean}</a>`);
                        }
                        if(releaseInfo.audio_dirty!=null){
                            $("#audio_dirty").html(`<a href="${releaseInfo.audio_dirty}" target="_blank"><span>${releaseInfo.audio_dirty}</span></a>`);
                        }
                        if(releaseInfo.audio_instrumental!=null){
                            $("#audio_instrumental").html(`<a href="${releaseInfo.audio_instrumental}" target="_blank"><span>${releaseInfo.audio_instrumental}</span></a>`);
                        }
                        if(audio_acapella.audio_dirty!=null){
                            $("#audio_acapella").html(`<a href="${releaseInfo.audio_acapella}" target="_blank"><span>${releaseInfo.audio_acapella}</span></a>`);
                        }

//                         var dataArray = JSON.parse(releaseInfo.official_video_versions);
                        $('#official_orther_version').html("");
                        $.each(releaseInfo.official_video_versions, function(index, item) {
                                var officialVersion =    `<div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <input id="official_video_version" type="text" name="official_video_version[]" class="form-control" style="background: #ccc" value="${item.link}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <input id="start_view_official_version" type="nomber" name="start_view_official_version[]" class="form-control" value="${item.views}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <input id="start_like_official_version" type="number" name="start_like_official_version[]" class="form-control" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <input id="start_cmt_official_version" type="number" name="start_cmt_official_version[]" class="form-control" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <input id="start_sub_official_version" type="number" name="start_sub_official_version[]" class="form-control" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                            $('#official_orther_version').append(officialVersion);                           
//                            $('#official_orther_version').append('<span><a href="' + item.link + '">' + item.name + '</a></span><br>');
                        });
                       
                        

                    }

                    $("#fulfillment-data").html(data.check_list_html);
                    savedAllocations = data.submission_info!=null?JSON.parse(data.submission_info):[];
                    totalMoney = data.money;
                    loadSavedAllocations();
                    $("#badge-sub-count").html(savedAllocations.length);
                    $('.fulfillment-tooltip').tooltip();

                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });

            $("#dialog_import_campaign_title").html(`<span class="cur-poiter" onclick="editCampaign(${id},0)">Edit Campaign ${id}</span>`);
            $("#dialog-loading-add-campaign").show();
            if(dialog){
                $('#dialog_import_campaign').find(".modal-dialog").addClass("modal-80");
//                $(".modal-dialog").addClass("modal-80");
                $('#dialog_import_campaign').modal({
                    backdrop: false
                });
               
            }
        }

        function clearForm() {
            $("#some_note").val("");
            $("#hashtags").val("");
            $("#content_drive").val("");
            $("#hook").val("");
            $("#campaign_name").val("");
            $("#campaign_start_date").val(moment().format('YYYY-MM-DD'));
            $("#campaign_start_time").val('00:00:00');
            $("#artists_channel").val("");
            $("#channelName").val("");
            $("#genre").val("-1");
            $("#status").val("1");
            $("#label").val("");
            $("#artist").val("");
            $("#songname").val("");
            $("#artists_channel").val("");
            $("#artists_playlist").val("");
            $("#artists_social").val("");
            $("#desc_video").val("");
            $("#spotify_id").val("");
            $("#bitly_url").val("");
            $("#guest_artist_1").val("");
            $("#guest_artist_2").val("");
            $("#guest_artist_3").val("");
            $("#promo_keywords").val("");
            $(".audio_url").val("");
            $("#link_audio").html("");
            $(".lyric_url").val("");
            $("#lyrics").val("");
            $("#deezer_artist_id").val("0");
            $("#tier").val("2");
            $("#cam_id").val("");
            $("#target").val("");
            $("#campaign_duration").val(1).change();
            $("#target_total").val("0M");
            $("#remaining").html("");
            $("#official_video").val("");
            $("#start_view_official").val(0);
            $("#start_like_official").val(0);
            $("#start_cmt_official").val(0);
            $("#start_sub_official").val(0);
            $("#target_view").val("");
            $("#target_like").val(0);
            $("#target_cmt").val(0);
            $("#target_sub").val(0);
            $("#crypto_usd").val(0);
            $("#crypto_usd_last").val(0);
            $("#crypto_view").val(0);
            $("#crypto_view_last").val(0);
            $('#crypto_view_run').prop('checked', false);
            $("#crypto_like").val(0);
            $("#crypto_like_last").val(0);
            $('#crypto_like_run').prop('checked', false);
            $("#crypto_subs").val(0);
            $("#crypto_subs_last").val(0);
            $('#crypto_subs_run').prop('checked', false);
            $("#crypto_cmt").val(0);
            $("#crypto_cmt_last").val(0);
            $('#crypto_cmt_run').prop('checked', false);
            $("#crypto_group_comment").val("");
            $("#crypto_cmt_link").val("");
            $("#crypto_cmt_content").val("");
            $("#crypto_cmt_content_finish").val("");
            $("#cmt_schedule").val(1).change();
            $("#cmt_number").val(0);
            $("#adsense_usd").val(0);
            $("#adsense_view").val(0);
            $('#adsense_view_run').prop('checked', false);
            $("#facebook_usd").val(0);
            $("#money").val("0");
            $("#amount_paid").val("0");
            $("#bass_percent").val("0");

            $("#artist_picture_href").attr("href", "");
            $("#artist_picture").attr("src", "images/default-avatar.png");
            $("#artis_info_name").html("");
            $("#artis_info_email").html("");
            $("#hometown").html("");
            $("#city").html("");
            $("#describe_yourself").text("");
            $("#style_music").text("");
            $("#bio").text("");
            $("#related").text("");
            $("#similar").text("");
            $("#biggest_media_coverage").text("");
            $("#popular_songs").text("");
            $("#biggest_collaborators").text("");
            $("#publishing_company").html("");
            $("#press_release").text("");

            $("#div_pic_single").hide();
            $("#div_pic_album").hide();
            $("#div_pic_logo").hide();
            $("#div_pic_banner").hide();

            $("#div_pic_main_profile").hide();
            //        $("#pic_single_href").attr("href","");
            //        $("#pic_single").attr("src","assets/images/users/avatar-1.jpg");
            //        $("#pic_album_href").attr("href","");
            //        $("#pic_album").attr("src","assets/images/users/avatar-1.jpg");
            //        $("#pic_logo_href").attr("href","");
            //        $("#pic_logo").attr("src","assets/images/users/avatar-1.jpg");
            //        $("#pic_banner_href").attr("href","");
            //        $("#pic_banner").attr("src","assets/images/users/avatar-1.jpg");
            //        $("#pic_press_href").attr("href","");
            //        $("#pic_press").attr("src","assets/images/users/avatar-1.jpg");
            //        $("#pic_main_profile_href").attr("href","");
            //        $("#pic_main_profile").attr("src","assets/images/users/avatar-1.jpg");
            $("#song_quote").html("");
            $("#smart_link").val("");
            $("#font").html("");
            $("#producer").text("");
            $("#producer_collaborators").text("");
            $("#guest_artists").text("");
            $("#engagement").html("");
            $("#official_is_video_clean").html("");
            $("#official_video_url").html("");
            $("#official_director").html("");
            $("#official_intended_release_date").html("");
            $("#div_official_shoot_pics").hide();
            $("#div_lyric_video_bg").hide();
            $("#album_name").html("");
            $("#album_release_date").html("");
            $("#album_quote").html("");
            $("#album_guests").text("");
            $("#album_producers").text("");
            $("#album_stream_link").html("");
            $("#album_previous_name").html("");
            $("#album_upc").html("");
            $("#viral_video").html("");
            $("#div_pic_press").html("");
        }

        $(".campaign-status").click(function(e) {
            e.preventDefault();
            var id = $(this).attr("data-id");
            var rbclass = '';
            var text = '';
            $.ajax({
                type: "GET",
                url: '/campaignstatus',
                data: {
                    'id': id
                },
                dataType: 'json',
                success: function(data) {
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                    if (data.newStatus == 0) {
                        rbclass = 'ribbon-red';
                        text = 'Finish';
                    } else if (data.newStatus == 2) {
                        rbclass = 'ribbon-yellow';
                        text = 'Upcoming';
                    } else if (data.newStatus == 1) {
                        rbclass = '';
                        text = 'Active';
                    }
                    $(".sp-" + id).removeClass("ribbon-red ribbon-yellow").addClass(rbclass);
                    $(".sp-" + id).text(text);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
        $(".btn-add-campaign").click(function(e) {
            e.preventDefault();
            var validate = Validator({
                form: '#formadd',
                notify: true,
                rules: [
                    Validator.isRequired({
                        tab: "#profile-b1",
                        input: "#genre",
                        name: "Genre"
                    }),
                    Validator.isRequired({
                        tab: "#profile-b1",
                        input: "#artist",
                        name: "Artist"
                    }),
                    Validator.isRequired({
                        tab: "#profile-b1",
                        input: "#songname",
                        name: "Song Name"
                    }),
                ]
            });
            if (validate) {
                return;
            }

            var markup = $('#summernote_press_release').summernote('code');
            $("#press_release").val(markup);
//            var pressRelease = $("#press_release").text();
            var form = $("#formadd");
            var formData = form.serialize();
//                            console.log(formData);

            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            $this.attr("disabled", true);

            $.ajax({
                type: "POST",
                url: "/addcampaign",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $this.attr("disabled", false);
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                    $(".campaign_select").html(data.option);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                    console.log('Error:', data);
                }
            });
        });
        $(".btn-edit-campaign").click(function(e) {
            e.preventDefault();
            var form = $("#formedit");
            var formData = form.serialize();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/addcampaign",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data
                    .contentedit);
                    //            $(".campaign_select").html(data.option);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                    console.log('Error:', data);
                }
            });
        });

        function detailCampaign(id) {
            $("#detail").slideUp('fast');
            $("#detail").html('');
            $("#detail-name").html('');
            $("#loading-list-video").show();
            $.ajax({
                type: "GET",
                url: "/detailcampaign",
                data: {
                    "id": id
                },
                dataType: 'json',
                success: function(data) {
                    $("#loading-list-video").hide();
                    //                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                                            console.log(data);
                    var option = `<select class="col-md-2 m-l-10 form-control input-sm select-period-views">
                            
                                                ${data.month}
                    </select>`;
                    if (data.datas.length > 0) {
                        $("#detail-name").html('Campaign Details: '+ id +' - '+ data.datas[0].campaign_name);
                        $("#detail-name").append(option);
                        $("#detail-name").append('<i onclick="downloadListvideo(\'' + id +
                            '\')" data-toggle="tooltip" data-placement="top" data-original-title="Download list video" class="m-l-10 ti-download cur-poiter td-info color-h"></i>'
                            );
                        makeTableDetail(data.datas);
                        videoSync();
                    } else {
                        $("#detail").html('No Data');
                        $('#detail').slideDown("fast");
                    }

                    $('html, body').animate({
                        scrollTop: $("#detail").offset().top
                    }, 100);

                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        }

        function checkList(id) {
            $(".dialog_check_list_loadding").show();
            $(".formchecklist_content").html("");
            $("#campaign_id").val(id);
            $.ajax({
                type: "GET",
                url: "/getCheckList",
                data: {
                    "id": id
                },
                dataType: 'text',
                success: function(data) {
                    console.log(data);
                    $(".dialog_check_list_loadding").hide();
                    $(".formchecklist_content").html(data);
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
            //    $(".modal-dialog").addClass("modal-80");
            $('#dialog_check_list').modal({
                backdrop: false
            });
        }

        function importChecklist() {
            var $this = $(".btn-import-checklist");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            var formData = $("#formchecklist").serialize();
            $.ajax({
                type: "GET",
                url: "/addCheckList",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        }

        function downloadListvideo(id) {
            var month =  $(".select-period-views").val();
            window.location.href = `/downloadListvideo?id=${id}&month=${month}`;
        }

        function makeTableDetail(data) {

            var html = '';
            html += '<div id="channel-chart" style="overflow: auto">';
            html +=
                '<table id="tbl-detail-list" class="table" style="border-collapse: inherit"><thead><tr><th>#</th><th>User</th><th>Channel</th><th>Type</th><th>Published</th><th>VideoId</th><th>Title</th>';
            html += '<th class="text-center">Ads Views</th>';
            html += '<th  class="text-center">Bass Views</th>';
            html += '<th  class="text-center">Total Views</th>';
            html += '<th  class="text-center">Likes</th>';
            //        html += '<th style="text-align: left">Dislikes</th>';
            //        html += '<th style="text-align: left">Comments</th>';
            //        html += '<th style="text-align: left">EndScreens</th>';
            var i = 0;
            $.each(data, function(key, value) {
                i = i + 1;
                var type = '';
                if (value.video_type == 1) {
                    type = 'Official';
                } else if (value.video_type == 2) {
                    type = 'Lyric';
                } else if (value.video_type == 5) {
                    type = 'Mix';
                } else if (value.video_type == 6) {
                    type = 'Short';
                }
                var publish = getTIMESTAMP(value.publish_date);
                var dailyChart =
                    html += '<tr><td scope="row">' + i + '</td><td>' + value.username + '</td><td>' + value
                    .channel_name + '</td><td>' + type +
                    '</td><td>' + publish + '</td><td>' + value.video_id + '</td>';
                html += '<td class="text-ellipsis"><a target="_blank" href="https://www.youtube.com/watch?v=' + value.video_id +
                    '"><img class="video-img_thumb" src="https://i.ytimg.com/vi/' + value.video_id +
                    '/default.jpg">' +
                    value.video_title + '</a>&nbsp;<a taget="_blank" href="downloadVideoInfo?id=' + value.id +
                    '"><i class="ti-download cur-poiter td-info color-h"></i></a>&nbsp;<a class="cur-poiter video-sync btn-success" data-id="' +
                    value.id + '"><i class="ti-reload" ></i></a></td>';
                html += `<td class="text-center">
                <span 
                    class="cur-poiter"
                    data-type="1" data-container="body" 
                    data-toggle="popover"
                    data-placement="top" data-html="true"
                    data-content='<input placeholder="ads views" class="form-control m-b-5 ads_views_value_${value.id}" type="text" name="ads_views"><button type="button" class="btn btn-sm btn-ssm btn-success waves-effect m-b-5 " onclick="updateAdsViews(${value.id})">Submit</button>'>
                    <span id="ads_views_${value.id}">${number_format(value.ads_views, 0, '.', ',')}</span>
                </span>    
                </td>`;
                html += `<td id="bass_views_${value.id}" class="text-left">${number_format(value.views - value.ads_views, 0, '.', ',')}</td>`;
                html += `<td id="total_views_${value.id}" class="text-left">${number_format(value.views, 0, '.', ',')}</td>`;
                //            html += `<td style="text-align: left"><span 
            //                                                class="cur-poiter"
            //                                                data-type="1" data-container="body" 
            //                                                data-toggle="popover"
            //                                                data-placement="top" data-html="true"
            //                                                data-content='<input placeholder="ads views" class="form-control m-b-5 ads_views_value_${value.id}" type="text" name="ads_views"><button type="button" class="btn btn-sm btn-ssm btn-success waves-effect m-b-5 " onclick="updateAdsViews(${value.id})">Submit</button>'>
            //                                                ${value.ads_views}
            //                                            </span></td>`;
                html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_like, value
                    .per_daily_like, value.like, 'like') + '</td>';
                //            html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_dislike, value
                //                .per_daily_dislike, value.dislike, 'dislike') + '</td>';
                //            html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_comment, value
                //                .per_daily_comment, value.comment, 'comment') + '</td>';
                //            html += '<td style="text-align: left">' + value.card_clicks + '</td>';
                //            html += '<td style="text-align: left">' + value.es_clicks + '</td>';
                html += '</tr>';
            });
            html += '</tbody></table></div>';
            $("#detail").html(html);
            $('#detail').slideDown("fast");

            chart('.video_detail_like', '/getcampaign', 'like');
            chart('.video_detail_dislike', '/getcampaign', 'dislike');
            chart('.video_detail_comment', '/getcampaign', 'comment');
            $('.td-info').tooltip();
            $('[data-toggle="popover"]').popover();
            //        debugger;
            //        $('#tbl-detail-list').DataTable({
            ////                    searching: true,
            ////                    ordering: true,
            //////                    processing: false,
            //////                    select: true,
            //////                    responsive: false,
            ////                    paging: false
            //            "stripeClasses": [],
            //            "order": [
            //                [0, "asc"]
            //            ],
            //            "paging": false,
            //        });
        }

        function updateAdsViews(id) {
            var adsViews = $(`.ads_views_value_${id}`).val();
            $.ajax({
                type: "GET",
                url: '/update/adsviews',
                data: {
                    id: id,
                    ads_views: adsViews,
                },
                dataType: 'json',
                success: function(data) {
                    $.Notification.autoHideNotify(data.status, 'top center', '', data.message);
                    if (data.status == "success") {
                        $(`#ads_views_${id}`).html(adsViews);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function chart(control, url, type) {
            $(control).click(function(e) {
                e.preventDefault();
                $("#content-dialog").html("");
                $("#dialog-loading").show();
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        'id': $(this).attr("detail_id")
                    },
                    dataType: 'json',
                    success: function(data) {
                        //                console.log(data);
                        var html =
                            '<div class="row"><div class="col-md-12"><canvas id="chart-video-daily"></canvas></div></div>';
                        $("#content-dialog").html(html);
                        $("#dialog-loading").hide();
                        var dataChart = JSON.parse(data.views_detail);
                        var label = new Array();
                        var dataTotal = new Array();
                        var dataDaily = new Array();
                        $.each(dataChart, function(key, value) {
                            label.push(value.date);
                            if (type == 'view') {
                                dataDaily.push(value.daily);
                            } else if (type == 'like') {
                                dataDaily.push(value.daily_like);
                            } else if (type == 'dislike') {
                                dataDaily.push(value.daily_dislike);
                            } else if (type == 'comment') {
                                dataDaily.push(value.daily_comment);
                            }
                        });
                        drawLineChart('chart-video-daily', dataDaily, label, 'Daily ' + type);
                        $("#dialog-list-tile").text(data.video_title);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
                $(".modal-dialog").addClass("modal-80");
                $('#dialog_music_channel').modal({
                    backdrop: false
                });
            });
        }

        function charts(control, url) {
            $(control).click(function(e) {
                e.preventDefault();
                $("#content-dialog").html("");
                $("#dialog-loading").show();
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        'id': $(this).attr("detail_id")
                    },
                    dataType: 'json',
                    success: function(data) {
                        var html =
                            '<div class="row"><div class="col-md-12"><div class="control-chart"><i class="ion-chevron-left float-left cur-poiter"></i><i class="ion-chevron-right float-right cur-poiter m-r-10"></i></div><canvas id="chart-video-daily"></canvas></div></div>';
                        $("#content-dialog").html(html);
                        $("#dialog-loading").hide();
                        var dataChart = JSON.parse(data.views_detail);
                        var label = new Array();
                        //                var dataDailyCampaign = new Array();
                        var dataDailyLyric = new Array();
                        var dataDailyTiktok = new Array();
                        var dataDailyOfficial = new Array();
                        var datasets = new Array();
                        $.each(dataChart, function(key, value) {
                            if (value.hasOwnProperty('oficial')) {
                                label.push(value.date);
                                dataDailyOfficial.push(value.oficial);
                                dataDailyLyric.push(parseInt(value.lyric));
                                dataDailyTiktok.push(parseInt(value.tiktok));
                            }

                        });
                        var dataset1 = {
                            label: 'Lyric Daily Views',
                            data: dataDailyLyric,
                            fill: false,
                            backgroundColor: 'rgb(25, 165, 253)',
                            borderColor: 'rgb(25, 165, 253)',
                            borderWidth: 1
                        };
                        datasets.push(dataset1);
                        var dataset2 = {
                            label: 'Official Video Daily Views',
                            data: dataDailyOfficial,
                            fill: false,
                            backgroundColor: 'rgb(255, 99, 132)',
                            borderColor: 'rgb(255, 99, 132)',
                            borderWidth: 1
                        };
                        datasets.push(dataset2);
                        var dataset3 = {
                            label: 'Tiktok Video Daily Views',
                            data: dataDailyTiktok,
                            fill: false,
                            backgroundColor: 'rgb(82,187,86)',
                            borderColor: 'rgb(82,187,86)',
                            borderWidth: 1
                        };
                        datasets.push(dataset3);
                        drawLineCharts('chart-video-daily', label, datasets);
                        $("#dialog-list-tile").text(data.campaign_name);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
                $(".modal-dialog").addClass("modal-80");
                $('#dialog_music_channel').modal({
                    backdrop: false
                });
            });
        }

        function caculatePercentDaily(id, daily, percentDaily, total, type) {
            var temp = '<div class="td-daily"><i class="font-25 color-green mdi mdi-trending-up"></i>';
            if (percentDaily < 0) {
                temp = '<div class="td-daily"><i class="font-25 color-red mdi mdi-trending-down"></i>';
            } else if (percentDaily == 0) {
                temp = '<div class="td-daily"><i class="font-25 color-y mdi mdi-trending-neutral"></i>';
            }
            var html = '<div detail_id="' + id +
                '" class="td-info cur-poiter video_detail_' + type + '" data-toggle="tooltip" data-html="true"';
            html += 'data-placement="top" data-original-title="Click to open chart<br>Daily: ' + daily + '">';
            html += temp;
            html += '<span>' + Math.abs(percentDaily) + '%</span></div> ' + total + '';
            html += '</div>';
            return html;
        }
    </script>
@endsection
