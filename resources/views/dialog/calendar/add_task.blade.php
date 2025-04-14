<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 justify-content-start"><strong>Filters</strong>
                        </h5>
                    </div>
                    <div class="">

                        <button type="button" class="btn btn-outline-secondary btn-text-secondary"
                            id="clearFilters">Clear Filters</button>
                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                            data-id="filterModal" data-toggle="tooltip" data-placement="top" title="Close"
                            style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                fill="currentColor" class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div id="modal-filter" class="modal-body overflow-hidden">
                <!-- Text filter input -->
                <div class="form-group position-relative">
                    <i id="text_filter_loading" class="fa fa-circle-o-notch fa-spin position-absolute disp-none"
                        style="top: 11px;right: 7px;"></i>
                    <input type="text" class="form-control" id="text_filter"
                        placeholder="Type task id,task name,campaign id...">
                </div>
                <div class="form-group position-relative">
                    <i id="text_filter_job_loading" class="fa fa-circle-o-notch fa-spin position-absolute disp-none"
                        style="top: 11px;right: 7px;"></i>
                    <input type="text" class="form-control" id="text_filter_job"
                        placeholder="Type job id, job name...">
                </div>


                <div class="form-check d-flex align-items-center justify-content-end">
                    <!--<input class="form-check-input m-0" type="checkbox" value="1" id="is_assigned">-->
                    <label class="form-check-label">
                        Created By
                    </label>
                </div>
                <div id="div-select-user">
                    <div class="d-flex justify-content-end ">
                        <button
                            class="btn btn-secondary user-select-btn btn-circle w-30px h-30px d-flex justify-content-center align-items-center"
                            id="customUserButton1">
                            <i class="fa fa-user"></i>
                        </button>
                        <input type="hidden" class="selectedUserIds" id="selectedCreatedBy">
                    </div>
                </div>
                <div class="form-check d-flex align-items-center justify-content-end">
                    <!--<input class="form-check-input m-0" type="checkbox" value="1" id="is_assigned">-->
                    <label class="form-check-label">
                        Assignees
                    </label>
                </div>
                <div id="div-select-user2">
                    <div class="d-flex justify-content-end ">
                        <button
                            class="btn btn-secondary user-select-btn btn-circle w-30px h-30px d-flex justify-content-center align-items-center"
                            id="customUserButton2">
                            <i class="fa fa-user"></i>
                        </button>
                        <input type="hidden" class="selectedUserIds" id="selectedAssignees">
                    </div>
                </div>
                <div class="form-check d-flex align-items-center justify-content-end m-t-5 m-r-10">
                    <input class="form-check-input m-0" type="checkbox" value="1" id="is_hide_finished">
                    <label class="form-check-label" for="is_hide_finished" style="padding-right: 1.25rem">
                        Hide completed tasks
                    </label>
                </div>
                <ul id="result-tab" class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="#result-tasks" data-toggle="tab" aria-expanded="false" class="nav-link active">
                            Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#result-jobs" data-toggle="tab" aria-expanded="true" class="nav-link ">
                            Jobs
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="result-tasks">
                        <div id="result-filter" class="mb-3">
                            <ul class="list-group" id="results-task-ul">

                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane fade " id="result-jobs">
                        <ul class="list-group" id="results-job-ul">

                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade none-border" id="modal-add-task">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <span id="created_by_avatar"></span>
                            <strong id="dialog-task-title">Task</strong>
                        </h5>
                    </div>
                    <div class="">
                        @if ($is_admin_calendar)
                            <button
                                class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 btn-move"
                                data-toggle="tooltip" data-placement="top" title="Move">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8" />
                                </svg>
                            </button>
                            <button
                                class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 btn-repeat"
                                data-toggle="tooltip" data-placement="top" title="Clone">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                                </svg>
                            </button>
                        @endif
                        <a target="_blank"
                            class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 btn-view-campaign"
                            data-toggle="tooltip" data-placement="top" title="View Campaign">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                                <path fill-rule="evenodd"
                                    d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                            </svg>
                        </a>
                        <button
                            class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 btn-reload-list-job"
                            data-toggle="tooltip" data-placement="top" title="Reload">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-arrow-clockwise m-t-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                                <path
                                    d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                            </svg>
                        </button>
                        @if ($is_admin_calendar)
                            <button
                                class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 btn-delete-task delete-event"
                                data-toggle="tooltip" data-placement="top" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path
                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                    <path
                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                </svg>
                            </button>

                            <button
                                class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 btn-save-task save-event"
                                data-toggle="tooltip" data-placement="top" title="Save">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-floppy m-t-1" viewBox="0 0 16 16">
                                    <path d="M11 2H9v3h2z" />
                                    <path
                                        d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                                </svg>
                            </button>
                        @endif
                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                            data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close"
                            style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body div_scroll_50">
                <form id="form-add-task" class="form-style">
                    <input type="hidden" id="task_id" name="task_id" />
                    <input type="hidden" id="curr_start" name="curr_start" />
                    <input type="hidden" id="rage_start" name="rage_start" />
                    <input type="hidden" id="rage_end" name="rage_end" />
                    <input type="hidden" id="task_repeat" name="task_repeat" value="" />
                    <input type="hidden" id="task_move" name="task_move" value="" />
                    <fieldset class="fieldset-custom m-b-10">
                        <legend class="legend-custom">Task Info</legend>
                        <div class="row col-12">
                            <div class="col-md-4">
                                <div class="form-group row col-12">
                                    <label class="col-form-label">Task Type</label>
                                    <select id="task_type" name="task_type" data-show-subtext="true"
                                        data-live-search="true" style="min-height: calc(1.5em + 1.1rem + 6px);"
                                        class="select2_multiple form-control form-control-sm ">
                                        <option value="campaign">Campaign</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 div_campaign">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Campaign <span
                                            class="color-red">*</span></label>
                                    <select id="campaign_id" name="campaign_id" data-show-subtext="true"
                                        data-live-search="true" data-size="5"
                                        class="select2_multiple form-control form-control-sm search_select">
                                        {!! $campaigns !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Priority <span
                                            class="color-red">*</span></label>
                                    <select id="task_priority" name="task_priority" data-show-subtext="true"
                                        data-live-search="true"
                                        class="select2_multiple form-control form-control-sm search_select">
                                        <option value="b_medium">Medium</option>
                                        <!--<option value="c_high">High</option>-->
                                        <option value="d_urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 div_other">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">&nbsp;</label>
                                    <div
                                        class="col-12 checkbox checkbox-primary d-flex align-items-center checkbox-rounded">
                                        <input id="ck_auto_job" class="checkbox-multi" type="checkbox"
                                            value="1">
                                        <label class="m-b-18 p-l-0"></label>
                                        <p class="w-100 m-0 truncate">Auto job</p>
                                    </div>
                                    <span class="font-12 text-muted font-italic m-l-5">Auto make a job with same task
                                        name</span>
                                </div>
                            </div>
                        </div>
                        <div class="row col-12">
                            <div class="col-md-12">
                                <div class="form-group row p-l-10 p-r-10">
                                    <label class="col-form-label">Task Name <span class="color-red">*</span></label>
                                    <input id="task_name" class="form-control form-control-sm control-h-lg"
                                        placeholder="Enter name" type="text" name="task_name" />
                                </div>
                            </div>
                        </div>
                        <div class="row col-12">



                        </div>
                    </fieldset>
                    <div class="row div_other div_ck_auto_job ">
                        <div class="col-md-12">
                            <fieldset class="fieldset-custom">
                                <legend class="legend-custom">Job Info</legend>
                                <div class="row col-12">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Description</label>
                                            <div class="col-12">
                                                <input id="job_des_task" name="job_des_task" type="hidden" />
                                                <div id="summernote_job_des_task"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Assignees <span
                                                    class="color-red">*</span></label>
                                            <select id="username_task" name="username_task[]"
                                                data-show-subtext="true" data-live-search="true" multiple=""
                                                data-size="5" data-container="body"
                                                class="select2_multiple form-control form-control-sm search_select">
                                                {!! $users !!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Member</label>
                                            <select id="job_member_task" name="job_member_task[]"
                                                data-show-subtext="true" data-live-search="true" multiple=""
                                                data-size="5" data-container="body"
                                                class="select2_multiple form-control form-control-sm search_select">
                                                {!! $members !!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row col-12">
                                            <label class="col-form-label">Job Type <span
                                                    class="color-red">*</span></label>
                                            <select id="job_type_task" name="job_type_task" data-show-subtext="true"
                                                data-live-search="true" data-container="body"
                                                class="select2_multiple form-control form-control-sm control-h-lg">
                                                <option value="promo">Promo</option>
                                                <option value="submission">Submission</option>
                                                <option value="design">Design</option>
                                                <option value="ads">Ads</option>
                                                <option value="channel">Main Channel</option>
                                                <option value="dev">Dev</option>
                                                <option value="sale">Sale</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row col-12 ">
                                            <label class="col-form-label">Priority <span
                                                    class="color-red">*</span></label>
                                            <select id="job_priority_task" name="job_priority_task"
                                                data-show-subtext="true" data-live-search="true"
                                                class="select2_multiple form-control form-control-sm control-h-lg">
                                                <option value="b_medium">Medium</option>
                                                <option value="d_urgent">Urgent</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group row col-12 ">
                                            <label class="col-form-label">Estimate <span
                                                    class="color-red">*</span></label>
                                            <!--                                    <input type="number" id="job_man_hour" class="form-control"
                                                    name="job_man_hour" style="min-height: calc(1.5em + 1.1rem + 6px);"
                                                    @if (!$is_admin_calendar) readonly @endif>-->
                                            <div class="input-group">
                                                <input type="number" class="form-control control-h-lg2"
                                                    id="job_man_hour_task" name="job_man_hour_task" step="0.01"
                                                    placeholder="Enter value"
                                                    @if (!$is_admin_calendar) readonly @endif>
                                                <select class="custom-select control-h-lg2"
                                                    id="job_man_hour_unit_task" name="job_man_hour_unit_task"
                                                    @if (!$is_admin_calendar) readonly @endif>
                                                    <option value="minutes">Min</option>
                                                    <option value="hours">Hr</option>
                                                </select>
                                            </div>
                                            <span class="font-12 text-muted font-italic m-l-5">Times to complete the
                                                job</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Due Date </label>
                                            <div class="col-12">
                                                <input type="datetime-local" id="job_duedate_task"
                                                    class="form-control control-h-lg2" name="job_duedate"
                                                    @if (!$is_admin_calendar) readonly @endif>

                                            </div>
                                            @if ($is_admin_calendar)
                                                <div class="col-12 m-t-5 d-flex justify-content-sm-between">
                                                    <button class="btn-plus-hour-task form-control cus-point"
                                                        style="line-height: inherit;width: auto;"
                                                        value="1">+1h</button>
                                                    <button class="btn-plus-hour-task form-control cus-point"
                                                        style="line-height: inherit;width: auto;"
                                                        value="2">+2h</button>
                                                    <button class="btn-plus-hour-task form-control cus-point"
                                                        style="line-height: inherit;width: auto;"
                                                        value="3">+3h</button>
                                                    {{-- <button class="btn-plus-hour-task form-control cus-point"
                                                        style="line-height: inherit;width: auto;" value="4">+4h</button>
                                                    <button class="btn-plus-hour-task form-control cus-point"
                                                        style="line-height: inherit;width: auto;" value="5">+5h</button>
                                                                        <button class="btn-plus-hour-task form-control cus-point"
                                                                                            style="line-height: inherit;width: auto;" value="6">+6h</button> --}}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="col-12 col-form-label">Penalty <span
                                                    class="color-red">*</span></label>
                                            <div class="input-group bootstrap-touchspin">
                                                <input type="number" value="20" id="penalty_task"
                                                    name="penalty_task" class="form-control control-h-lg2">
                                                <span class="input-group-addon bootstrap-touchspin-postfix">%</span>
                                            </div>
                                            <span class="font-12 text-muted font-italic m-l-5">Over Due Penalty</span>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="div_job_group div_campaign">
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="fieldset-custom">
                                    <legend class="legend-custom">Job group</legend>
                                    <div class="col-md-12">
                                        <!--<label class="col-form-label">Job group</label>-->
                                        <div class="d-flex gap-15px mb-7">
<!--                                            <button type="button" data-id="div_artist_setup"
                                                class="btn-job-group btn-outline-dashed btn-outline-dashed-sm"
                                                value="artist setup">
                                                artist setup
                                            </button>-->
                                            <button type="button" data-id="div_campaign_setup"
                                                class="btn-job-group btn-outline-dashed btn-outline-dashed-sm"
                                                value="campaign setup">
                                                campaign setup
                                            </button>
                                            <button type="button" data-id="div_release_day"
                                                class="btn-job-group btn-outline-dashed btn-outline-dashed-sm"
                                                value="release day">
                                                release day
                                            </button>
                                            <button type="button" data-id="div_campaign_mid"
                                                class="btn-job-group btn-outline-dashed btn-outline-dashed-sm"
                                                value="campaign mid">
                                                campaign mid
                                            </button>
                                            <button type="button" data-id="div_campaign_end"
                                                class="btn-job-group btn-outline-dashed btn-outline-dashed-sm"
                                                value="campaign end">
                                                campaign end
                                            </button>
                                        </div>
                                    </div>
                                    <div id="div_artist_setup" class="col-md-6 disp-none">
                                        <label class="col-form-label">Artist Setup</label>
                                        <div id="div_artist_setup_jobs"></div>
                                        <div class="col-md-9">
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Due Date </label>
                                                <div class="col-12">
                                                    <input type="datetime-local" id="artist_setup_duedate"
                                                        class="form-control" name="artist_setup_duedate">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="div_campaign_setup" class="col-md-6 disp-none">
                                        <label class="col-form-label">Campaign Setup</label>
                                        <div id="div_campaign_setup_jobs"></div>
                                        <div class="col-md-9">
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Due Date </label>
                                                <div class="col-12">
                                                    <input type="datetime-local" id="campaign_setup_duedate"
                                                        class="form-control" name="campaign_setup_duedate">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="div_release_day" class="col-md-6 disp-none">
                                        <label class="col-form-label">Release Day</label>
                                        <div id="div_release_day_jobs"></div>
                                        <div class="col-md-9">
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Due Date </label>
                                                <div class="col-12">
                                                    <input type="datetime-local" id="release_day_duedate"
                                                        class="form-control" name="release_day_duedate">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="div_campaign_mid" class="col-md-6 disp-none">
                                        <label class="col-form-label">Campaign Mid</label>
                                        <div id="div_campaign_mid_jobs"></div>
                                        <div class="col-md-9">
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Due Date </label>
                                                <div class="col-12">
                                                    <input type="datetime-local" id="campaign_mid_duedate"
                                                        class="form-control" name="campaign_mid_duedate">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="div_campaign_end" class="col-md-6 disp-none">
                                        <label class="col-form-label">Campaign End</label>
                                        <div id="div_campaign_end_jobs"></div>
                                        <div class="col-md-9">
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Due Date </label>
                                                <div class="col-12">
                                                    <input type="datetime-local" id="campaign_end_duedate"
                                                        class="form-control" name="campaign_end_duedate">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </fieldset>
                            </div>
                        </div>


                    </div>
                    <input type="hidden" id="job_groups" name="job_groups" />
                    <div id="div_jobs">
                        <div class="task_progress_bar">
                            <div class="d-flex justify-content-sm-between font-12">
                                <div class="w_25"><span id="pro-done">0 done</span></div>
                                <div class="w_25"><span id="pro-total">10 total</span></div>
                            </div>
                            <div class="w_center w_55">
                                <div class="progress-montly progress progress-custom progress-lg m-b-20 "
                                    style="white-space: nowrap;">
                                    <div id="pro-task" class="color-cus progress-bar bg-primary progress-lg"
                                        role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                        style="width: 0%;">
                                        <span id="pro-task-per" class="m-l-5">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <hr>
                        <div class="d-flex justify-content-sm-between align-items-center m-b-10">
                            <h6 class="modal-title mt-0"><i class="fa fa-tasks"></i> Jobs</h6>
                            <span class="toggle-filter" data-toggle="tooltip" data-placement="top"
                                title="Assign for {{ auth()->user()->user_name }}"><i
                                    class="fa fa-users font-20"></i></span>
                            <input type="hidden" id="job_owner_filter" name="job_owner_filter" value="all" />
                            @if ($is_admin_calendar)
                                <i class="ti-plus cur-poiter dialog-job font-20" data-toggle="tooltip"
                                    data-placement="top" title="Add Job"></i>
                            @endif
                        </div>
                        <div class="row ">
                            <div class="col-md-12">
                                <div class="div_list_jobs ps ps--active-y">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mt-0 m-b-10"><i class="fa  fa-history"></i> History</h6>
                        <div class="div_task_history">

                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<div class="modal fade none-border" id="modal-add-job">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div id="dialog-job-title" class="d-flex justify-content-start align-items-center">
                        <h5>Job</h5>
                    </div>
                    <div>
                        <button
                            class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 btn-reload-job"
                            data-toggle="tooltip" data-placement="top" title="Reload">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                fill="currentColor" class="bi bi-arrow-clockwise m-t-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                                <path
                                    d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                            </svg>
                        </button>
                        @if ($is_admin_calendar)
                            <button id="btn-delete-job"
                                class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 btn-delete-job"
                                data-toggle="tooltip" data-placement="top" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-trash3 m-t-1" viewBox="0 0 16 16">
                                    <path
                                        d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                </svg>
                            </button>
                        @endif

                        <button
                            class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 btn-save-job"
                            data-toggle="tooltip" data-placement="top" title="Save">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                fill="currentColor" class="bi bi-floppy m-t-1" viewBox="0 0 16 16">
                                <path d="M11 2H9v3h2z" />
                                <path
                                    d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                            </svg>
                        </button>
                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                            data-id="modal-add-job" data-toggle="tooltip" data-placement="top" title="Close"
                            style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                fill="currentColor" class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body div_scroll_50">
                <form id="form-add-job" class="form-style">
                    {{ csrf_field() }}
                    <input type="hidden" id="job_id" name="job_id" />
                    <input type="hidden" id="job_code" name="job_code" />
                    <input type="hidden" id="job_id_open" value="{{ $job_id_open }}" />
                    <input type="hidden" id="task_id_open" value="{{ $task_id_open }}" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Title <span class="color-red">*</span></label>
                                <div class="col-12">
                                    <textarea id="job_title" class="form-control " rows="5" @if (!$is_admin_calendar) readonly @endif
                                        name="job_title" spellcheck="false" style="line-height: 1.25;height: 60px"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Description <span id="job-tut-link" class="font-13 font-italic font-normal"></span></label>
                                <div class="col-12">
                                    <input id="job_des" name="job_des" type="hidden" />
                                    <div id="summernote_job_des"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if ($is_admin_calendar)
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Assignees <span
                                            class="color-red">*</span></label>
                                    <select id="username" name="username[]" data-show-subtext="true"
                                        data-live-search="true" multiple="" data-size="5" data-container="body"
                                        class="select2_multiple form-control form-control-sm search_select">
                                        {!! $users !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Member</label>
                                    <select id="job_member" name="job_member[]" data-show-subtext="true"
                                        data-live-search="true" multiple="" data-size="5" data-container="body"
                                        class="select2_multiple form-control form-control-sm search_select">
                                        {!! $members !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Job Type <span
                                            class="color-red">*</span></label>
                                    <select id="job_type" name="job_type" data-show-subtext="true"
                                        data-live-search="true" data-container="body"
                                        class="select2_multiple form-control form-control-sm search_select">
                                        <option value="promo">Promo</option>
                                        <option value="submission">Submission</option>
                                        <option value="design">Design</option>
                                        <option value="ads">Ads</option>
                                        <option value="channel">Main Channel</option>
                                        <option value="dev">Dev</option>
                                        <option value="sale">Sale</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Priority <span
                                            class="color-red">*</span></label>
                                    <select id="job_priority" name="job_priority" data-show-subtext="true"
                                        data-live-search="true"
                                        class="select2_multiple form-control form-control-sm search_select">
                                        <option value="b_medium">Medium</option>
                                        <option value="d_urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group row col-12">
                                <label class="col-form-label">Status <span class="color-red">*</span></label>
                                <select id="job_status" name="job_status"
                                    class="select2_multiple form-control form-control-sm control-h-lg2">
                                    <option value="0">New</option>
                                    <option value="1">Processing</option>
                                    <option value="2">On Hold</option>
                                    <option value="3">Finished</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-form-label">Estimate <span class="color-red">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control control-h-lg2" id="job_man_hour"
                                        name="job_man_hour" step="0.01" placeholder="Enter value"
                                        @if (!$is_admin_calendar) readonly @endif>
                                    <select class="custom-select control-h-lg2" id="job_man_hour_unit"
                                        name="job_man_hour_unit" @if (!$is_admin_calendar) readonly @endif>
                                        <option value="minutes">Min</option>
                                        <option value="hours">Hr</option>
                                    </select>
                                </div>
                                <span class="font-12 text-muted font-italic m-l-5">Times to complete the job</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Due Date </label>
                                <div class="col-12">
                                    <input type="datetime-local" id="job_duedate" class="form-control control-h-lg2"
                                        name="job_duedate" @if (!$is_admin_calendar) readonly @endif>

                                </div>
                                @if ($is_admin_calendar)
                                    <div class="col-12 m-t-5 d-flex justify-content-sm-between">
                                        <button class="btn-plus-hour form-control cus-point"
                                            style="line-height: inherit;width: auto;" value="1">+1h</button>
                                        <button class="btn-plus-hour form-control cus-point"
                                            style="line-height: inherit;width: auto;" value="2">+2h</button>
                                        <button class="btn-plus-hour form-control cus-point"
                                            style="line-height: inherit;width: auto;" value="3">+3h</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="col-12 col-form-label">Penalty <span class="color-red">*</span></label>
                                <div class="input-group bootstrap-touchspin">
                                    <input type="number" value="20" id="penalty" name="penalty"
                                        class="form-control control-h-lg2">
                                    <span class="input-group-addon bootstrap-touchspin-postfix">%</span>
                                </div>
                                <span class="font-12 text-muted font-italic m-l-5">Over Due Penalty</span>
                            </div>
                        </div>
                    </div>


                    <div id="comment">
                        <div id="job_detail_wrap"class="row">
                            <div class="col-md-12">
                                <fieldset class="fieldset-custom">
                                    <legend class="legend-custom">Job detail</legend>
                                    <div id="job_detail" class="col-md-12">
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Result</label>
                                    <div class="col-12">
                                        <textarea id="job_result" class="form-control" rows="5" name="job_result" spellcheck="false"
                                            placeholder="Paste result here..." style="line-height: 1.25;height: 100px"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row div_comment_content">
                            <div class="col-md-12">
                                <fieldset class="fieldset-custom">
                                    <legend class="legend-custom">Comment</legend>
                                    <div class="div_list_comment " style="height: auto">

                                    </div>

                                </fieldset>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <!--<label class="col-12 col-form-label">Comment</label>-->
                                    <div class="col-12">
                                        <input id="job_comment" name="job_comment" type="hidden" />
                                        <div id="summernote_job_comment"></div>
                                        <!--                                        <textarea id="job_comment" class="form-control " rows="5" name="job_comment" spellcheck="false"
                                            placeholder="Write a comment..." style="height: 100px"></textarea>-->
                                        <button type="button"
                                            class="btn btn-sm btn-success waves-effect waves-light float-right m-t-5 btn-save-comment">Comment</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <br>
            </div>
        </div>
        <div class="panel-disabled">
            <div class="loader-1"></div>
        </div>
    </div>
</div>

<div id="emojiModal" class="modal-emoji">
    <div class="emoji-picker"></div>
</div>


