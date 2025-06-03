@extends('layouts.master')

@section('content')
    <style>
        #action-toolbar {
            /*position: fixed;*/
            /*bottom: 0;*/
            width: 100%;
            /*background-color: #f8f9fa;*/
            /*padding: 10px;*/
            /*box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);*/
            z-index: 1000;
            text-align: left;
            padding-bottom: 10px
        }

        .checkbox label::after {
            margin-left: -19px;
        }

        .song-player button {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #2D0A31;
            color: white;
            border: none;
            transition: all 0.2s ease;
        }

        .song-player button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
            border: none;
        }


        .bg-purple {
            background-color: #2D0A31;
        }

        .fixed-bottom {
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-bar {
            background-color: #fff;
            transition: width 0.1s ease;
        }

        .btn-play-pause {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            color: #2D0A31;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-play-pause:hover {
            transform: scale(1.05);
            background-color: #f0f0f0;
        }

        .song-thumb img {
            object-fit: cover;
        }

        .volume-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            border-radius: 2px;
            background: rgba(255, 255, 255, 0.2);
            outline: none;
        }

        .volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: white;
            cursor: pointer;
        }

        .volume-slider::-moz-range-thumb {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            border: none;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 1rem;
        }

        .skeleton-loading {
            position: relative;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            height: 38px;
        }

        .skeleton-loading::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.2) 20%,
                    rgba(255, 255, 255, 0.5) 60%,
                    rgba(255, 255, 255, 0) 100%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .select-container {
            position: relative;
        }

        .select-container select {
            width: 100%;
        }

        .select-container.loading select {
            display: none;
        }

        .select-container.loading .skeleton-loading {
            display: block;
        }

        .skeleton-loading {
            display: none;
        }


        .editable-container {
            display: flex;
            align-items: center;
        }

        #editIcon {
            cursor: pointer;
            margin-left: 5px;
        }

        #groupNameInput {
            border: none;
            outline: none;
            background: transparent;
            font-size: 1.25rem;
            /* Giữ font giống h5 */
            font-weight: bold;
            color: #008000;
            /* Màu xanh lá cây */
            padding: 0;
            width: auto;
            min-width: 100px;
            display: none;
            /* Mặc định ẩn */
            z-index: 1;
            text-align: left;
            /*float: right;*/
            height: 30px;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .block-word {
            overflow-wrap: break-word;
            white-space: normal;
            max-width: 100%;
            display: block;
        }
        
    .action-buttons .btn {
    transition: all 0.3s ease;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .stat-box {
        border-radius: 8px;
        padding: 10px 20px;
        margin-right: 10px;
        text-align: center;
        background-color: #f8f9fa;
    }
    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
    }

    .preview-image {
        width: 100%;
        min-height: 200px;
        object-fit: cover;
        border-radius: 5px;
        border: 1px dashed #ced4da;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        background-color: #f8f9fa;
    }
    .group_item{
        padding-top: 5px;
        padding-bottom: 5px;
    }
    .group_item:hover{
        background: #ccc;
        border-radius: 10px;
    }

    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Boom</span>
                    <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Bom Song" type="button"
                        class="btn btn-outline-info btn-import-bom"><i class="fa fa-plus"></i></button>
                    <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Local Song"
                        type="button" class="m-l-5 btn btn-outline-info btn-import-bom2"><i
                            class="fa fa-music"></i></button>
                    <button data-toggle="tooltip" data-placement="top" data-original-title="Export to csv" type="button"
                        class=" m-l-5 btn btn-outline-info btn-export"><i class="fa fa-file-excel-o"></i></button>
                        @if($is_admin_music)
                    <button data-toggle="tooltip" 
                            data-placement="top" 
                            data-original-title="Create Album" 
                            type="button"
                            onclick='albumModal()'
                            class=" m-l-5 btn btn-outline-info">
                        <i class="fas fa-compact-disc"></i>
                    </button>
                        @endif
                </h4>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                    <li class="breadcrumb-item active">Boom</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <form id="formGroupBom" action="/boom">
                    <input type="hidden" name="limit" id="limit" value="{{ $limit }}">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Group <span id="btn_add_bom_group"
                                        onclick="showAddBomGroup()"><i class="fa fa-plus-circle color-red"
                                            style="font-size: 20px;"></i></span></label>
                                <div class="col-12">
                                    <select id="group_bom_filter" class="form-control search_select" name="group_bom_filter"
                                        data-show-subtext="true" data-live-search="true">
                                        {!! $groupBom !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Genre</label>
                                <div class="col-12">
                                    <select class="form-control" name="channel_genre">
                                        {!! $genres !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Song Name</label>
                                <div class="col-12">
                                    <input id="name" class="form-control" type="text" name="name"
                                        value="{{ $request->name }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Artist</label>
                                <div class="col-12">
                                    <input class="form-control" type="text" name="artist"
                                        value="{{ $request->artist }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Vip Status</label>
                                <div class="col-12">
                                    <select class="form-control" name="is_vip">
                                        {!! $vipStatus !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Is Lyric</label>
                                <div class="col-12">
                                    <select class="form-control" name="is_lyric">
                                        {!! $isLyric !!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if ($is_admin_music)
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">User</label>
                                    <div class="col-12">
                                        <select class="form-control search_select" name="c5" data-show-subtext="true" value="{{$request->c5}}"
                                            data-live-search="true">
                                            {!! $list_users !!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-1">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">&nbsp;</label>
                                <div class="col-12">
                                    <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i
                                            class="fa fa-filter"></i> Filter</button>
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
                @if (!isset($request->group_bom_filter))
                    <h4 class="header-title m-t-0 ">LIST BOOM ({{ $datas->total() }}) -
                        PRIORITY({{ $countPriority }})
                    </h4>
                @else
                    <div class="d-flex align-items-center m-b-15">
                        @if ($selected_group != null)
                            <div class="widget-bg-color-icon card-custom widget-user position-relative"
                                style="width: 370px">
                                <div class="text-left">
                                    <div class="editable-container m-b-10 m-r-10">
                                        <span id="groupNameText"
                                            class="color-green font-bold block-word">{{ $selected_group->name }}</span>
                                        <span id="editIcon"><i class="fa fa-pencil"></i></span>
                                        <input type="text" id="groupNameInput" class="w-100"
                                            value="{{ $selected_group->name }}">
                                    </div>

                                    <div class="stats-container mb-3">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="d-flex flex-column align-items-center p-2  stat-box shadow-sm">
                                                    <span class="text-muted small">Total</span>
                                                    <span class="font-weight-bold">{{ count($selected_all) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex flex-column align-items-center p-2  stat-box shadow-sm">
                                                    <span class="text-muted small">VIP</span>
                                                    <span
                                                        class="font-weight-bold text-warning">{{ count($selected_vip) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex flex-column align-items-center p-2  stat-box shadow-sm">
                                                    <span class="text-muted small">Normal</span>
                                                    <span
                                                        class="font-weight-bold text-info">{{ count($selected_all) - count($selected_vip) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="description-container position-relative">
                                        <p id="groupDesText" class="text-muted font-italic small mb-0 w-100 block-word">
                                            {!! $selected_group->description !!}
                                        </p>
                                    </div>
                                </div>
                                <a id="btn-edit-gr" style="right: 30px;bottom: 0px;"
                                    class="cur-poiter position-absolute" data-type="edit_group"
                                    onclick="showEditBomGroup({{ $selected_group->id }}, '{{ base64_encode($selected_group->name) }}', '@if ($selected_group->description != null) {{ base64_encode($selected_group->description) }} @endif')">
                                    <i class="fa fa-pencil"></i></a>
                                <a style="right: 10px;bottom: 0px;" class="btn-priority-song cur-poiter position-absolute"
                                    data-type="delete_group" onclick="javascript void(0)">
                                    <i class="fa fa-trash-o"></i></a>
                                <div class="clearfix"></div>
                            </div>
                        @endif
                    </div>
                @endif
                <div id="action-toolbar" class="disp-none">
                    <div class="row">
                        <div class="col-md-12">
                            <button id="addToGroupBtn" data-mutiple="1"
                                class="btn-group-open btn btn-sm btn-secondary"><i class="fa fa-plus mr-2"></i>Add to Group</button>
                            @if ($selected_group != null)
                                <button id="removeFromGroupBtn" data-mutiple="1"
                                    onclick="deleteFromGroup({{ $selected_group->id }})"
                                    class="btn btn-sm btn-secondary"><i class="fa fa-minus mr-2"></i>Remove from Group</button>
                            @endif
                            <button id="setReleasableBtn" data-mutiple="1" data-type="set_releasable"
                                    class="btn btn-sm btn-info btn-remove-song"><i class="fa fa-check-circle mr-1"></i> Set Releasable</button>
                            <button id="deleteBtn" data-mutiple="1" data-type="delete_song"
                                class="btn btn-sm btn-danger btn-remove-song"> <i class="fa fa-trash mr-1"></i> Delete</button>
                        </div>
                    </div>
                </div>
                <div style="overflow: auto;padding-right: 2px;">
                    <form id="form-boom" style="max-width: 99%">
                        {{ csrf_field() }}
                        <div class="row">
                            <table class="table mobile-table-width table-drag" style="table-layout: fixed;width: 98%">
                                <thead class="thead-default">
                                    <tr align="center">
                                        <th style="width:5%;text-align: right">
                                            <div class="checkbox checkbox-primary tbl-chk">
                                                <input id="select_all" type="checkbox" name="select_all">
                                                <label for="select_all" class="m-b-22 p-l-0"
                                                    style="margin-bottom: 1rem"></label>
                                            </div>
                                        </th>
                                        <th style="width: 6%;text-align: center">@sortablelink('id', 'ID')</th>
                                        <th style="width: 7%;text-align: center">Genre</th>
                                        <th style="width: 7%;text-align: left">Username</th>
                                        <th style="width: 15%;text-align: left">Song Name</th>
                                        <th style="width: 7%;text-align: center">Deezer/Local ID</th>
                                        <th style="width: 10%;text-align: left">Artist</th>
                                        <th style="width: 10%;text-align: left">Album</th>
                                        <th style="width: 10%;text-align: center">Created</th>
                                        <!--<th style="width: 10%;text-align: center">Last used</th>-->
                                        <!--<th style="width: 10%;text-align: center">User use</th>-->
                                        <th style="width: 5%;text-align: center">Count</th>
                                        <th style="width: 20%;text-align: right">{{ trans('label.col.function') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $data)
                                        <tr id="song_{{ $data->id }}" class="odd gradeX" align="center">
                                            <td class="text-right">
                                                <div class="checkbox checkbox-primary tbl-chk">
                                                    <input class="checkbox-multi chkBomAll" type="checkbox"
                                                        name="chkBomAll[]" id="ck-video<?php echo $data->id; ?>"
                                                        value="{{ $data->id }}">
                                                    <label class="m-b-18 p-l-0" for="ck-video<?php echo $data->id; ?>"></label>
                                                </div>
                                            </td>
                                            <td>{{ $data->id }}<br>
                                                @if($data->direct_wav!=null)
                                                    <span class="badge badge-success">.wav</span>
                                                @endif
                                                @if($data->is_releasable==2)
                                                    <span class="badge badge-danger">duplicate</span>
                                                @endif
                                            </td>
                                            <td>{{ $data->genre }}</td>
                                            <td style="text-align: left">{{ $data->username }}</td>
                                            <td style="text-align: left" class="text-ellipsis">
                                                @if ($data->deezer_id != null)
                                                    <a target="_blank"
                                                        href="https://www.deezer.com/us/track/{{ $data->deezer_id }}">{{ $data->song_name }}</a>
                                                @elseif($data->direct_link != null)
                                                    <div class="song-player">
                                                        <audio class="audio-source" src="{{ $data->direct_link }}"
                                                            preload="none" data-song-id="{{ $data->id }}"></audio>
                                                        <button class="btn btn-sm btn-play-toggle"
                                                            data-song-id="{{ $data->id }}">
                                                            <i class="fa fa-play"></i>
                                                        </button>

                                                        <span class="song-name">{{ $data->song_name }}</span>
                                                    </div>
                                                @else
                                                    {{ $data->song_name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->deezer_id != null)
                                                    {{ $data->deezer_id }}
                                                @else
                                                    {{ $data->local_id }}
                                                @endif
                                            </td>
                                            <td style="text-align: left" class="text-ellipsis">{{ $data->artist }}</td>
                                            <td style="text-align: left" class="text-ellipsis">
                                                @if($data->album_cover!=null)
                                                <img class="rounded-circle img-cover" src="{{$data->album_cover}}" width="40" height="40">
                                                @endif
                                                {{ $data->album_name }}
                                            </td>
                                            <td>
                                                {!! nl2br(e(str_replace(' ', "\n", $data->created))) !!}
                                               
                                            </td>
                                            <!--<td>{{ $data->last_used }}</td>-->
                                            <!--<td>{{ $data->user_used }}</td>-->
                                            <td>{{ $data->count }}</td>

                                            <td style="text-align: right">
                                                <span data-toggle='tooltip' data-placement='top'
                                                    data-original-title='Add this song to multiple groups'><button
                                                        type="button" class="color-green btn btn-group-open"
                                                        data-mutiple="0" data-boom-id="{{ $data->id }}"><i
                                                            class="fa fa-list-alt"></i></button></span>
                                                @if ($data->sync == 0)
                                                    <span data-toggle='tooltip' data-placement='top'
                                                        data-original-title='Sync this song to system'><button
                                                            type="button" class="color-green btn btn-sync-song"
                                                            data-boom-id="{{ $data->id }}"><i
                                                                class="ion-android-download"></i></button></span>
                                                @endif
                                                @if ($data->lyric == 0)
                                                    <span data-toggle='tooltip' data-placement='top'
                                                        data-original-title='Make lyric for this song'><button
                                                            type="button" class="color-red btn btn-make-lyric"
                                                            data-boom-id="{{ $data->id }}"
                                                            {{ $data->lyric_disable }}><i
                                                                class="ion-music-note"></i></button></span>
                                                @elseif($data->lyric == 2)
                                                    <span data-toggle='tooltip' data-placement='top'
                                                        data-original-title='Finished make lyric'><button type="button"
                                                            class="color-t btn btn-finish-make-lyric"
                                                            data-boom-id="{{ $data->id }}"><i
                                                                class="ion-music-note"></i></button></span>
                                                @endif
                                                @if (!isset($request->group_bom_filter))
                                                    <span data-toggle='tooltip' data-placement='top'
                                                        data-original-title='Set/Unset vip for Genre'>
                                                        <button type="button"
                                                            class="<?php echo $data->priority == 1 ? 'color-red ' : ''; ?>btn btn-priority-song"
                                                            data-boom-id="{{ $data->id }}"
                                                            data-type="change_vip_genre"><i
                                                                class="ion-star"></i></button></span>
                                                @elseif($selected_group != null)
                                                    <span data-toggle='tooltip' data-placement='top'
                                                        data-original-title='Set/Unset vip for Group {{ $selected_group->name }}'>
                                                        <button type="button"
                                                            class="<?php echo $data->is_vip_group == 1 ? 'color-red ' : ''; ?>btn btn-priority-song"
                                                            data-boom-id="{{ $data->id }}"
                                                            data-type="change_vip_group"><i
                                                                class="ion-star"></i></button></span>
                                                @endif
                                                <span data-toggle='tooltip' data-placement='top'
                                                    data-original-title='Make video for this song'><button type="button"
                                                        class="color-green btn btn-show-hub"
                                                        data-music-type="<?php echo $data->deezer_id != null ? 'deezer' : 'local'; ?>"
                                                        data-music-id="<?php echo $data->deezer_id != null ? $data->deezer_id : $data->local_id; ?>"
                                                        data-songname="{{ $data->song_name }}"
                                                        {{ $data->cross_disable }}><i
                                                            class="ion-videocamera"></i></button></span>
                                                <span data-toggle='tooltip' data-placement='top'
                                                    data-original-title='Edit this song'><button type="button"
                                                        class="btn btn-edit-song" data-boom-id="{{ $data->id }}"><i
                                                            class="ion-edit"></i></button></span>
                                                <span class="dropdown">
                                                    <button class="btn dropdown-toggle" type="button"
                                                        id="dropdownMenuButton" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="ion-navicon"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        @if ($selected_group != null)
                                                            <a class="dropdown-item btn-priority-song cur-poiter"
                                                                data-boom-id="{{ $data->id }}"
                                                                data-type="remove_from_group"
                                                                onclick="javascript:void(0)"><i class="fa fa-times"></i>
                                                                Remove From Group</a>
                                                        @endif
                                                         <a class="dropdown-item btn-remove-song cur-poiter"
                                                            data-boom-id="{{ $data->id }}" data-type="set_releasable"
                                                            data-mutiple="0" onclick="javascript:void(0)"><i
                                                                class="fa fa-check-circle"></i>
                                                            Set Releasable</a>
                                                        <a class="dropdown-item btn-remove-song cur-poiter"
                                                            data-boom-id="{{ $data->id }}" data-type="delete_song"
                                                            data-mutiple="0" onclick="javascript:void(0)"><i
                                                                class="ion-trash-a"></i>
                                                            Delete Song</a>
                                                    </div>
                                                </span>
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
                                    <select id="cbbLimit" name="limit" aria-controls="tbl-title"
                                        class="form-control input-sm">
                                        {!! $limitSelectbox !!}
                                    </select>&nbsp;
                                    <?php if (isset($datas)) { ?>
                                    {!! $datas->links() !!}
                                    <?php } ?>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="fixed-player" class="fixed-bottom bg-purple text-white  d-none">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="song-thumb" style="padding: 5px 0px;">
                            <img id="current-song-thumb" src="/images/logo1.png" alt="Song thumbnail" class="rounded"
                                height="70">
                        </div>
                        <div>
                            <div id="current-song-name" class="font-weight-bold text-truncate"></div>
                            <div id="current-artist" class="small text-truncate"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="d-flex justify-content-between small mt-2">
                        <span id="current-time">0:00</span>
                        <span id="duration">0:00</span>
                    </div>
                    <div class="progress" style="height: 10px; cursor: pointer;">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="volume-control d-flex align-items-center justify-content-end gap-2">
                        <button id="player-volume" class="btn btn-link text-white">
                            <i class="fa fa-volume-up"></i>
                        </button>
                        <div class="volume-slider-container"
                            style="width: 100px;display: flex;align-items: center;height: 100%;">
                            <input type="range" class="volume-slider" min="0" max="100" value="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dialog.bom.importnoclaim')
    @include('dialog.bom.importbom')
    @include('dialog.bom.boomupload')
    @include('dialog.bom.bomgrouplist')
    @include('dialog.bom.bomgroupadd')
    @include('dialog.bom.add_album')
@endsection

@section('script')
    <script type="text/javascript">
                    // Handle album cover preview click
            $('#imagePreview').click(function() {
                $('#albumCover').click();
            });
            
            // Handle file selection for album cover
            $('#albumCover').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').html(`
                            <img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
                        `);
                    };
                    reader.readAsDataURL(file);
                }
            });
        function albumModal(){
            if ($('#dialog_album_add').is(':visible')) {
                console.log('Modal is visible');
            } else {
                console.log('Modal is hidden');
                $('#dialog_album_add').modal("show");
            }
        }
        
            $('#resetBtn').click(function() {
                $('#createAlbumForm')[0].reset();
                $('#imagePreview').html('<i class="fas fa-compact-disc fa-3x music-icon"></i>');
                $('.custom-file-label').html('Choose image');
                $('#successMessage').hide();
                
                // Remove all tracks except the first one
                $('#tracksContainer .track-item:not(:first)').remove();
                
                // Reset first track
                const firstTrack = $('#tracksContainer .track-item:first');
                firstTrack.find('.track-title').val('');
                firstTrack.find('.track-artist').val('');
                firstTrack.find('.track-duration').val('');
                firstTrack.find('.track-file').val('');
                firstTrack.find('.custom-file-label').html('Choose audio file');
                firstTrack.find('.remove-track-btn').hide();
            });
            
            // Submit form
            $('#submitAlbum').on('click', function(e) {
                e.preventDefault();
//                var form = $("#form-album").serialize();
//                const albumCover = $('#albumCover')[0].files[0];
  
                // Validate data
                const albumTitle = $('#albumTitle').val();
                const albumArtist = $('#albumArtist').val();
                const albumGenre = $('#albumGenre').val();
                const albumCover = $('#albumCover')[0].files[0];
                
                if (!albumTitle || !albumArtist || !albumGenre || !albumCover) {
                    alert('Please fill in all required album information!');
                    return;
                }
                
//                // Validate tracks
//                let isValid = true;
//                const tracks = [];
//                
//                $('#tracksContainer .track-item').each(function() {
//                    const trackTitle = $(this).find('.track-title').val();
//                    if (!trackTitle) {
//                        alert('Please enter a title for all tracks!');
//                        isValid = false;
//                        return false;
//                    }
//                    
//                    const trackData = {
//                        title: trackTitle,
//                        artist: $(this).find('.track-artist').val(),
//                        duration: $(this).find('.track-duration').val(),
//                        file: $(this).find('.track-file')[0].files[0]
//                    };
//                    
//                    tracks.push(trackData);
//                });
//                
//                if (!isValid) return;
                
                // Show loading spinner
                $('#loadingSpinner').show();
                $('#submitIcon').hide();
                $('#submitBtn').attr('disabled', true);
                
                // Prepare data
                const formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                formData.append('title', albumTitle);
                formData.append('artist', albumArtist);
//                formData.append('description', $('#albumDescription').val());
                formData.append('genre', albumGenre);
                formData.append('releaseDate', $('#releaseDate').val());
//                formData.append('recordLabel', $('#recordLabel').val());
//                formData.append('tags', $('#albumTags').val());
//                formData.append('privacy', $('input[name="privacy"]:checked').val());
                formData.append('albumCover', albumCover);
                
//              $.ajax({
//                type: "POST",
//                url: "/addAlbum",
//                data: formData,
//                dataType: 'json',
//                success: function(data) {
//                    $.Notification.autoHideNotify(data.status, 'top right', 'Notify', data.message);
//                },
//                error: function(data) {
//
//                }
//            });            
                
                
                
//                // Add track information
//                tracks.forEach((track, index) => {
//                    formData.append(`tracks[${index}][title]`, track.title);
//                    formData.append(`tracks[${index}][artist]`, track.artist || albumArtist);
//                    formData.append(`tracks[${index}][duration]`, track.duration);
//                    if (track.file) {
//                        formData.append(`tracks[${index}][audioFile]`, track.file);
//                    }
//                });
                
//                // Call API to create album
//                // Change URL to match your API
                fetch('/addAlbum', {
                    method: 'POST',
                    body: formData,
                    // If authentication is needed
                    headers: {
                        'Authorization': 'Bearer YOUR_ACCESS_TOKEN'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error creating music album');
                    }
                    return response.json();
                })
                .then(data => {
                    // Show success message
                    $('#successMessage').show();
                    
                    // Scroll to top
                    window.scrollTo({top: 0, behavior: 'smooth'});
                    
                    console.log('Album created successfully:', data);
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                    console.error('Error:', error);
                })
                .finally(() => {
                    // Hide loading spinner
                    $('#loadingSpinner').hide();
                    $('#submitIcon').show();
                    $('#submitBtn').attr('disabled', false);
                });
            });        
        
        
        
        
        
        @if ($selected_group != null)
            $("#editIcon").on("click", function() {
                $("#groupNameText, #editIcon").hide();
//                $("#groupNameInput").show().focus();
                let input = $("#groupNameInput").show().focus();

                // Đưa con trỏ xuống cuối nội dung
                let value = input.val();
                input[0].setSelectionRange(value.length, value.length);
            });

            $("#groupNameInput").on("blur", function() {
                let newName = $(this).val().trim();
                let groupId = {{ $selected_group->id }};

                if (newName === "") {
                    $.Notification.autoHideNotify("error", 'top right', 'Notify', "");
                    return;
                }

                $.ajax({
                    url: "/updateGroup",
                    method: "POST",
                    data: {
                        id: groupId,
                        group_name: newName,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        $("#groupNameText").text(newName).show();
                        $("#editIcon").show();
                        $("#groupNameInput").hide();
                        if (data.status == "success") {
                            $("#btn-edit-gr").attr("onclick",
                                `showEditBomGroup(${data.data.id},'${btoa(data.data.name)}','${btoa(data.data.description)}')`
                                );
                        }
                    },
                    error: function() {}
                });
            });
        @endif

        $("#group_bom_filter").change(function() {
            $("#formGroupBom").submit();
        });

        $('#deezerId').on('input', function() {
            const text = $(this).val();
            const lines = text.split('\n').length;
            $('#dz_count').text(`(${lines})`);
        });

        $(".btn-check-spotify").click(function() {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }

            $("#error_song").empty();
            var spId = $("#spotify_playlist_id").val();
            $.ajax({
                type: "GET",
                url: "/getDeezerFromSpotify",
                data: {
                    sp_id: spId
                },
                dataType: 'json',
                success: function(data) {
                    logger('getDeezerFromSpotify', data);
                    $this.html($this.data('original-text'));
                    const textContent = data.result.join('\n');
                    // Gán chuỗi này vào textarea sử dụng jQuery
                    $('#deezerId').val(textContent);
                    $('#deezerId').trigger('input');
                    genErrorSpotify("#error_song", data.error);
                },
                error: function(data) {

                }
            });
        });


        let globalAudio = new Audio();
        let currentSongId = null;
        let isPlaying = false;

        $('.song-player button').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $button = $(this);
            const $audioElement = $button.siblings('audio');
            const songId = $audioElement.data('song-id');
            const songUrl = $audioElement.attr('src');
            const songName = $button.siblings('.song-name').text();
            const artist = $button.closest('tr').find('td:eq(6)').text();

            // Nếu click vào bài đang phát
            if (songId === currentSongId) {
                togglePlay($button);
                return;
            }

            // Reset trạng thái các nút play khác
            $('.song-player button').find('i')
                .removeClass('fa-pause')
                .addClass('fa-play');

            // Phát bài mới
            playSong({
                id: songId,
                url: songUrl,
                name: songName,
                artist: artist,
                button: $button
            });
        });

        // Xử lý thanh progress
        $('#progress-bar').parent().click(function(e) {
            const percent = e.offsetX / $(this).width();
            const newTime = percent * globalAudio.duration;
            globalAudio.currentTime = newTime;
        });

        $('.volume-slider').on('input', function() {
            const volumeValue = parseInt($(this).val());
            globalAudio.volume = volumeValue / 100;
        });

        $('#player-volume').click(function() {
            const $icon = $(this).find('i');
            const $slider = $('.volume-slider');
            if (globalAudio.volume > 0) {
                $slider.data('lastVolume', $slider.val());
                $slider.val(0);
                $icon.removeClass('fa-volume-up').addClass('fa-volume-off');
            } else {
                $slider.val($slider.data('lastVolume') || 100);
                $icon.removeClass('fa-volume-mute').addClass('fa-volume-up');
            }
            $slider.trigger('input');
            globalAudio.volume = $slider.val() / 100;
        });

        // Cập nhật progress bar
        globalAudio.addEventListener('timeupdate', function() {
            const percent = (globalAudio.currentTime / globalAudio.duration) * 100;
            $('#progress-bar').css('width', percent + '%');
            $('#current-time').text(formatTime(globalAudio.currentTime));
            $('#duration').text(formatTime(globalAudio.duration));
        });

        // Xử lý khi audio kết thúc
        globalAudio.addEventListener('ended', function() {
            const $currentButton = $(`.song-player button[data-song-id="${currentSongId}"]`);
            $currentButton.find('i')
                .removeClass('fa-pause')
                .addClass('fa-play');
            isPlaying = false;
            currentSongId = null;
        });


        // Hàm phát nhạc
        function playSong(song) {
            // Cập nhật audio source
            globalAudio.src = song.url;
            globalAudio.play();

            // Cập nhật trạng thái
            isPlaying = true;
            currentSongId = song.id;

            // Cập nhật UI của nút
            song.button.find('i')
                .removeClass('fa-play')
                .addClass('fa-pause');

            // Hiển thị và cập nhật thanh player
            $('#fixed-player').removeClass('d-none');
            $('#current-song-name').text(song.name);
            $('#current-artist').text(song.artist);
        }

        // Hàm toggle play/pause
        function togglePlay($button) {
            if (isPlaying) {
                globalAudio.pause();
                $button.find('i')
                    .removeClass('fa-pause')
                    .addClass('fa-play');
                isPlaying = false;
            } else {
                globalAudio.play();
                $button.find('i')
                    .removeClass('fa-play')
                    .addClass('fa-pause');
                isPlaying = true;
            }
        }

        // Hàm format thời gian
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            seconds = Math.floor(seconds % 60);
            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        function showCustomName($this) {
            if ($this.checked) {
                $(".div_2_song").show();
            } else {
                $(".div_2_song").hide();
            }
        }

        function showOverTone($this) {
            if ($this.checked) {
                $(".div_overtone").show();
                $(".div_other").hide();
            } else {
                $(".div_overtone").hide();
                $(".div_other").show();
            }
        }

        function showAddLocal($this) {
            if ($this.checked) {
                $(".div_add_local").show();
                $(".div_add_deezer").hide();
            } else {
                $(".div_add_local").hide();
                $(".div_add_deezer").show();
            }
        }

        function sourceChange($this) {
            if ($this.value.includes("youtu") || $this.value.includes("drive.google.com/file")) {
                $(".div_1_song").show();
                $(".div_check_udio").hide();
                $(".div_2_song").hide();
            } else if ($this.value.includes("udio.com") || $this.value.includes("suno.com")) {
                $(".div_1_song").hide();
                $(".div_check_udio").show();
                if ($('#chk_keep_name').prop('checked')) {
                    $(".div_2_song").show();
                } else {
                    $(".div_2_song").hide();
                }

            } else if ($this.value.includes("drive.google.com/drive/folders/")) {
                $(".div_1_song").hide();
                $(".div_2_song").show();
                $(".div_check_udio").hide();
            }else {
                $(".div_1_song").show();
                $(".div_check_udio").hide();
                $(".div_2_song").hide();
            }
        }

        function showAddBomGroup() {
            $("#bom_gr_id").val(null);
            $("#group_name").val("");
            $("#group_des").val("");
            $("#dialog_bom_group_add").modal('show');
        }

        function showEditBomGroup(id, name, des) {
            $("#bom_gr_id").val(id);
            $("#group_name").val(atob(name));
            $("#group_des").val(atob(des));
            $("#dialog_bom_group_add").modal('show');
        }

        $('.checkbox-multi').change(function() {
            // Kiểm tra xem có checkbox nào được chọn hay không
            if ($('.checkbox-multi:checked').length > 0) {
                // Hiển thị thanh công cụ nếu có checkbox được chọn
                $('#action-toolbar').show();
            } else {
                // Ẩn thanh công cụ nếu không có checkbox nào được chọn
                $('#action-toolbar').hide();
            }
        });

        // Theo dõi checkbox "Select All"
        $('#select_all').change(function() {
            $('.checkbox-multi').prop('checked', this.checked).trigger('change');
        });

        //2023/04/28 add to group
        function showAddGroupForm() {
            $(".add_new_group_list").show();
            $(".btn-show-add-group-form").hide();
        }

        function hideAddGroupForm() {
            $(".add_new_group_list").hide();
            $(".btn-show-add-group-form").show();
            $("#group_name").val("");
            $("#group_des").val("");

        }

        function addToGroup(id) {
            $("#group_name").val("");
            var addType = $("#add_type").val();
            var status = 0;
            if ($("#g_" + id).is(":checked")) {
                status = 1;
            }
            var input;
            if (addType == 0) {
                input = "group_id=" + id + "&boom_add_id=" + $("#boom_add_id").val() + "&status=" + status + "&type=" +
                    addType;
            } else {
                input = $("#form-boom").serialize() + "&type=" + addType + "&group_id=" + id;
            }
            $.ajax({
                type: "GET",
                url: "/addBomToGroup",
                data: input,
                dataType: 'json',
                success: function(data) {
                    $.Notification.autoHideNotify(data.status, 'top right', 'Notify', data.message);
                },
                error: function(data) {

                }
            });
        }

        function deleteFromGroup(id) {
            var input = $("#form-boom").serialize() + "&type=1&group_id=" + id + "&is_remove=1";
            $.ajax({
                type: "GET",
                url: "/addBomToGroup",
                data: input,
                dataType: 'json',
                success: function(data) {
                    $.Notification.autoHideNotify(data.status, 'top right', 'Notify', data.message);
                    location.reload();
                },
                error: function(data) {

                }
            });
        }

        function addPriority(id) {
            $("#group_name").val("");
            var addType = $("#add_type").val();
            var bomId = $("#boom_add_id").val();

            var input;
            if (addType == 0) {
                input = `group_id=${id}&type=${addType}&boom_add_id=${bomId}`;
            } else {
                input = $("#form-boom").serialize() + "&type=" + addType + "&group_id=" + id;
            }
            $.ajax({
                type: "GET",
                url: "/addPriority",
                data: input,
                dataType: 'json',
                success: function(data) {
                    $.Notification.autoHideNotify(data.status, 'top right', 'Notify', data.message);
                    //                    if (data.status == 'error') {
                    //                        $("#p_" + id).prop('checked', false);
                    //                    }
                    if (data.is_vip) {
                        $("#p_" + id).addClass('color-red');
                    } else {
                        $("#p_" + id).removeClass('color-red');
                    }

                },
                error: function(data) {

                }
            });
        }

        function addGroup() {
            var form = $("#formaddgroup").serialize();
            var groupName = $("#group_name").val();
            $.ajax({
                type: "GET",
                url: "/addNewGroup",
                data: form,
                dataType: 'json',
                success: function(data) {
                    $.Notification.autoHideNotify(data.status, 'top right', 'Notify', data.message);
                    groupList($("#boom_add_id").val(), 0);
                    if (data.status == "success") {
                        $("#group_name").val("");
                        $("#group_des").val("");
                        $("#dialog_bom_group_add").modal("hide");
                        if(data.is_edit==1){
                            $("#groupNameText").html(data.data.name);
                            $("#groupNameInput").val(data.data.name);
                            $("#groupDesText").html(data.data.description);
                            $("#btn-edit-gr").attr("onclick",
                                `showEditBomGroup(${data.data.id},'${btoa(data.data.name)}','${btoa(data.data.description)}')`
                                );
                        }
                    }
                },
                error: function(data) {

                }
            });
        }

        function groupList(boomId, dialog = 1, isSelect = 0) {
            $.ajax({
                type: "GET",
                url: "/groups/list",
                data: {

                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    var html = ``;
                    $.each(data, function(k, v) {

                        var listSong = v.list_song != null ? JSON.parse(v.list_song) : [];
                        var listPriority = v.list_priority != null ? JSON.parse(v.list_priority) : [];
                        var checked = "";
                        var checked2 = "";
                        if (listSong.includes(parseInt(boomId))) {
                            checked = "checked";
                        }
                        if (listPriority.includes(parseInt(boomId))) {
                            checked2 = "color-red";
                        }
                        html += `<div class="row d-flex align-items-center group_item"><div class="col-md-10"">
                           <div class="checkbox-radius checkbox-circle d-flex">
                               <input id="g_${v.id}" class="g_${v.id}" type="checkbox" onchange="addToGroup(${v.id})" ${checked}>
                               <label for="g_${v.id}" class="m-b-18 m-l-15"></label>
                               <span>${v.name}<span>    
                           </div></div>`;

                        var addType = $("#add_type").val();
                        if (addType == 0) {
                            html +=
                                `<div class="col-md-2"><i id="p_${v.id}" onclick="addPriority(${v.id})" class="ion-star font-30 ${checked2}" data-toggle="tooltip" data-placement="top" data-original-title="Set as vip"></i></div>`;
                        }
                        html += `</div>`;

                    });
                    $(".group_list_content").html(html);

                    //gen html cho selectbox
                    var option = "";
                    $.each(data, function(k, v) {
                        option +=
                            `<option value='${v.id}'  data-content='${v.name} <span class="font-12 text-muted font-italic"> ${v.list_song != null ? JSON.parse(v.list_song).length : 0} tracks</span>'></option>`;
                    });
                    $("select.local_group").empty();
                    $("select.local_group").html(option);
                    $('select.local_group').selectpicker('refresh');


                    if (dialog == 1) {
                        $('#dialog_bom_group_list').modal({
                            backdrop: true
                        });
                    }
                    $('[data-toggle="tooltip"]').tooltip();

                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        }

        $(".btn-group-open").click(function() {
            var boomId = $(this).attr("data-boom-id");
            var addType = $(this).attr("data-mutiple");
            $("#boom_add_id").val(boomId);
            $("#add_type").val(addType);
            groupList(boomId);
        });

        $(".btn-export").click(function() {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var url = "/exportBoom";
            window.location.href = url;
            $this.html($this.data('original-text'));


        });

        $(".btn-finish-make-lyric").click(function(e) {
            e.preventDefault();
            var boomId = $(this).attr("data-boom-id");
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }

            $.ajax({
                type: "GET",
                url: "boomFinishMakeLyric",
                data: {
                    "boom_id": boomId
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    if (data.status === "success") {
                        setTimeout(location.reload(), 5000);
                    }

                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        });

        $(".btn-make-lyric").click(function(e) {
            e.preventDefault();
            var boomId = $(this).attr("data-boom-id");
            $.ajax({
                type: "GET",
                url: "boomMakeLyric",
                data: {
                    "boom_id": boomId
                },
                dataType: 'json',
                success: function(data) {

                    if (data.status == "success") {
                        if (data.type == 'deezer') {
                            window.open('http://lyric.automusic.win?track_id=' + data.id + '&url=' +
                                data.url + '&username=' + data.username + '&type=noclaim&deezer_art_id=-1&cam_id='+
                                data.cam_id, '_blank');
                        } else {
                            window.open('http://lyric.automusic.win?audio_url=' + data.url +
                                '&username=' + data.username + '&artist=' + data.artist +
                                '&title=' + data.title +
                                '&lyric=' + data.lyric + '&type=noclaim&deezer_art_id=-1&cam_id=' +
                                data.id, '_blank'
                            );
                        }
                        
                            window.open(`http://lyric.automusic.win?audio_url=${data.url}
                                &username=${data.username}
                                &artist=${data.artist}
                                &title=${data.title}
                                &lyric=${data.lyric}
                                &type=noclaim
                                &deezer_art_id=-1
                                &cam_id=${data.id}, _blank`
                            );
                        location.reload();
                    }

                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        });

        function activeMakeLyric($this, boomId) {

            console.log($this);
            $.ajax({
                type: "GET",
                url: "boomMakeLyric",
                data: {
                    "boom_id": boomId
                },
                dataType: 'json',
                success: function(data) {

                    if (data.status == "success") {
                        if (data.type == 'deezer') {
                            window.open('http://lyric.automusic.win?track_id=' + data.id + '&url=' +
                                data.url + '&username=' + data.username + '', '_blank');
                        } else {
                            window.open('http://lyric.automusic.win?audio_url=' + data.url +
                                '&username=' + data.username + '&artist=' + data.artist +
                                '&title=' + data.title +
                                '&lyric=' + data.lyric + '&type=noclaim&deezer_art_id=-1&cam_id=' + data.id,
                                '_blank'
                            );
                        }

                    }

                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        }

        $(".btn-remove-song").click(function(e) {
            e.preventDefault();
            var type = $(this).attr("data-type");
            var mutiple = $(this).attr("data-mutiple");
            var boomId = $(this).attr("data-boom-id");
            if (mutiple == 1) {
                var chkBomAllArray = $("input[name='chkBomAll[]']:checked").map(function() {
                    return $(this).val();
                }).get();
                //            var input = $("#form-boom").serialize() + `&type=${type}&mutiple=${mutiple}`;
                var input = {
                    "bom_array": chkBomAllArray,
                    'type': type,
                    'mutiple': mutiple
                };
            } else {
                var input = {
                    "boom_id": boomId,
                    'type': type,
                    'mutiple': mutiple
                };

            }
            console.log(input);
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';

            $.confirm({
                animation: 'rotateXR',
                title: 'Confirm!',
                content: 'Are you sure you want to proceed?',
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajax({
                                type: "GET",
                                url: "boomRemove",
                                data: input,
                                dataType: 'json',
                                success: function(data) {
                                    $this.html($this.data('original-text'));
                                    $.Notification.notify(data.status, 'top center', '',
                                        data.message);
                                    if (data.status == "success") {
                                        if(type=='delete_song'){
                                            if (mutiple == '0') {
                                                $this.closest("tr").hide();
                                            } else {
                                                chkBomAllArray.forEach(function(id) {
                                                    $(`#song_${id}`).hide();
                                                });
                                            }
                                        }
                                    }

                                },
                                error: function(data) {
                                    $this.html($this.data('original-text'));
                                    console.log('Error:', data);

                                }
                            });
                        }
                    },
                    cancel: function() {

                    }

                }
            });
        });

        $(".btn-priority-song").click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var boomId = $(this).attr("data-boom-id");
            var type = $(this).attr("data-type");
            var groupId = $("#group_bom_filter").val();
            $.confirm({
                animation: 'rotateXR',
                title: 'Confirm!',
                content: 'Are you sure about this action?',
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-red',
                        action: function () {
                            $.ajax({
                                type: "GET",
                                url: "boomRemove",
                                data: {
                                    "boom_id": boomId,
                                    "type": type,
                                    "group_id": groupId
                                },
                                dataType: 'json',
                                success: function(data) {
                                    $this.html($this.data('original-text'));
                                    $.Notification.notify(data.status, 'top center', '', data.message);
                                    if (data.status == "success") {
                                        if (data.is_vip === 1) {
                                            $this.addClass("color-red");
                                        }
                                        if (data.is_vip === 0) {
                                            $this.removeClass("color-red");
                                        }
                                        if (data.is_reload == 1) {
                                            location.href = "/boom";
                                        }
                                    }

                                },
                                error: function(data) {
                                    $this.html($this.data('original-text'));
                                    console.log('Error:', data);

                                }
                            });
                        }
                    },
                    cancel: function () {

                    }

                }
            });
            


        });

        $(".btn-sync-song").click(function(e) {
            e.preventDefault();
            var boomId = $(this).attr("data-boom-id");
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }

            $.ajax({
                type: "GET",
                url: "boomSync",
                data: {
                    "boom_id": boomId
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    setTimeout(location.reload(), 3000);

                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                    console.log('Error:', data);

                }
            });
        });

        $(".btn-import-bom").click(function(e) {
            e.preventDefault();
            clearForm();
            $(".div_group_show").show();
            groupList($("#boom_add_id").val(), 0);
            $('#dialog_import_bom').modal({
                backdrop: false
            });
        });


        $("#overtone_playlist_id").wrap('<div class="select-container"></div>');
        $(".select-container").append('<div class="skeleton-loading"></div>');

        function toggleSelectSkeletonLoading(show) {
            const container = $("#overtone_playlist_id").closest('.select-container');
            if (show) {
                container.addClass('loading');
            } else {
                container.removeClass('loading');
            }
        }

        $(".btn-import-bom2").click(function(e) {
            e.preventDefault();
            clearForm2();
            groupList(0, 0, 1);
            toggleSelectSkeletonLoading(true);
            $(".select-container .search_select").hide();
            $.ajax({
                type: "GET",
                url: "/getOvertonePlaylist",
                //            url: "/getOverTonePlaylistId",
                data: {},
                dataType: 'text',
                success: function(data) {
                    $('#overtone_playlist_id').html(data);
                    $('#overtone_playlist_id').selectpicker('destroy');
                    $('#overtone_playlist_id').selectpicker('render');
                    toggleSelectSkeletonLoading(false);
                    $(".select-container .search_select").show();
                },
                error: function(data) {
                    console.log('Error:', data);
                    toggleSelectSkeletonLoading(false);


                }
            });
            $('#dialog_import_noclaim').modal({
                backdrop: false
            });
        });

        $(".btn-edit-song").click(function(e) {
            e.preventDefault();
            var id = $(this).attr("data-boom-id");
            $(".div_group_show").hide();
            $.ajax({
                type: "GET",
                url: "boom/" + id,
                data: {},
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.status === 'error') {
                        $.Notification.notify(data.status, 'top center', '', data.message);
                    } else {
                        $("#songName").val(data.song_name);
                        $("#deezerId").val(data.deezer_id);
                        $("#artist").val(data.artist);
                        $("#deezerArtistId").val(data.deezer_artist_id);
                        $("#bom_id").val(data.id);
                        $("#social").val(data.social);
                        $("#localId").val(data.local_id);
                        $("#genre").val(data.genre).change();
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
            $('#dialog_import_bom').modal({
                backdrop: false
            });
        });

        function clearForm() {
            $("#songName").val("");
            $("#deezerId").val("");
            $("#artist").val("");
            $("#deezerArtistId").val("");
            $("#bom_id").val("");
            $("#social").val("");
            $("#localId").val("");
            $("#genre").val("-1").change();
        }

        function clearForm2() {
            $("#local_songName").val("");
            $("#local_source_link").val("");
            $("#local_artist").val("");
            $("#noclaim_id").val("");
            $("#local_genre").val("-1").change();
            $("#local_group").val("");
        }

        $(".btn-save-bom").click(function(e) {
            $("#import_result_deezer").empty();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var form = $("#frmBom");
            var formData = form.serialize();
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "boomStore",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    //                if (data.status == "success") {
                    //                    setTimeout(location.reload(), 3000);
                    //                }
                    genResultImportDeezer("#import_result_deezer", data.results);
                },
                error: function(data) {
                    console.log('Error:', data);
                    $this.html($this.data('original-text'));

                }
            });
        });

        $(".btn-save-local-song").click(function(e) {
            $("#import_result").empty();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var form = $("#frmLocalSong");
            var formData = form.serialize();
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "noclaimStore",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    genResultImport("#import_result", data.playlist);

                },
                error: function(data) {
                    console.log('Error:', data);
                    $this.html($this.data('original-text'));

                }
            });
        });

        function genResultImport(element, datas) {
            var $table = $('<table></table>').addClass(
                'table mobile-table-width table-drag table-copy').css({
                "width": "99%",
                "table-layout": "fixed"
            });
            var $thead = $('<thead></thead>');
            var $theadRow = $('<tr></tr>');
            $theadRow.append($('<th class="text-center w-7p" ></th>').text('Index'));
            $theadRow.append($('<th class="text-center  w-7p"></th>').text('BomId'));
            $theadRow.append($('<th class="text-center w-10p" ></th>').text('User'));
            $theadRow.append($('<th class="text-left w-10p"></th>').text('Genre'));
            $theadRow.append($('<th class="text-left w-10p"></th>').text('Artist'));
            $theadRow.append($('<th class="text-Song w-20p"></th>').text('Song'));
            $theadRow.append($('<th class="text-right w-10p"></th>').text('Status'));
            $thead.append($theadRow);
            $table.append($thead);
            var $tbody = $('<tbody></tbody>');
            $.each(datas, function(index, song) {
                var status = `<span class="badge badge-success">Success</span>`;
                if (song.status == 2) {
                    status = `<span class="badge badge-warning">Exists</span>`;
                }
                $tbody.append(
                    `<tr class="cur-poiter">' +
            <td class="text-center">${(index + 1)}</td>
            <td class="text-center">${(song.id)}</td>
            <td class="text-center">
                <div data-toggle="tooltip" data-placement="top" title="" data-original-title="${song.user}">
                    <img class="rounded-circle img-cover" src="/images/avatar/${song.user}.jpg" height="40" width="40">
                </div>
            </td>
            <td class="text-left text-ellipsis">${song.genre}</td>
            <td class="text-left text-ellipsis" style="width: 20%;">${song.artist}</td>
            <td class="text-left text-ellipsis">${song.song_name}</td>
            <td class="text-right">${status}</td>
            </tr>`
                );
            });
            $table.append($tbody);
            $(`${element}`).append($table);
            activeTableCopy();
        }

        function genResultImportDeezer(element, datas) {

            var $table = $('<table></table>').addClass(
                'table mobile-table-width table-drag table-copy').css({
                "width": "99%",
                "table-layout": "fixed"
            });
            var $thead = $('<thead></thead>');
            var $theadRow = $('<tr></tr>');
            $theadRow.append($('<th class="text-center w-7p" ></th>').text('Index'));
            $theadRow.append($('<th class="text-center  w-10p"></th>').text('Deezer'));
            $theadRow.append($('<th class="text-left w-20p"></th>').text('Artist'));
            $theadRow.append($('<th class="text-Song w-20p"></th>').text('Song'));
            $theadRow.append($('<th class="text-right w-20p"></th>').text('Status'));
            $thead.append($theadRow);
            $table.append($thead);
            var $tbody = $('<tbody></tbody>');
            $.each(datas, function(index, song) {
                var status = `<span class="badge badge-success">Success</span>`;
                if (song.status == 2) {
                    status = `<span class="badge badge-warning">Exists</span>`;
                } else if (song.status == 3) {
                    status = `<span class="badge badge-danger">Error</span>`;
                }
                var makeLyric = "";
                if (song.is_lyric == 0) {
                    makeLyric =
                        `<span data-toggle='tooltip' data-placement='top'
                                              data-original-title='Make lyric for this song'><button
                                                type="button" class="color-red btn btn-make-lyric" onclick="activeMakeLyric(this,${song.bom_id})"
                                                data-boom-id="${song.bom_id}"><i class="ion-music-note"></i></button></span>`;
                }
                $tbody.append(
                    `<tr class="cur-poiter">' +
            <td class="text-center">${(index + 1)}</td>
            <td class="text-center">${(song.deezer)}</td>
            <td class="text-left text-ellipsis" style="width: 20%;">${song.artist}</td>
            <td class="text-left text-ellipsis">${song.song_name} </td>
            <td class="text-right">${makeLyric} ${status}</td>
            </tr>`
                );
            });
            $table.append($tbody);
            $(`${element}`).append($table);
            $('[data-toggle="tooltip"]').tooltip();
            //        activeTableCopy();
        }

        function genErrorSpotify(element, datas) {
            if (datas.length === 0) {
                return;
            }
            var $table = $('<table></table>').addClass(
                'table mobile-table-width table-drag table-copy').css({
                "width": "99%",
                "table-layout": "fixed"
            });
            var $thead = $('<thead></thead>');
            var $theadRow = $('<tr></tr>');
            $theadRow.append($('<th class="text-center w-7p" ></th>').text('Index'));
            $theadRow.append($('<th class="text-left w-20p"></th>').text('Artist'));
            $theadRow.append($('<th class="text-Song w-20p"></th>').text('Song'));
            $theadRow.append($('<th class="text-right w-10p"></th>').text('Status'));
            $thead.append($theadRow);
            $table.append($thead);
            var $tbody = $('<tbody></tbody>');
            $.each(datas, function(index, song) {
                $tbody.append(
                    `<tr class="cur-poiter">' +
            <td class="text-center">${(index + 1)}</td>
            <td class="text-left text-ellipsis" style="width: 20%;">${song.artist}</td>
            <td class="text-left text-ellipsis"><a target='_blank' href='https://open.spotify.com/track/${song.id}'>${song.song_name}</a></td>
            <td class="text-right"><span class="badge badge-danger">Not Found</span></td>
            </tr>`
                );
            });
            $table.append($tbody);
            $(`${element}`).append($table);
            activeTableCopy();
        }

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
                data: form + "&crossType=3",
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
                            'data': jsonObj,
                            'multiple': false
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
                        //                    $(this).jstree("check_all");

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
            $.ajax({
                type: "GET",
                url: "/autoMakeLyricHub",
                data: {
                    'trackId': trackId,
                    'trackType': trackType,
                    'hubs': hubs
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    var mess = data.content;
                    if (data.status == 'success') {
                        mess = '<ol>';
                        $.each(data.content, function(key, value) {
                            //                            console.log(value);
                            mess += '<li>' + value.channel_name + ' &rarr; ' + (value.status ==
                                1 ? "success" : "fail") + '  </li>';
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
            $("#track-id").val(musicId);
            $("#track-type").val(musicType);
            //        console.log($("#track-id").val());
            var songName = $(this).attr("data-songname");
            //$("#content-dialog").html("");
            $("#dialog-loading").show();
            refreshJsTree();
            $("#dialog-multi-upload-title").html("Config make videos for " + songName);
            $(".dialog-icon").html('<i class="ti-music-alt"></i>');
            $('#dialog_multi_upload').modal({
                backdrop: false
            });
        });
    </script>
@endsection
