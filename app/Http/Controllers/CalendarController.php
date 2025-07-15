<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\Events\ChatEvent;
use App\Http\Models\CampaignChecklists;
use App\Http\Models\CampaignJobs;
use App\Http\Models\CampaignStatistics;
use App\Http\Models\CampaignTasks;
use App\Http\Models\CampaignTasksHistory;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function event;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function response;
use function view;

class CalendarController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        $users = $this->genListUserForMoveChannel($user, $request, 1, 3, 0, 0, 1, 1);
        $users = "<optgroup label='Group'><option  data-content=\"<img class='rounded-circle img-cover disp-inline h-40px w-40px mw-40px' src='images/user.jpg'> All User\" value='all_user'></option>"
                . "<option  data-content=\"<img class='rounded-circle img-cover disp-inline h-40px w-40px mw-40px' src='images/user.jpg'> Bassteam\" value='bassteam'></option>"
                . "<option  data-content=\"<img class='rounded-circle img-cover disp-inline h-40px w-40px mw-40px' src='images/user.jpg'> Zteam\" value='zteam'></option></optgroup>"
                . "<optgroup label='Member'>$users</option>";
        $members = $users;
        $j = null;
        $t = null;
        if (isset($request->jid)) {
            $j = $request->jid;
            $job = CampaignJobs::find($j);
            if ($job) {
                $t = $job->task_id;
            }
        }
        $tagUsers = $this->getTagUser();
        $highlightUser = [];
        foreach ($tagUsers as $tag) {
            $highlightUser[] = "<img data-user=\"$tag\" class='rounded-circle img-cover m-r-5 w-30px h-30px' src='/images/avatar/$tag.jpg'> $tag";
        }
//        Log::info(json_encode($users));
        return view("components.calendar", [
            "campaigns" => $this->loadCampaign(),
            "users" => $users,
            "members" => $members,
            "highlightUser" => $highlightUser,
            "job_id_open" => $j,
            "task_id_open" => $t,
        ]);
    }

    public function getTasks(Request $request) {
        $user = Auth::user();
        DB::enableQueryLog();
        Log::info($user->user_name . '|CalendarController.getTasks|request=' . json_encode($request->all()));
//        $firstDayOfMonth = gmdate("Y-m-d", strtotime($request->month));
//        $newStart = strtotime($request->start) + 15 * 86400;
//        $firstDayOfMonth = gmdate("Y-m-01", $newStart);
//        $lastDayOfMonth = date("Y-m-t", strtotime($firstDayOfMonth));
        $today = date('Y-m-d');

        // Xác định thời điểm bắt đầu và kết thúc của ngày hôm nay
        $startOfDay = strtotime($today . ' 00:00:00 GMT+7');
        $endOfDay = strtotime($today . ' 23:59:59 GMT+7');

        $startOfWeek = strtotime('monday this week GMT+7');
        $endOfWeek = strtotime('sunday this week GMT+7');
//        Log::info("date $startOfDay $endOfDay $startOfWeek $endOfWeek");

        $curr = time();
        $tmp = $request->filter;
        $filter = json_decode($tmp);
        $whereFilter = "";
        $resultTasks = [];
        $resultJobs = [];
        if ($filter->filter_duesoon == 1) {
            $whereFilter .= " (status <> 3 and duadate_time > $curr and duadate_time - $curr  < 2* 3600) ";
        }
        if ($filter->filter_dueweek == 1) {
            $whereFilter .= ($whereFilter != "" ? "or" : "") . " (status <> 3 and duadate_time >= $startOfWeek and duadate_time  <= $endOfWeek) ";
        }
        if ($filter->filter_duetoday == 1) {
            $whereFilter .= ($whereFilter != "" ? "or" : "") . " (status <> 3 and duadate_time >= $startOfDay and duadate_time  <= $endOfDay) ";
        }
        if ($filter->filter_overdue == 1) {
            $whereFilter .= ($whereFilter != "" ? "or" : "") . " (finish_time > duadate_time or (finish_time is null and duadate_time < $curr)) ";
        }
        if ($filter->filter_notdone == 1) {
            $whereFilter .= ($whereFilter != "" ? "or" : "") . " status = 0 ";
        }
        if ($filter->filter_finished == 1) {
            $whereFilter .= ($whereFilter != "" ? "or" : "") . " status = 3 ";
        }
        if ($filter->filter_progress == 1) {
            $whereFilter .= ($whereFilter != "" ? "or" : "") . " status = 1 ";
        }
        if ($whereFilter != "") {
            $whereFilter = " and (" . $whereFilter . ") ";
        }
        $whereTextFilter = "";
        $whereTextFilterForJob = "";
        if ($request->text_filter != null && $request->text_filter != "") {
            $textFilter = trim($request->text_filter);
            $whereTextFilter .= " and (title like '%$textFilter%' or id = '$textFilter') ";
        }
        if ($request->text_filter_job != null && $request->text_filter_job != "") {
            $textFilterJob = trim($request->text_filter_job);
            $whereTextFilterForJob .= " and (name like '%$textFilterJob%' or id = '$textFilterJob') ";
        }
        $whereTime = "and start >= '$request->start' and (end < '$request->end' or end is null)";
        if ($request->is_admin_music) {
            $whereAdmin = "";
            if ($request->filter_assignees != null) {
                $filterAssignees = explode(",", $request->filter_assignees);
                $assignees = "('" . implode("','", $filterAssignees) . "')";
                $whereAdmin .= " (`username` in $assignees ";
                foreach ($filterAssignees as $ass) {
                    $whereAdmin .= " or member like '%$ass%' ";
                }
                $whereAdmin .= ")";
            }

            if ($request->filter_createdby != null) {
                $whereAdmin .= ($whereAdmin != "" ? "and" : "") . "";
                $createdby = implode("','", explode(",", $request->filter_createdby));
                $createdby = "('" . $createdby . "')";
                $whereAdmin .= " admin in $createdby ";
            }

            if ($whereAdmin != "") {
                $whereAdmin = " and (" . $whereAdmin . ") ";
            }

            $whereJob = "";

            if ($whereTextFilterForJob != "" || $whereFilter != "" || $whereAdmin != "") {
                $whereJob = "and id in (SELECT task_id FROM `campaign_jobs` WHERE `del_status` = 0 $whereTextFilterForJob $whereFilter $whereAdmin)";
            }
            $sql = "select id,title,start,end, campaign_id,priority as className,task_group,task_type,created_by from campaign_tasks
                                where del_status =0 $whereTextFilter $whereJob";

//            Log::info("sqltask: $sql $whereTime");
            $tasks = DB::select("$sql $whereTime");
            if ($whereTextFilter != "" || $whereTextFilterForJob != "") {
                $resultTasks = DB::select($sql);
                $whereTask = "";
                if ($whereTextFilter != "") {
                    $whereTask = "and task_id in (select id from campaign_tasks where del_status =0 $whereTextFilter)";
                }
                $sqlJob = "SELECT * FROM `campaign_jobs` WHERE `del_status` = 0 $whereTextFilterForJob $whereFilter $whereAdmin $whereTask";
//                Log::info("sqljob: $sqlJob");
                $resultJobs = DB::select($sqlJob);
            }
            $jobs = DB::select("SELECT task_id,count(id) as jobs
                                FROM `campaign_jobs`
                                WHERE `status` != '3' AND `del_status` = '0'
                                group by task_id");
        } else {
//            and start >= '$firstDayOfMonth' and start <= '$lastDayOfMonth'
            $whereCreatedTask = "";
            $whereCreatedJob = "";
            if ($request->is_admin_calendar) {
                $whereCreatedJob = "or admin = '$user->user_name'";
                $whereCreatedTask = "or created_by = '$user->user_name'";
            }
            $sql = "select id,title,start,end, campaign_id,priority as className,task_group,task_type,created_by from campaign_tasks 
                                where del_status =0 $whereTextFilter
                                and (id in (SELECT task_id FROM `campaign_jobs` 
                                WHERE (`username` = '$user->user_name' or member like '%$user->user_name%' $whereCreatedJob) AND `del_status` = '0' $whereFilter) $whereCreatedTask)";
            $tasks = DB::select("$sql $whereTime");
            if ($whereTextFilter != "" || $whereTextFilterForJob != "") {
                $resultTasks = DB::select($sql);
            }
            $jobs = DB::select("SELECT task_id,count(id) as jobs
                                FROM `campaign_jobs`
                                WHERE (`username` = '$user->user_name' or member like '%$user->user_name%') AND `status` != '3' AND `del_status` = '0'
                                group by task_id");
        }
        $result = [];
        foreach ($tasks as $task) {
            $task->count_job = 0;
            if ($task->task_group != null) {
                $task->title = "(" . strtoupper(trim(str_replace(["campaign", "day"], ["", ""], $task->task_group))) . ") " . $task->title;
            }
            $task->is_admin_music = $request->is_admin_music;
            foreach ($jobs as $job) {
                if ($task->id == $job->task_id) {
                    $task->count_job = $job->jobs;
                    break;
                }
            }
            if ($request->is_hide == "true") {
                if ($task->count_job > 0) {
                    $result[] = $task;
                }
            }
        }

        //ẩn job đã hoàn thành
//        Log::info(DB::getQueryLog());
        if ($request->is_hide == "true") {
            $tasks = $result;
//            return response()->json($result);
        }
        usort($tasks, function($a, $b) {
            return $a->count_job < $b->count_job;
        });
        usort($resultTasks, function($a, $b) {
            return $a->start < $b->start;
        });
        return response()->json(["tasks" => $tasks, "result_task" => $resultTasks, "result_job" => $resultJobs]);
    }

    public function findTask(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.findTask|request=' . json_encode($request->all()));
        $task = CampaignTasks::where("id", $request->task_id)->where("del_status", 0)->first();
        if (!$task) {
            return response()->json(["status" => "danger", "message" => "not found", "data" => null]);
        }
        return response()->json(["status" => "success", "message" => "success", "data" => $task]);
    }

    public function getRepeatTasks(Request $request) {
        $user = Auth::user();
        DB::enableQueryLog();
        Log::info($user->user_name . '|CalendarController.getRepeateTasks|request=' . json_encode($request->all()));
        $tasks = CampaignTasks::where("campaign_id", $request->campaign_id)
                ->where("del_status", 0)
                ->pluck("start");
//        Log::info(DB::getQueryLog());

        $im = implode(", ", $tasks->toArray());
        return response()->json(["status" => "success", "start_str" => $im]);
    }

    public function createOrUpdateTasks(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.createOrUpdateTasks|request=' . json_encode($request->all()));
        //trong 1 ngày chỉ được tạo 1 task cho 1 campaign_id, task other thì tạo thỏa mái
        if ($request->task_type != "other") {
            $campaignId = $request->campaign_id;
        } else {
            $campaignId = Utils::slugify($request->title);
        }
        $check = CampaignTasks::where("campaign_id", $campaignId)->where("start", $request->start)->where("del_status", 0)->first();
        if ($check) {
            if ((isset($request->task_id) && $check->id != $request->task_id) || !isset($request->task_id)) {
                return response()->json(["status" => "error", "message" => "Dupticate task on $request->start"]);
            }
        }
        $log = "";
        if (isset($request->task_id)) {
            $task = CampaignTasks::find($request->task_id);
            if ($task->title != $request->title) {
                $task->title = $request->title;
                $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update title" . PHP_EOL;
            }
            if ($task->campaign_id != $campaignId) {
                $task->campaign_id = $campaignId;
                $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update campaign_id to $campaignId" . PHP_EOL;
            }
            if ($task->priority != $request->task_priority) {
                $task->priority = $request->task_priority;
                $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update priority to $request->task_priority" . PHP_EOL;
            }
            $task->log = $log . $task->log;
            $task->save();

            //move task sang ngày khác
            if ($request->task_move != null && $request->task_move != $task->start) {
                $task->start = $request->task_move;
                $task->end = null;
                $log = Utils::timeToStringGmT7(time()) . " $user->user_name update start to $request->task_move" . PHP_EOL;
                $task->log = $log . $task->log;
                $task->save();
                return response()->json(["status" => "success", "message" => "Success"]);
            }

            $taskRepeat = explode(",", $request->task_repeat);
            $taskRepeat = array_map('trim', $taskRepeat);
//            Log::info(json_encode($taskRepeat));
            //tự động xóa những task trong những ngày không có trong list truyền lên
            $log2 = Utils::timeToStringGmT7(time()) . " $user->user_name deleted by repeater" . PHP_EOL;
            $taskToDelete = CampaignTasks::where("campaign_id", $campaignId)->whereNotIn("start", $taskRepeat)->where("del_status", 0);
            $deleteId = $taskToDelete->pluck("id");
            $taskToDelete = $taskToDelete->update(["del_status" => 1, "log" => DB::raw("CONCAT('$log2',log)")]);
            CampaignJobs::whereIn("task_id", $deleteId)->update(["del_status" => 1, "log" => DB::raw("CONCAT('$log2',log)")]);
//            Log::info(DB::getQueryLog());
            //tự động tạo nhiều task theo multiple date select đã chọn
            if ($request->task_repeat != null) {
                //lấy job của task gốc để tạo cho task clone
                $jobs = CampaignJobs::where("task_id", $request->task_id)->where("del_status", 0)->get();

                foreach ($taskRepeat as $t) {
                    $check = CampaignTasks::where("campaign_id", $campaignId)->where("start", $t)->where("del_status", 0)->first();
                    if (!$check) {
                        $task = new CampaignTasks();
                        $task->task_type = $request->task_type;
                        $task->created_by = $user->user_name;
                        $task->start = $t;
                        $task->title = $request->title;
                        $task->campaign_id = $campaignId;
                        $task->priority = $request->task_priority;
                        $task->created = Utils::timeToStringGmT7(time());
                        $task->log = $task->log . Utils::timeToStringGmT7(time()) . " $user->user_name created by repeater" . PHP_EOL;
                        $task->save();
                        foreach ($jobs as $job) {
                            //clone job
                            $new = $job->replicate();
                            $oldDue = $job->due_date;
                            $split = explode("T", $oldDue);
                            if (count($split) == 2) {
                                $newDue = $t . "T" . $split[1];
                                $new->due_date = $newDue;
                                $unix_timestamp = strtotime($newDue) - $user->timezone * 3600;
                                $new->duadate_time = $unix_timestamp;
                            }
//                            Log::info(json_encode($split));
                            $new->task_id = $task->id;
                            $new->result = null;
                            $new->status = 0;
                            $new->save();
                        }
                    }
                }
            }
        } else {
            if ($request->task_type == "other") {
                if ($request->ck_auto_job == "true") {
                    if (!isset($request->job_type_task)) {
                        return response()->json(["status" => "error", "message" => "Please select Job Type"]);
                    }
                    if (!isset($request->username_task)) {
                        return response()->json(["status" => "error", "message" => "Please select a Assignees"]);
                    }
                    if (!isset($request->job_man_hour_task)) {
                        return response()->json(["status" => "error", "message" => "Please enter Estimate"]);
                    }
                }
            }
            $task = new CampaignTasks();
            $task->task_type = $request->task_type;
            $task->created_by = $user->user_name;
            $task->start = $request->start;
            $task->end = $request->end;
            $task->title = $request->title;
            $task->campaign_id = $campaignId;
            $task->priority = $request->task_priority;
            $task->created = Utils::timeToStringGmT7(time());
            $task->log = $task->log . Utils::timeToStringGmT7(time()) . " $user->user_name created" . PHP_EOL;
            $task->save();
            $taskId = $task->id;
            if ($request->task_type == "campaign") {
                $campaignStatic = CampaignStatistics::where("id", $campaignId)->first();
                $start = strtotime($request->start);
//            Log::info($start);
                $duration = $campaignStatic->duration * 86400 * 30;
                $midStartTime = $start + round($duration / 2);
                //2024/08/19 james confirm $midStartDate phải + thềm 3 ngày
                $endStartTime = $start + $duration + 3 * 86400;
                $midStartDate = gmdate("Y-m-d", $midStartTime);
                $endStartDate = gmdate("Y-m-d", $endStartTime);
                //            Log::info("$midStartDate $endStartDate");
                if (!empty($request->init_jobs)) {
                    $checkLists = CampaignChecklists::where("status", 1)->where("is_calendar", 1)
                                    ->whereIn("key", $request->init_jobs)
                                    ->orderBy("location_order", "asc")->orderBy("job_order", "asc")->get();
//                Log::info(json_encode($checkLists));
                    $newDueTime = '17:30';
                    foreach ($checkLists as $index => $check) {
                        //update task_group cho task vừa tạo
                        if ($index == 0) {
                            $task->task_group = $check->location;
                            $task->save();
                        }

                        //lấy ngày duedate
                        $group = str_replace(" ", "_", $check->location);
                        $due = $group . "_duedate";
                        $dueDate = $request->$due;
                        if ($check->location == 'release day') {

                            //lấy giờ duedate của release day để gán cho mid vs end
                            if ($dueDate != null) {
                                $split = explode("T", $dueDate);
                                if (count($split) == 2) {
                                    $newDueTime = $split[1];
                                }
                            } else {
                                $newDueTime = '17:30';
                            }
                        }
                        //tạo task mới sang ngày mới nếu là campaign mid, và campaign end
                        if ($check->location == 'campaign mid' || $check->location == 'campaign end') {
                            $midEndStart = $check->location == 'campaign mid' ? $midStartDate : $endStartDate;
                            $taskMid = CampaignTasks::where("start", $midEndStart)->where("campaign_id", $campaignId)->where("del_status", 0)->first();
                            if (!$taskMid) {
                                $taskMid = new CampaignTasks();
                                $taskMid->task_type = $request->task_type;
                                $taskMid->created_by = $user->user_name;
                                $taskMid->start = $midEndStart;
                                $taskMid->title = $request->title;
                                $taskMid->campaign_id = $campaignId;
                                $taskMid->priority = $request->task_priority;
                                $taskMid->created = Utils::timeToStringGmT7(time());
                                $taskMid->task_group = $check->location;
                                $taskMid->save();
                            }
                            $taskId = $taskMid->id;
                            $dueDate = $taskMid->start . "T" . $newDueTime;
                        }
                        $job = new CampaignJobs();
                        $job->task_id = $taskId;
                        $job->job_group = $check->location;
                        $job->job_type = $check->job_type;
                        $job->job_code = $check->key;
                        $job->name = $check->name;
                        $job->tutorial = $check->des;
                        $job->admin = $user->user_name;
                        $job->username = $check->persion;
                        $job->member = $check->member;
                        $job->priority = "b_medium";
                        $job->man_hour = $check->estimate;
                        $job->penalty = 20;
                        $job->created = Utils::timeToStringGmT7(time());
                        $job->year = gmdate("Y", time() + 7 * 3600);
                        $job->month = gmdate("m", time() + 7 * 3600);

//                    Log::info($due);
//                    Log::info("due " . $request->$due);
                        if ($dueDate != null) {
                            $job->due_date = $dueDate;
                            $unix_timestamp = strtotime($dueDate) - $user->timezone * 3600;
                            $job->duadate_time = $unix_timestamp;
                            //(chưa làm) nếu duedate là T7, CN thì phải chuyển thành T2
                        }

                        $job->save();
                        if ($check->job_detail != null) {
                            $details = json_decode($check->job_detail);
                            $tmps = [];
                            foreach ($details as $index => $detail) {
                                $tmp = (object) [
                                            "id" => "$job->job_code$index",
                                            "type" => $detail->type,
                                            "name" => $detail->name,
                                            "created" => time(),
                                            "due_date" => null,
                                            "result" => null,
                                            "is_finish" => 0
                                ];
                                // với dữ liệu Engagement và Promo Keywords thì lấy ở bên campaign đưa vào, mặc định là đã hoàn thành rồi
                                if ($detail->name == 'engagement') {
                                    $tmp->is_finish = 1;
                                }
                                if ($detail->name == 'Promo keywords') {
                                    $tmp->is_finish = 1;
                                }
                                if ($detail->name == 'spotify_id') {
                                    $tmp->is_finish = 1;
                                }
                                if (!empty($detail->button)) {
                                    $tmp->button = $detail->button;
                                }
                                if (!empty($detail->link)) {
                                    $tmp->link = $detail->link;
                                }
                                if (!empty($detail->group)) {
                                    $tmp->group = $detail->group;
                                }
                                if (!empty($detail->style)) {
                                    $tmp->style = $detail->style;
                                }

                                $tmps[] = $tmp;
                            }
                            $job->job_detail = json_encode($tmps);
                            $job->save();
                        }
                    }
                }
            } else {
                if ($request->ck_auto_job == "true") {
                    $role = "16";
                    if ($request->job_type_task == "design") {
                        $role = "23";
                    }
                    if ($request->job_type_task == "dev") {
                        $role = "24";
                    }
                    if ($request->job_type_task == "sale") {
                        $role = "25";
                    }
//                    if ($request->username_task[0] == "all_user") {
//                        $usernameTask = User::where("role", "like", "%$role%")->where("status", 1)->pluck("user_name");
//                    } else {
//                        $usernameTask = $request->username_task;
//                    }

                    if ($request->username_task[0] == "all_user") {
                        $username = User::where("role", "like", "%26%")->where("status", 1)->pluck("user_name");
                    } elseif ($request->username_task[0] == "bassteam") {
                        $username = User::where("role", "like", "%28%")->where("status", 1)->pluck("user_name");
                    } elseif ($request->username_task[0] == "zteam") {
                        $username = User::where("role", "like", "%29%")->where("status", 1)->pluck("user_name");
                    } else {
                        $username = $request->username_task;
                    }
                    $jobMember = [];
                    if (isset($request->job_member_task)) {
                        if ($request->job_member_task[0] == "all_user") {
                            $jobMember = User::where("role", "like", "%26%")->where("status", 1)->pluck("user_name")->toArray();
                        } elseif ($request->job_member_task[0] == "bassteam") {
                            $jobMember = User::where("role", "like", "%28%")->where("status", 1)->pluck("user_name")->toArray();
                        } elseif ($request->job_member_task[0] == "zteam") {
                            $jobMember = User::where("role", "like", "%29%")->where("status", 1)->pluck("user_name")->toArray();
                        } else {
                            $jobMember = $request->job_member_task;
                        }
                    }

                    $mess = "New job";
                    if (!empty($username)) {
                        if (empty($request->job_type_task)) {
                            return response()->json(["status" => "error", "message" => "Please select Job Type"]);
                        }
                        if ($request->job_man_hour_task == null) {
                            return response()->json(["status" => "error", "message" => "Please enter Estimate"]);
                        }
                        $hours = Utils::convertToHours($request->job_man_hour_task, $request->job_man_hour_unit_task);
                        if ($hours > 4) {
                            return response()->json(["status" => "error", "message" => "Estimate must not exceed 4 hours."]);
                        }


                        $penaltyTask = 20;
                        if (isset($request->penalty_task)) {
                            $penaltyTask = $request->penalty_task;
                        }
                        foreach ($username as $us) {
                            $members = [];
                            if (!empty($jobMember)) {
                                $members = Utils::deleteValueArray([$us], $jobMember);
                            }
                            $job = new CampaignJobs();
                            $job->admin = $user->user_name;
                            $job->task_id = $taskId;
                            $job->job_type = $request->job_type_task;
                            $job->name = $request->title;
                            $job->des = $request->job_des_task;
                            $job->username = $us;
                            $job->member = json_encode($members);
                            $job->priority = $request->job_priority_task;
                            $job->status = 0;
                            $job->man_hour = $hours;
                            $job->penalty = $penaltyTask;
                            $job->created = Utils::timeToStringGmT7(time());
                            $job->year = gmdate("Y", time() + 7 * 3600);
                            $job->month = gmdate("m", time() + 7 * 3600);
                            if ($request->job_duedate_task != null) {
                                $job->due_date = $request->job_duedate_task;
                                $unix_timestamp = strtotime($request->job_duedate_task) - $user->timezone * 3600;
                                $job->duadate_time = $unix_timestamp;
                            }
                            $job->save();
                            $members[] = $us;
                            foreach ($members as $mem) {
                                $history = new CampaignTasksHistory();
                                $history->type = 3;
                                $history->task_id = $job->task_id;
                                $history->job_id = $job->id;
                                $history->username = $user->user_name;
                                $history->receive = $mem;
                                $history->created = time();
                                $history->content = $mem == $us ? "New job" : "You are a member";
                                $history->title = $mem == $us ? "New job" : "You are a member";
                                $history->save();
                            }

                            $data = (object) [
                                        "type" => 2,
                                        "job_id" => $job->id,
                                        "title" => $job->name,
                                        "message" => $history->title,
                                        "comment" => null,
                                        "redirect" => "/calendar?jid=$job->id",
                                        "noti_id" => uniqid()
                            ];
                            event(new ChatEvent($user, [$us], $data));
                        }
                    }
                }
            }
        }
        return response()->json(["status" => "success", "message" => "Success"]);
    }

    public function deleteTask(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.deleteTask|request=' . json_encode($request->all()));
        if (isset($request->task_id)) {
            $task = CampaignTasks::find($request->task_id);
            if ($task) {
                if ($request->is_admin_music || $task->created_by == $user->user_name) {
                    $task->del_status = 1;
                    $task->log = Utils::timeToStringGmT7(time()) . " $user->user_name deleted" . PHP_EOL . $task->log;
                    $task->save();
                    CampaignJobs::where("task_id", $request->task_id)->update(["del_status" => 1]);
                    CampaignTasksHistory::where("task_id", $request->task_id)->update(["del_status" => 1]);
                    return response()->json(["status" => "success"]);
                } else {
                    return response()->json(["status" => "error", "message" => "You do not have permission to delete"]);
                }
            }
        }
        return response()->json(["status" => "error", "message" => "Not enough information"]);
    }

    public function getJobs(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.getJobs|request=' . json_encode($request->all()));
//        $jobs = CampaignJobs::where("task_id", $request->task_id)->where("del_status", 0)->get();
        $filter = "";
        if ($request->job_owner_filter == "one") {
            $filter = "and (username = '$user->user_name' or member like '%$user->user_name%')";
        }
        if (!$request->is_admin_music) {
            $filter = "and (username = '$user->user_name' or member like '%$user->user_name%' or admin = '$user->user_name')";
        }
//        $jobs = DB::select("select a.*,b.avatar from campaign_jobs a, users b where a.task_id = $request->task_id $filter and a.del_status = 0 and a.username = b.user_name");
        $jobs = DB::select("select a.*,b.avatar from (select * from campaign_jobs  where task_id = $request->task_id $filter and del_status = 0 ) a LEFT JOIN users b on a.username = b.user_name order by a.status asc,a.priority desc");
        $comments = DB::select("SELECT job_id,count(*) as comment
                                FROM `campaign_tasks_history`
                                WHERE `task_id` = '$request->task_id' AND `type` = '2' and del_status = 0
                                group by job_id");
        $total = count($jobs);
        $done = 0;
        foreach ($jobs as $job) {
            $job->is_admin_music = $request->is_admin_music;
            $job->is_admin_calendar = $request->is_admin_calendar;
            $job->cal_duedate = "";
            $job->duatime = "";
            $job->remain = 0;
            $job->comment = 0;
//            $job->name = htmlentities($job->name);
            if ($job->due_date != null) {
                //nếu tời gian finish time 
                $dueTime = strtotime("$job->due_date GMT+7");
                $job->duatime = gmdate("M d, h:i A", $dueTime + 7 * 3600);
                if ($job->finish_time != null) {
                    $remain = $dueTime - $job->finish_time;
                } else {
                    $remain = $dueTime - time();
                }
                $job->remain = $remain;
                if ($remain < 7200 && $remain > 0) {
                    $job->cal_duedate = "Due soon";
                } elseif ($remain <= 0) {
                    $job->cal_duedate = "Over Due";
                } else {
                    $job->cal_duedate = Utils::countDuaDateHour($dueTime);
                }
//                //job mà finish trước thời hạn due thì ko hiện là over dua nữa.
                if ($job->status == 3 && $job->finish_time <= $dueTime) {
                    $job->cal_duedate = "";
                }
            }
            foreach ($comments as $cmt) {
                if ($cmt->job_id == $job->id) {
                    $job->comment = $cmt->comment;
                    break;
                }
            }
            if ($job->status == 3) {
                $done++;
            }

            $job->job_man_hour = "";
            $job->job_man_hour_unit = '';
            if ($job->man_hour != null) {
                $convert = Utils::convertHours($job->man_hour);
                $job->job_man_hour = round($convert["value"], 2);
                $job->job_man_hour_unit = $convert["unit"];
                if ($job->job_man_hour <= 1) {
                    if ($job->job_man_hour_unit == 'minutes') {
                        $job->job_man_hour_unit = 'minute';
                    } elseif ($job->job_man_hour_unit == 'hours') {
                        $job->job_man_hour_unit = 'hour';
                    }
                }
            }
        }
        $per = 0;
        if ($total > 0) {
            $per = round($done / $total * 100);
        }

//        usort($jobs, function($a, $b) {
//            return $a->status > $b->status;
//        });
        //ghi log view task
        $task = CampaignTasks::where("id", $request->task_id)->first();
        if ($task) {
            $task->log = Utils::timeToStringGmT7(time()) . " $user->user_name view" . PHP_EOL . $task->log;
            $task->save();
        }
        return response()->json(["status" => "success", "message" => "success", "data" => $jobs, "done" => $done, "total" => $total, "per" => $per]);
    }

    public function findJob(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.findJob|request=' . json_encode($request->all()));
        $job = CampaignJobs::where("id", $request->job_id)->where("del_status", 0)->first();
        if (!$job) {
            return response()->json(["status" => "danger", "message" => "not found", "data" => null]);
        }

        $job->log = Utils::timeToStringGmT7(time()) . " $user->user_name view" . PHP_EOL . $job->log;
        $job->save();
        $job->job_detail_html = null;
        if ($job->job_detail != null) {
            $task = CampaignTasks::where("id", $job->task_id)->first();
            $campaignStatic = CampaignStatistics::where("id", $task->campaign_id)->first();
            $jobDetails = json_decode($job->job_detail);
//            $total = count($jobDetails);
            $groupedData = [];

            foreach ($jobDetails as $item) {
                if (isset($item->group)) {
                    $groupedData[$item->group][] = $item;
                } else {
                    $groupedData[] = [$item];
                }
            }

            $html = "";
            foreach ($groupedData as $group => $items) {
                if (is_string($group)) {
                    $html .= '<fieldset class="fieldset-custom m-b-10 p-15">';
                    $html .= '<legend class="legend-custom">' . $group . '</legend>';
                }

                foreach ($items as $item) {
                    $result = isset($item->result) ? $item->result : '';
                    if ($item->name == 'engagement') {
                        $releaseInfo = $campaignStatic->release_info != null ? json_decode($campaignStatic->release_info) : null;
                        if ($releaseInfo != null) {
                            $result = $releaseInfo->engagement;
                        }
                    }
                    if ($item->name == 'spotify_id') {
                        $result = $campaignStatic->spotify_id;
                    }
                    if ($item->name == 'Promo keywords') {
                        $result = $campaignStatic->promo_keywords;
                    }
                    if ($item->name == 'Official Video') {
                        $result = $campaignStatic->official_video;
                    }
                    if ($item->name == 'Hook') {
                        $shortText = $campaignStatic->short_text != null ? json_decode($campaignStatic->short_text) : null;
                        if ($shortText != null && !empty($shortText->hook)) {
                            $result = $shortText->hook;
                            Log::info("result $result");
                        }
                    }
                    $result = htmlspecialchars($result, ENT_QUOTES);
                    if ($item->type == "checkbox") {
                        $checked = "";
                        if ($item->is_finish) {
                            $checked = "checked";
                        }
                        $checkName = $item->name;
                        if (!empty($item->link)) {
                            $checkName = "<a target='_blank' href='$item->link'>$item->name</a>";
                        }
                        $html .= "<div class='d-flex align-items-center'>
                                        <div class='checkbox checkbox-primary d-flex align-items-center checkbox-rounded custom-after'>
                                            <input class='checkbox-multi' type='checkbox' value='1' name='$item->id' $checked>
                                            <label class='m-b-18 p-l-0'></label>
                                        </div>
                                        <p class='w-100 m-0 truncate'>$checkName</p>
                                    </div>";
                    } elseif ($item->type == 'textbox' && !isset($item->group)) {
                        $html .= '<div class="form-group">';
                        $html .= '<label class="font-13">' . ucfirst($item->name) . ' <i class="fa fa-copy" onclick="copyInputValue(\'' . $item->id . '\')"></i> </label>';
                        $html .= '<input id="' . $item->id . '" class="form-control form-control-sm m-b-2" value="' . $result . '" placeholder="" type="text" name="' . $item->id . '"></div>';
                    } elseif ($item->type == 'textbox') {
                        $html .= '<div class="input-group m-b-2">';
                        $html .= '<span class="input-group-addon">' . $item->name . '</span>';
                        $html .= '<input type="text" id="' . $item->id . '" name="' . $item->id . '" class="form-control form-control-sm m-b-2" value="' . $result . '">';
                        $html .= '<span class="input-group-addon"><i class="fa fa-copy" onclick="copyInputValue(\'' . $item->id . '\')"></i></span></div>';
                    } elseif ($item->type == 'textarea') {
                        $html .= '<div class="form-group">';
                        $html .= '<label class="font-13">' . ucfirst($item->name) . '</label>';
                        $html .= '<textarea id="' . $item->id . '" name="' . $item->id . '" class="form-control" rows="5" spellcheck="false" style="line-height: 1.25;height: 100px">' . $result . '</textarea></div>';
                    }
                }

                if (is_string($group)) {
                    $html .= '</fieldset>';
                }
            }
            $job->job_detail_html = $html;
//            foreach ($jobDetails as $detail) {
//                $result = $detail->result;
//                if ($detail->name == 'engagement') {
//                    $releaseInfo = $campaignStatic->release_info != null ? json_decode($campaignStatic->release_info) : null;
//                    if ($releaseInfo != null) {
//                        $result = $releaseInfo->engagement;
//                    }
//                }
//                if ($detail->name == 'Promo keywords') {
//                    $result = $campaignStatic->promo_keywords;
//                }
//                if ($detail->type == "checkbox") {
//                    $checked = "";
//                    if ($detail->is_finish) {
//                        $checked = "checked";
//                    }
//                    $checkName = $detail->name;
//                    if (!empty($detail->link)) {
//                        $checkName = "<a target='_blank' href='$detail->link'>$detail->name</a>";
//                    }
//                    $job->job_detail_html .= "<div class='d-flex align-items-center'>
//                                        <div class='checkbox checkbox-primary d-flex align-items-center checkbox-rounded custom-after'>
//                                            <input class='checkbox-multi' type='checkbox' value='1' name='$detail->id' $checked>
//                                            <label class='m-b-18 p-l-0'></label>
//                                        </div>
//                                        <p class='w-100 m-0 truncate'>$checkName</p>
//                                    </div>";
//                } elseif ($detail->type == "textbox") {
//                    $button = "";
//                    if (!empty($detail->button)) {
//                        $button = "<a class='m-l-5'><i class='fa fa-plus m-l-5' onclick=\"addMoreField(this,$total,'$detail->id')\"></i></a>";
//                    }
//                    $job->job_detail_html .= "<div class='form-group'>
//                                                <label class='font-13'>" . ucwords($detail->name) . " <i class='fa fa-copy' onclick=\"copyInputValue('$detail->id')\"></i> </label>$button
//                                                <input id='$detail->id' class='form-control form-control-sm m-b-2' value='$result' placeholder='' type='text' name='$detail->id'>";
//
//
//                    if (isset($detail->extra) && is_array($detail->extra)) {
//                        foreach ($detail->extra as $extra) {
//                            $job->job_detail_html .= "<div class='input-container'>
//                            <input id='$extra->id' class='form-control form-control-sm m-b-5' value='$extra->result' type='text' name='$extra->id'>
//                            <button class='btn btn-danger btn-sm m-l-2' onclick='removeField(this,\"$detail->id\",\"$extra->id\")'>Remove</button>
//                        </div>";
//                        }
//                    }
//                    $job->job_detail_html .= "</div>";
//                } elseif ($detail->type == "textarea") {
//                    $job->job_detail_html .= "<div class='form-group'>
//                                                <label class='font-13'>" . ucwords($detail->name) . "</label>
//                                                    <textarea id='$detail->id' name='$detail->id' class='form-control' rows='5' spellcheck='false' style='line-height: 1.25;height: 100px'>$result</textarea>";
//                }
//            }
        }
        //chuyển đổi man-hours nếu có
        $job->job_man_hour = null;
        $job->job_man_hour_unit = 'minutes';
        if ($job->man_hour != null) {
            $convert = Utils::convertHours($job->man_hour);
            $job->job_man_hour = round($convert["value"], 2);
            $job->job_man_hour_unit = $convert["unit"];
        }
        // Tạo đối tượng DateTime với múi giờ GMT+7
        $date = new DateTime($job->created, new DateTimeZone('GMT+7'));
        // Đổi múi giờ của đối tượng DateTime sang GMT+0
        $date->setTimezone(new DateTimeZone('GMT+0'));
        // Chuyển đổi DateTime thành Unix timestamp
        $timestamp = $date->getTimestamp();
        $job->created_beauty = gmdate("M d, Y h:i A", $timestamp + 7 * 3600);
        $job->task_name = "";
        if ($job->job_code != null) {
            $task = CampaignTasks::find($job->task_id);
            if ($task) {
                $job->task_name = $task->title;
            }
        }
        return response()->json(["status" => "success", "message" => "success", "data" => $job]);
    }

    public function createOrUpdateJobs(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.createOrUpdateJobs|request=' . json_encode($request->all()));
        if ($request->job_title == null && $request->action_type != "quick") {
            return response()->json(["status" => "error", "message" => "The job title cannot be left blank"]);
        }

        if (isset($request->job_id)) {
            $job = CampaignJobs::find($request->job_id);
            if (!$job) {
                return response()->json(["status" => "error", "message" => "Not found job #$request->job_id"]);
            }
            if (!$request->is_admin_music) {
                if ($job->username != $user->user_name && $job->admin != $user->user_name) {
                    return response()->json(["status" => "error", "message" => "You are unable to alter the info of this job"]);
                }
            }
            //không cho thay đổi nếu thay job_code khác
            if (isset($request->job_code)) {
                if ($request->job_code != $job->job_code) {
                    return response()->json(["status" => "error", "message" => "You cannot modify the job code for this job"]);
                }
            }
            //khi sửa job template thì ko cho nhập nhiều user
            if (isset($request->username)) {
                if (count($request->username) > 1 || count($request->username) == 0) {
                    return response()->json(["status" => "error", "message" => "You may select only one user for updates"]);
                }
                if ($request->username[0] == "all_user" || $request->username[0] == "bassteam" || $request->username[0] == "zteam") {
                    return response()->json(["status" => "error", "message" => "Please choose a user when editing"]);
                }
            }
            $log = "";
            $memberNotify = [];
            $isUpdateDes = 0;
            $isUpdateDuedate = 0;
            //admin music, người tạo job  này sẽ dược sửa thông tin job 
            if (($request->is_admin_music || $job->admin == $user->user_name) && $request->action_type != "quick") {
                if (isset($request->job_type) && $request->job_type != $job->job_type) {
                    $job->job_type = $request->job_type;
                    $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update job_type = $job->job_type" . PHP_EOL;
                }
                if ($request->job_title != $job->name) {
                    $job->name = $request->job_title;
                    $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update name = $job->name" . PHP_EOL;
                }
                if (isset($request->username)) {
                    if ($request->username[0] != $job->username) {
                        $job->username = $request->username[0];
                        $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update username = $job->username" . PHP_EOL;
                    }
                }

                if ($request->job_des != $job->des) {
                    $isUpdateDes = 1;
                    $html = $request->job_des;
                    //xóa phần tử  <p><br></p> ở đầu
                    if (strpos($html, '<p><br></p>') === 0) {
                        $html = substr($html, strlen('<p><br></p>'));
                    }

                    // Xóa phần tử <p><br></p> ở cuối
                    if (strrpos($html, '<p><br></p>') === (strlen($html) - strlen('<p><br></p>'))) {
                        $html = substr($html, 0, strlen($html) - strlen('<p><br></p>'));
                    }
                    $job->des = $html;
                    $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update des" . PHP_EOL;
                }
                if (isset($request->job_priority)) {
                    $job->priority = $request->job_priority;
                }
                if ($request->job_man_hour != null) {
                    if ($request->job_man_hour != $job->man_hour) {
                        $hours = Utils::convertToHours($request->job_man_hour, $request->job_man_hour_unit);
                        $job->man_hour = $hours;
                        $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update man_hour = $hours" . PHP_EOL;
                    }
                }
                if ($request->job_duedate != $job->due_date) {
                    $job->due_date = $request->job_duedate;
                    $job->duadate_time = null;
                    if ($request->job_duedate != null) {
                        $unix_timestamp = strtotime($request->job_duedate) - $user->timezone * 3600;
                        $job->duadate_time = $unix_timestamp;
                    }
                    $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update due_date = $job->due_date" . PHP_EOL;
                    $isUpdateDuedate = 1;
                }
                if (isset($request->penalty)) {
                    $job->penalty = $request->penalty;
                }
                $members = [];

                if (isset($request->job_member)) {
                    if (json_encode($request->job_member) != $job->member) {
                        $members = Utils::deleteValueArray([$job->username, $job->admin], $request->job_member);
                        $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update member = " . json_encode($members) . PHP_EOL;
                        //chỉ gửi thông báo cho những người mới được cập nhật thêm vào
                        if ($job->member != null) {
                            $memberNotify = Utils::deleteValueArray(json_decode($job->member), $members);
                        } else {
                            $memberNotify = $members;
                        }
                        $job->member = json_encode($members);
                    }
                }
            }

            //xử lý job_detail
            $totalJobDetail = 0;
            $finishJobDetailNow = 0;
            $finishJobDetailOld = 0;
            $jobDetails = [];
            if ($job->job_detail != null) {
                $jobDetails = json_decode($job->job_detail);
                foreach ($jobDetails as $detail) {
                    $totalJobDetail++;
                    //đếm job detail đã hoàn thành trong db
                    if ($detail->is_finish) {
                        $finishJobDetailOld++;
                    }
                    if ($request->action_type != "quick") {
                        //cập nhật trạng thái hoàn thành mới
                        $key = $detail->id;
                        if (isset($request->$key)) {
                            if ($detail->type == 'checkbox') {
                                $detail->is_finish = 1;
                                $finishJobDetailNow++;
                            } elseif ($detail->type == 'textbox' || $detail->type == 'textarea') {
                                $detail->is_finish = 1;
                                $finishJobDetailNow++;
                                $detail->result = $request->$key;
                            }
                        } else {
                            $detail->is_finish = 0;
                            $detail->result = null;
                        }
                        if (isset($detail->extra) && is_array($detail->extra)) {
                            foreach ($detail->extra as $extra) {
                                $keyExtra = $extra->id;
                                if (isset($request->$keyExtra)) {
                                    $extra->result = $request->$keyExtra;
                                }
                            }
                        }
                    }
                }
                if ($request->action_type != "quick") {
                    $job->job_detail = json_encode($jobDetails);
                }
            }

            if (isset($request->job_status)) {
                //chỉ ghi log với trương hợp finised đầu tiên
                if ($request->job_status == 3) {
                    //kiểm tra xem đã hoàn thành hết sub tag chưa
                    //trường hợp tick vào dầu finish
                    if ($request->action_type == "quick") {
                        if ($finishJobDetailOld < $totalJobDetail) {
                            return response()->json(["status" => "error", "message" => "You must complete Job Detail first"]);
                        }
                    } else {
                        //trường hợp submit form chọn status select
                        if ($finishJobDetailNow < $totalJobDetail) {
                            return response()->json(["status" => "error", "message" => "You must complete Job Detail first"]);
                        }
                    }
                    if ($job->status != 3) {
                        //ghi log task khi chuyển trạng thái thành finished
                        $check = CampaignTasksHistory::where("job_id", $request->job_id)->where("type", 1)->first();
                        if (!$check) {
                            $commnent = new CampaignTasksHistory();
                            $commnent->type = 1;
                            $commnent->task_id = $job->task_id;
                            $commnent->job_id = $request->job_id;
                            $commnent->username = $user->user_name;
                            $commnent->receive = $job->admin;
                            $commnent->date = gmdate("M d, Y", time() + 7 * 3600);
                            $commnent->time = gmdate("h:i A", time() + 7 * 3600);
                            $commnent->created = time();
                            $commnent->content = "$user->user_name has completed job #$request->job_id $job->name";
                            $commnent->title = "completed job";
                            $commnent->save();
                            //đánh dấu thời gian bấm finish job để so với thời gian due date xem có hoàn thành đúng thời hạn ko.
                            $job->finish_time = time();
                        }
                    }
                } elseif ($request->job_status == 0 && $job->status == 3) {
                    //trường hợp mở lại job đã hoàn thành
                    $commnent = new CampaignTasksHistory();
                    $commnent->type = 1;
                    $commnent->task_id = $job->task_id;
                    $commnent->job_id = $request->job_id;
                    $commnent->username = $user->user_name;
                    $commnent->receive = $job->admin;
                    $commnent->date = gmdate("M d, Y", time() + 7 * 3600);
                    $commnent->time = gmdate("h:i A", time() + 7 * 3600);
                    $commnent->created = time();
                    $commnent->content = "$user->user_name change status to new #$request->job_id $job->name";
                    $commnent->title = "change status to new";
                    $commnent->save();
                    $job->finish_time = null;
                    $job->save();
                }

                //nếu status thay đổi thì update
                if ($request->job_status != $job->status) {
                    $job->status = $request->job_status;
                    $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update status = $request->job_status" . PHP_EOL;
                    //nếu thay đổi thành on hold thì remove due date
                    if ($request->job_status == 2) {
                        $job->due_date = null;
                        $job->duadate_time = null;
                    }

                    //thông báo đã hoàn thành job
                    $receives = [];
                    if ($job->status == 3) {
                        $messStatus = "Finished";
                    } elseif ($job->status == 2) {
                        $messStatus = "On hold";
                    } elseif ($job->status == 1) {
                        $messStatus = "Processing";
                    } elseif ($job->status == 0) {
                        $messStatus = "Open back";
                    }
                    $history = CampaignTasksHistory::where("type", 3)
                                    ->where("job_id", $job->id)->where("receive", $job->admin)
                                    ->where("username", $user->user_name)->first();
                    if (!$history) {
                        $history = new CampaignTasksHistory();
                        $history->type = 3;
                        $history->task_id = $job->task_id;
                        $history->job_id = $job->id;
                        $history->username = $user->user_name;
                        $history->receive = $job->admin;
                        $history->created = time();
                        $history->content = $messStatus;
                        $history->title = $messStatus;
                        $history->save();
                        $receives[] = $job->admin;
                    }
                    $data = (object) [
                                "type" => 2,
                                "job_id" => $job->id,
                                "title" => $job->name,
                                "message" => $messStatus,
                                "comment" => null,
                                "redirect" => "/calendar?jid=$job->id",
                                "noti_id" => uniqid()
                    ];
                    event(new ChatEvent($user, $receives, $data));
                }



                //xử lý fulfillment checklist bên CampaignStatistics, tự động chuyển trạng thái
                $task = CampaignTasks::where("id", $job->task_id)->first();
                if ($task->campaign_id != null && $job->job_code != null) {
                    $campaign = CampaignStatistics::where("id", $task->campaign_id)->first();
                    if ($campaign) {
                        $checkList = CampaignChecklists::where("status", 1)->where("key", $job->job_code)->first();
                        $isFinish = $job->status == 3 ? 1 : 0;
                        $key = $job->job_code;
                        if (($campaign->check_lists == null || $campaign->check_lists == '[]')) {
                            $keys = CampaignChecklists::where("status", 1)->where("is_fulfillment", 1)->orderBy("location_order")->orderBy("job_order")->get();
                            $fulfillment = $this->makeChecklistJson($keys);
                        } else {
                            $fulfillment = json_decode($campaign->check_lists);
                        }
                        //cập nhật sang fullfillment , tính trường hợp thêm checklist template
                        $exists = 0;
                        foreach ($fulfillment as $full) {
                            if ($full->key == $key) {
                                $full->job_id = $job->id;
                                $full->is_finish = $isFinish;
                                $full->job_detail = $jobDetails;
                                $exists = 1;
                                break;
                            }
                        }
                        if (!$exists) {
                            $details = [];
                            foreach ($jobDetails as $index => $detail) {
                                $tmp = (object) [
                                            "id" => "$key$index",
                                            "type" => $detail->type,
                                            "name" => $detail->name,
                                            "result" => $detail->result,
                                            "is_finish" => $detail->is_finish
                                ];
                                if (isset($detail->extra)) {
                                    $tmp->extra = $detail->extra;
                                }
                                $details[] = $tmp;
                            }
                            $fulfillment[] = (object) [
                                        "key" => $key,
                                        "job_id" => $job->id,
                                        "type" => $checkList->type,
                                        "is_finish" => $isFinish,
                                        "result" => "",
                                        "job_detail" => $details
                            ];
                        }
                        $campaign->check_lists = json_encode($fulfillment);
                        $campaign->save();
                    }
                }
            }

            if ($request->action_type != "quick") {
                if ($request->job_result != $job->result) {
                    $job->result = $request->job_result;
                    $log .= Utils::timeToStringGmT7(time()) . " $user->user_name update result = $request->job_result" . PHP_EOL;
                                        // Tạo thông báo Result updated
                    $receives = ($job->member != null && $job->member != '[]') ? json_decode($job->member) : [];
                    $receives[] = $job->username;
                    $receives[] = $job->admin;
                    $receives = Utils::deleteValueArray([$user->user_name], $receives);
                    
                    foreach ($receives as $mem) {
                        $history = new CampaignTasksHistory();
                        $history->type = 3;
                        $history->task_id = $job->task_id;
                        $history->job_id = $job->id;
                        $history->username = $user->user_name;
                        $history->receive = $mem;
                        $history->created = time();
                        $history->content = "Result updated";
                        $history->title = "Result updated";
                        $history->save();
                    }

                    $data = (object) [
                                "type" => 2,
                                "job_id" => $job->id,
                                "title" => $job->name,
                                "message" => "Result updated",
                                "comment" => null,
                                "redirect" => "/calendar?jid=$job->id",
                                "noti_id" => uniqid()
                    ];
                    event(new ChatEvent($user, $receives, $data));
                }
            }

            $job->log = $log . $job->log;
            $job->save();
            //thông báo cho người dùng nếu có update member
            if (count($memberNotify) > 0) {
                $mess = "You are a member";
                foreach ($memberNotify as $mem) {
                    $history = new CampaignTasksHistory();
                    $history->type = 3;
                    $history->task_id = $job->task_id;
                    $history->job_id = $job->id;
                    $history->username = $user->user_name;
                    $history->receive = $mem;
                    $history->created = time();
                    $history->content = $mess;
                    $history->title = $mess;
                    $history->save();
                }

                $data = (object) [
                            "type" => 2,
                            "job_id" => $job->id,
                            "title" => $job->name,
                            "message" => $mess,
                            "comment" => null,
                            "redirect" => "/calendar?jid=$job->id",
                            "noti_id" => uniqid()
                ];
                event(new ChatEvent($user, $memberNotify, $data));
            }
            //thông báo cho user và member nếu có update description
            if ($isUpdateDes || $isUpdateDuedate) {
                $receives = ($job->member != null && $job->member != '[]') ? json_decode($job->member) : [];
                $receives[] = $job->username;
                $receives[] = $job->admin;
                $receives = Utils::deleteValueArray([$user->user_name], $receives);
                if ($isUpdateDes) {
                    $mess = "Desc updated";
                } elseif ($isUpdateDuedate) {
                    $mess = "Duedate updated";
                }
                foreach ($receives as $mem) {
                    $history = new CampaignTasksHistory();
                    $history->type = 3;
                    $history->task_id = $job->task_id;
                    $history->job_id = $job->id;
                    $history->username = $user->user_name;
                    $history->receive = $mem;
                    $history->created = time();
                    $history->content = $mess;
                    $history->title = $mess;
                    $history->save();
                }

                $data = (object) [
                            "type" => 2,
                            "job_id" => $job->id,
                            "title" => $job->name,
                            "message" => $mess,
                            "comment" => null,
                            "redirect" => "/calendar?jid=$job->id",
                            "noti_id" => uniqid()
                ];
                event(new ChatEvent($user, $receives, $data));
            }


            return response()->json(["status" => "success", "message" => "success", "data" => $job]);
        } else {
            if (empty($request->username)) {
                return response()->json(["status" => "error", "message" => "Please select Assignees"]);
            }
            if (empty($request->job_type)) {
                return response()->json(["status" => "error", "message" => "Please select Job Type"]);
            }
            if ($request->job_man_hour == null) {
                return response()->json(["status" => "error", "message" => "Please enter Estimate"]);
            }
            $role = "16";
            if ($request->job_type == "design") {
                $role = "23";
            }
            if ($request->job_type == "dev") {
                $role = "24";
            }
            if ($request->job_type == "sale") {
                $role = "25";
            }

            $username = $this->getUser($request->username);
            $jobMember = [];
            if (isset($request->job_member)) {
                $jobMember = $this->getUser($request->job_member)->toArray();
            }


            $count = 0;
            foreach ($username as $u) {
                $members = [];
                if (!empty($jobMember)) {
                    $members = Utils::deleteValueArray([$u], $jobMember);
                }
                $check = CampaignJobs::where("task_id", $request->task_id)
                        ->where("job_type", $request->job_type)
                        ->where("username", $u)
                        ->where("status", 0)
                        ->where("del_status", 0);
                if (isset($request->job_code)) {
                    $check = $check->where("job_code", $request->job_code);
                } else {
                    $check = $check->where("name", trim($request->job_title));
                }
                $check = $check->first();
                if (!$check) {
                    $job = new CampaignJobs();
                    $job->admin = $user->user_name;
                    $job->task_id = $request->task_id;
                    $job->job_type = $request->job_type;
                    $job->name = trim($request->job_title);
                    $job->username = $u;
                    $job->member = json_encode($members);
                    $job->priority = $request->job_priority;
                    $job->status = $request->job_status;
                    $job->penalty = $request->penalty;
                    $job->created = Utils::timeToStringGmT7(time());
                    $job->year = gmdate("Y", time() + 7 * 3600);
                    $job->month = gmdate("m", time() + 7 * 3600);
                    if ($request->job_man_hour != null) {
                        $hours = Utils::convertToHours($request->job_man_hour, $request->job_man_hour_unit);
                        $job->man_hour = round($hours, 2);
                    }
                    if ($request->job_duedate != null) {
                        $job->due_date = $request->job_duedate;
                        $unix_timestamp = strtotime($request->job_duedate) - $user->timezone * 3600;
                        $job->duadate_time = $unix_timestamp;
                    }
                    $job->des = $request->job_des;
                    $job->save();
                    if (isset($request->job_code)) {
                        $checkList = CampaignChecklists::where("key", $request->job_code)->where("status", 1)->first();
//                        Log::info("checklis " . json_encode($checkList));
                        if ($checkList) {
                            $job->name = $checkList->name;
                            $job->job_code = $checkList->key;
                            $job->job_group = $checkList->location;
                            $job->tutorial = $checkList->des;
                            //lưu job detail
                            if ($checkList->job_detail != null) {
                                $details = json_decode($checkList->job_detail);
//                                Log::info("detail " . json_encode($details));
                                $tmps = [];
                                foreach ($details as $index => $detail) {
                                    $tmp = (object) [
                                                "id" => "$request->job_code$index",
                                                "type" => $detail->type,
                                                "name" => $detail->name,
                                                "created" => time(),
                                                "due_date" => $request->job_duedate,
                                                "result" => null,
                                                "is_finish" => 0
                                    ];
                                    // với dữ liệu Engagement và Promo Keywords thì lấy ở bên campaign đưa vào, mặc định là đã hoàn thành rồi
                                    if ($detail->name == 'engagement') {
                                        $tmp->is_finish = 1;
//                                    $releaseInfo = $campaignStatic->release_info != null ? json_decode($campaignStatic->release_info) : null;
//                                    if ($releaseInfo != null) {
//                                        $tmp->result = $releaseInfo->engagement;
//                                    }
                                    }
                                    if ($detail->name == 'Promo keywords') {
                                        $tmp->is_finish = 1;
//                                    $tmp->result = $campaignStatic->promo_keywords;
                                    }
                                    if ($detail->name == 'spotify_id') {
                                        $tmp->is_finish = 1;
                                    }
                                    if (!empty($detail->button)) {
                                        $tmp->button = $detail->button;
                                    }
                                    if (!empty($detail->link)) {
                                        $tmp->link = $detail->link;
                                    }
                                    if (!empty($detail->group)) {
                                        $tmp->group = $detail->group;
                                    }
                                    if (!empty($detail->style)) {
                                        $tmp->style = $detail->style;
                                    }
                                    $tmps[] = $tmp;
                                }
                                $job->job_detail = json_encode($tmps);
                            }
                            $job->save();
                        }
                    }
                    $job->save();
                    $count++;
                    //đưa ra notify
                    $members[] = $u;
                    foreach ($members as $mem) {
                        $history = new CampaignTasksHistory();
                        $history->type = 3;
                        $history->task_id = $job->task_id;
                        $history->job_id = $job->id;
                        $history->username = $user->user_name;
                        $history->receive = $mem;
                        $history->created = time();
                        $history->content = $mem == $u ? "New job" : "You are a member";
                        $history->title = $mem == $u ? "New job" : "You are a member";
                        $history->save();
                    }

                    $data = (object) [
                                "type" => 2,
                                "job_id" => $history->job_id,
                                "title" => $job->name,
                                "message" => $history->content,
                                "comment" => null,
                                "redirect" => "/calendar?jid=$history->job_id",
                                "noti_id" => uniqid()
                    ];
                    event(new ChatEvent($user, $members, $data));
                } else {
                    return response()->json(["status" => "error", "message" => "Job exists"]);
                }
            }

            return response()->json(["status" => "success", "message" => "Success $count jobs"]);
        }
    }

    public function cloneJob(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.deleteJob|request=' . json_encode($request->all()));
        if (!$request->is_admin_music) {
            return response()->json(["status" => "error", "message" => "You do not have permission to clone"]);
        }

        if (isset($request->job_id)) {
            $job = CampaignJobs::find($request->job_id);
            if (!$job) {
                return response()->json(["status" => "error", "message" => "Not found job $request->job_id"]);
            }
            $new = $job->replicate();
            $new->status = 0;
            $new->log = Utils::timeToStringGmT7(time()) . " $user->user_name clone from $job->id";
            $new->save();

            return response()->json(["status" => "success", "message" => "Success", "task_id" => $new->task_id]);
        }
        return response()->json(["status" => "error", "message" => "Not enough information"]);
    }

    public function deleteJob(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.deleteJob|request=' . json_encode($request->all()));

        if (isset($request->job_id)) {
            $job = CampaignJobs::find($request->job_id);
            if (!$job) {
                return response()->json(["status" => "error", "message" => "Not found job $request->job_id"]);
            }
            if ($request->is_admin_music || $job->admin == $user->user_name) {
                $job->del_status = 1;
                $job->log = Utils::timeToStringGmT7(time()) . " $user->user_name deleted" . PHP_EOL . $job->log;
                $job->save();
                CampaignTasksHistory::where("job_id", $request->job_id)->update(["del_status" => 1]);
                return response()->json(["status" => "success", "message" => "Success"]);
            } else {
                return response()->json(["status" => "error", "message" => "You do not have permission to delete"]);
            }
        }
        return response()->json(["status" => "error", "message" => "Not enough information"]);
    }

    public function updateJobDetail(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.updateJobDetail|request=' . json_encode($request->all()));
        $job = CampaignJobs::where("id", $request->job_id)->first();
        if ($job) {
            if ($job->job_detail) {
                $jobDetail = json_decode($job->job_detail);
                foreach ($jobDetail as $j) {
                    if ($j->id == $request->parent_id) {
//                        Log::info("vao " . json_encode($j));
                        if ($request->action == 'remove') {
                            if (!empty($j->extra)) {
                                $j->extra = array_filter($j->extra, function($extraItem) use ($request) {
                                    return $extraItem->id !== $request->child_id;
                                });

                                // Re-index the array to maintain the numeric indexes
                                $j->extra = array_values($j->extra);
                            }
                        } elseif ($request->action == 'add') {
//                            Log::info("action $request->action");
                            $ex = (object) [
                                        "id" => $request->child_id,
                                        "result" => $request->value,
                            ];
                            if (empty($j->extra)) {
                                $j->extra = [];
                            }
//                            Log::info("extra " . json_encode($j->extra));
                            $j->extra[] = $ex;
//                            Log::info("extra last" . json_encode($j->extra));
                        }
                    }
                }
//                Log::info(json_encode($jobDetail));
                $job->job_detail = json_encode($jobDetail);
                $job->save();
            }
        }
        return response()->json(["status" => "success"]);
    }

    public function getHistory(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.getHistory|request=' . json_encode($request->all()));
        $datas = DB::select("select a.*,b.avatar from campaign_tasks_history a, users b where a.type = 1 and a.task_id = $request->task_id  and a.username = b.user_name order by a.id desc limit 20");
        return response()->json(["status" => "success", "message" => "success", "data" => $datas]);
    }

    public function getComments(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.getComments|request=' . json_encode($request->all()));
        $datas = CampaignTasksHistory::where("job_id", $request->job_id)->where("type", 2)->where("del_status", 0)->orderBy("id", "asc")->take(100)->get();
//        $datas = DB::select("select a.*,b.avatar from campaign_tasks_history a, users b where a.type = 2 and del_status =0 and a.job_id = $request->job_id  and a.username = b.user_name order by a.id asc limit 100");
        foreach ($datas as $data) {
            $newReactions = [];
            if ($data->reactions != null) {
                $reactions = json_decode($data->reactions);
                foreach ($reactions as $reaction) {
                    $emoji = $reaction->emoji;
                    $username = $reaction->username;

                    // Nếu emoji đã tồn tại trong mảng mới, thêm username vào danh sách
                    if (array_key_exists($emoji, $newReactions)) {
                        $newReactions[$emoji][] = $username;
                    } else {
                        // Nếu emoji chưa tồn tại, tạo mới và thêm username vào danh sách
                        $newReactions[$emoji] = [$username];
                    }
                }
            }
            $data->new_reaction = (object) $newReactions;
        }
        return response()->json(["status" => "success", "message" => "success", "data" => $datas, "user" => $user->user_name]);
    }

    public function createComment(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.createComment|request=' . json_encode($request->all()));
        if ($request->job_comment == null) {
            return response()->json(["status" => "error", "message" => "Comments cannot be empty"]);
        }
//        $check = strip_tags($request->job_comment,"<p><br>");
        $check = strip_tags($request->job_comment, '<p><img><span><div>');
        if ($check == null || $check == "" || $check == "<p></p>") {
            return response()->json(["status" => "error", "message" => "Comments cannot be empty"]);
        }
        //lưu lại comment
        $job = CampaignJobs::where("id", $request->job_id)->first();
        $comment = new CampaignTasksHistory();
        $comment->type = 2;
        $comment->task_id = $job->task_id;
        $comment->job_id = $request->job_id;
        $comment->username = $user->user_name;
        $comment->date = gmdate("M d, h:i A", time() + 7 * 3600);
        $comment->created = time();
        $cmt = Utils::detectCommentUrl($request->job_comment);
//        Log::info(strip_tags($request->job_comment));
//        $cmt = Utils::detectCommentUrl($request->highlight);
        $comment->content = $cmt;


        $eventType = 1;
        //kiểm tra nội dung gửi đến
        $comment->title = "New Comment";
        if (Utils::containString($request->job_comment, "data:image")) {
            $comment->title = "New photo";
            $eventType = 3;
        }
        $comment->save();
        //gửi thông báo đến người có liên quan
        $data = (object) [
                    "type" => $eventType, //eventType=1:commment bt, eventType=2:thông báo cho người dùng,eventType=3:gui ảnh
                    "job_id" => $comment->job_id,
                    "title" => $job->name,
                    "message" => $comment->title == "New photo" ? $comment->title : $request->job_comment,
                    "comment" => $eventType == 1 ? $comment : null,
                    "redirect" => "/calendar?jid=$comment->job_id",
                    "noti_id" => uniqid()
        ];
        //nếu là commment tag thì chỉ thông báo cho người được tag,không thông báo cho những người còn lại
        $receive2 = $this->findTagUsername($request->job_comment);
        if (count($receive2) > 0) {
            foreach ($receive2 as $arr) {
                $new = $comment->replicate();
                $new->receive = $arr;
                $new->type = 3;
                $new->title = "Mentioned you";
                $new->save();
            }
            $data->message = "Mentioned you";
            event(new ChatEvent($user, $receive2, $data));
        } else {
            $temp = [];
            $temp[] = $job->admin;
            $temp[] = $job->username;
            //gửi tin thông báo đến tất cả mọi người, bao gồm cả member
            if ($job->member != null) {
                $members = json_decode($job->member);
                $temp = array_merge($temp, $members);
            }
            $receive3 = Utils::deleteValueArray([$user->user_name], $temp);
            foreach ($receive3 as $arr) {
                $new = $comment->replicate();
                $new->receive = $arr;
                $new->type = 3;
                $new->title = $comment->title;
                $new->save();
            }
            $data->message = $comment->title;
            event(new ChatEvent($user, $receive3, $data));
        }




        return response()->json(["status" => "success", "message" => "Success", "comment" => $comment]);
    }

    public function deleteComment(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.deleteComment|request=' . json_encode($request->all()));
        if ($request->is_admin_music) {
            $data = CampaignTasksHistory::find($request->id);
        } else {
            $data = CampaignTasksHistory::where("id", $request->id)->where("username", $user->user_name)->first();
        }
        if (!$data) {
            return response()->json(["status" => "error", "message" => "You do not have permission to delete this comment"]);
        }
        $data->del_status = 1;
        $log = Utils::timeToStringGmT7(time()) . " $user->user_name update del_status = 1" . PHP_EOL;
        $data->log = $log . $data->log;
        $data->save();
        //gửi pusher đến các người dùng khác để update
        $data = (object) [
                    "type" => 3, //eventType=1:commment bt, eventType=2:thông báo cho người dùng,eventType=3:gui ảnh
                    "job_id" => $data->job_id,
                    "title" => "",
                    "message" => "",
                    "comment" => null,
                    "redirect" => "",
                    "noti_id" => uniqid()
        ];
        $job = CampaignJobs::where("id", $data->job_id)->first();
        $temp = [];
        $temp[] = $job->admin;
        $temp[] = $job->username;
        //gửi tin pusher đến tất cả mọi người, bao gồm cả member
        if ($job->member != null) {
            $members = json_decode($job->member);
            $temp = array_merge($temp, $members);
        }
        $receive3 = Utils::deleteValueArray([$user->user_name], $temp);
        event(new ChatEvent($user, $receive3, $data));
        return response()->json(["status" => "success", "message" => "Success"]);
    }

    public function updateComment(Request $request) {
//        $locker = new Locker(99123);
//        $locker->lock();
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.deleteComment|request=' . json_encode($request->all()));
        $message = CampaignTasksHistory::where("id", $request->id)->first();
        $username = $user->user_name;
        if (!$message) {
            return response()->json(["status" => "error", "message" => "Not found comment"]);
        }
        $emoji = $request->emoji;

        // Lấy emoji reactions hiện tại
        $emojiReactions = $message->reactions != null ? json_decode($message->reactions) : [];

        // Tìm kiếm người dùng trong danh sách emoji reactions
        $userReactionIndex = null;
        foreach ($emojiReactions as $index => $reaction) {
            if ($reaction->username == $username) {
                $userReactionIndex = $index;
                break;
            }
        }

        if ($userReactionIndex !== null) {
            // Người dùng đã thả emoji trước đó
            if ($emojiReactions[$userReactionIndex]->emoji == $emoji) {
                // Nếu emoji mới giống emoji cũ thì xóa
                unset($emojiReactions[$userReactionIndex]);
            } else {
                // Nếu emoji mới khác emoji cũ thì cập nhật
                $emojiReactions[$userReactionIndex]->emoji = $emoji;
            }
        } else {
            // Người dùng chưa thả emoji lần nào, thêm mới
            $emojiReactions[] = ['username' => $username, 'emoji' => $emoji];
        }

        // Cập nhật emoji reactions cho tin nhắn
        $message->reactions = json_encode(array_values($emojiReactions)); // Đảm bảo các chỉ mục trong mảng là liên tục
        $message->save();

        //xử lý thống kê
        $newReactions = [];
        $reactions = json_decode($message->reactions);
        foreach ($reactions as $reaction) {
            $emoji = $reaction->emoji;
            $username = $reaction->username;

            // Nếu emoji đã tồn tại trong mảng mới, thêm username vào danh sách
            if (array_key_exists($emoji, $newReactions)) {
                $newReactions[$emoji][] = $username;
            } else {
                // Nếu emoji chưa tồn tại, tạo mới và thêm username vào danh sách
                $newReactions[$emoji] = [$username];
            }
        }

        return response()->json(["status" => "success", "message" => "Success", "reactions" => $newReactions]);
    }

    public function getChecklist(Request $request) {
        $user = Auth::user();
        $datas = CampaignChecklists::where("status", 1)->where("is_calendar", 1);
        if (isset($request->group)) {
            $datas = $datas->where("location", $request->group);
        }
        $datas = $datas->orderBy("location_order", "asc")->orderBy("job_order", "asc")->get();
        $defaultDualDate = gmdate("Y-m-d", time() + $user->timezone * 3600) . 'T17:30';
        if (isset($request->campaign_id) && isset($request->start)) {

            $jobs = DB::select("SELECT 
                                campaign_jobs.id AS job_id, 
                                campaign_jobs.task_id, 
                                campaign_jobs.job_group, 
                                campaign_jobs.job_code, 
                                campaign_jobs.name, 
                                campaign_tasks.id AS task_id, 
                                campaign_tasks.title, 
                                campaign_tasks.campaign_id, 
                                campaign_tasks.task_group, 
                                campaign_tasks.start
                            FROM 
                                campaign_jobs
                            JOIN 
                                campaign_tasks ON campaign_jobs.task_id = campaign_tasks.id
                            WHERE 
                                campaign_tasks.campaign_id = '$request->campaign_id' AND campaign_tasks.task_group = '$request->group' AND campaign_jobs.del_status = 0");
//            Log::info(json_encode($jobs));
            foreach ($datas as $data) {
                foreach ($jobs as $job) {
                    if ($data->key == $job->job_code) {
                        $data->name = "<span>$data->name</span>" . " <span class='badge badge-warning font-normal'>This job was created on $job->start<span>";
                        break;
                    }
                }
            }
        }
        return response()->json(["status" => "success", "message" => "success", "checklist" => $datas, 'default' => $defaultDualDate]);
    }

    //lấy thông tin thống kê job
    public function getJobStatistics(Request $request) {
        $user = Auth::user();
        $isAdmin = $request->is_admin_music;
        Log::info($user->user_name . '|CalendarController.getJobStatistics|request=' . json_encode($request->all()));
//        $firstDayOfMonth = gmdate("Y-m-d", strtotime($request->month));
//        $lastDayOfMonth = date("Y-m-t", strtotime($firstDayOfMonth));
//        $firstDayOfNextMonth = date("Y-m-d", strtotime("+1 month", strtotime($firstDayOfMonth)));
//        Log::info("$firstDayOfMonth $lastDayOfMonth $firstDayOfNextMonth");
//        $whereDate = " and start >= '$firstDayOfMonth' and start <= '$lastDayOfMonth' ";
        $today = date('Y-m-d');
        $startOfDay = strtotime($today . ' 00:00:00 GMT+7');
        $endOfDay = strtotime($today . ' 23:59:59 GMT+7');

        $startOfWeek = strtotime('monday this week GMT+7');
        $endOfWeek = strtotime('sunday this week GMT+7');


        $whereDate = " and start >= '$request->start' and (end < '$request->end' or end is null) ";

//        $where = "";
//        if (!$request->is_admin_music || $request->is_my_task == "true") {
//            $where .= " and username='$user->user_name' ";
//        }
        $whereAdmin = "";
        if ($request->is_admin_music) {
            if ($request->filter_assignees != null) {
                $filterAssignees = explode(",", $request->filter_assignees);
                $assignees = "('" . implode("','", $filterAssignees) . "')";
                $whereAdmin .= " (`username` in $assignees ";
                foreach ($filterAssignees as $ass) {
                    $whereAdmin .= " or member like '%$ass%' ";
                }
                $whereAdmin .= ")";
            }

            if ($request->filter_createdby != null) {
                $whereAdmin .= ($whereAdmin != "" ? "and" : "") . "";
                $createdby = implode("','", explode(",", $request->filter_createdby));
                $createdby = "('" . $createdby . "')";
                $whereAdmin .= " admin in $createdby ";
            }

            if ($whereAdmin != "") {
                $whereAdmin = " and (" . $whereAdmin . ") ";
            }
        } else {
            $whereAdmin .= " and (username='$user->user_name' or member like '%$user->user_name%') ";
        }
        $jobs = DB::select("select id, status, due_date,finish_time from campaign_jobs where task_id in (select id from campaign_tasks where del_status = 0 $whereDate) and del_status = 0 $whereAdmin");

        $dash = (object) ["all" => 0, 'notdone' => 0, 'finish' => 0, 'processing' => 0, 'duesoon' => 0, 'overdue' => 0, 'duetoday' => 0, 'dueweek' => 0, 'ad' => $isAdmin];
        foreach ($jobs as $job) {
            $dash->all = $dash->all + 1;
            if ($job->status == 3) {
                $dash->finish = $dash->finish + 1;
            }
            if ($job->status == 1) {
                $dash->processing = $dash->processing + 1;
            }
            if ($job->status == 0) {
                $dash->notdone = $dash->notdone + 1;
            }
            if ($job->due_date != null) {
                $dueTime = strtotime("$job->due_date GMT+7");
                if ($job->status == 3) {
                    if ($job->finish_time > $dueTime) {
                        $dash->overdue = $dash->overdue + 1;
                    }
                } else {
                    if (time() >= $dueTime) {
                        $dash->overdue = $dash->overdue + 1;
                    } else {
                        if (($dueTime - time()) <= 7200) {
                            $dash->duesoon = $dash->duesoon + 1;
                        }
                        if ($startOfDay <= $dueTime && $dueTime <= $endOfDay) {
                            $dash->duetoday = $dash->duetoday + 1;
                        }
                        if ($startOfWeek <= $dueTime && $dueTime <= $endOfWeek) {
                            $dash->dueweek = $dash->dueweek + 1;
                        }
                    }
                }
            }
        }
        return response()->json(["status" => "success", "message" => "success", "statistics" => $dash, "__ad" => $isAdmin]);
    }

    //dữ liệu calendar notify
    public function listCalendarNotify(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.listCalendarNotify|request=' . json_encode($request->all()));
        $datas = CampaignTasksHistory::where("receive", $user->user_name)->whereIn("type", [3])->where("del_status", 0)->where("created", ">", time() - (86400 * 60));
        if ($request->is_show_read == "false") {
            $datas = $datas->where("is_read", 0);
        }
        $datas = $datas->orderBy("id", "desc")->get();
        $count = 0;
        foreach ($datas as $data) {
            $data->created_text = Utils::calcTimeText($data->created);
            if ($data->is_read == 0) {
                $count++;
            }
        }
        return response()->json(["status" => "success", "message" => "success", "notify" => $datas, "count" => $count]);
    }

    public function updateCalendarNotify(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CalendarController.updateCalendarNotify|request=' . json_encode($request->all()));
        if (isset($request->job_id)) {
            if ($request->job_id == 0) {
                CampaignTasksHistory::where("receive", $user->user_name)->update(["is_read" => 1]);
            } else {
                CampaignTasksHistory::where("receive", $user->user_name)->where("job_id", $request->job_id)->update(["is_read" => 1]);
            }
        }
        if (isset($request->id)) {
            $notify = CampaignTasksHistory::where("id", $request->id)->first();
            if ($notify) {
                $notify->is_read = $notify->is_read == 1 ? 0 : 1;
                $notify->save();
            }
        }
        return response()->json(["status" => "success", "message" => "success"]);
    }

    public function getUserMention() {
        $tagUsers = $this->getTagUser();
        $highlightUser = [];
        foreach ($tagUsers as $tag) {
            $highlightUser[] = "<img data-user=\"$tag\" class='rounded-circle img-cover m-r-5 w-30px h-30px' src='/images/avatar/$tag.jpg'> $tag";
        }
        return $highlightUser;
    }

    //thống kê overdue
    public function statistics(Request $request) {
        DB::enableQueryLog();
        if ($request->is_admin_music) {
            $users = User::where("role", "like", "%26%")->where("status", 1)->get(["user_name", "last_activity", "role"]);
        } else {
            $users = User::where("role", "like", "%26%")->where("role", "not like", "%20%")->where("status", 1)->get(["user_name", "last_activity", "role"]);
        }

        $time = time();
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $type = $request->input('type', 'month');
        $startYear = strtotime("$year-01-01");
        $nextYear = $year + 1;
        $endYear = strtotime("$nextYear-01-01");

        $startYearMonth = $year;
        $startMonth = strtotime("$startYearMonth-$month-01");
        $nextMonth = $month + 1;
        if ($nextMonth == 13) {
            $nextMonth = 1;
            $startYearMonth++;
        }
        $endMonth = strtotime("$startYearMonth-$nextMonth-01");

        if ($type == "year") {
            $workdays = $this->getWorkingDays($year, null);
            $statistics = DB::select("SELECT YEAR(IFNULL(due_date, created)) as year,username,
                                    SUM(man_hour) as total_man_hour,
                                    SUM(CASE WHEN status = 3 THEN CASE WHEN (finish_time IS NULL AND duadate_time < $time) OR (finish_time IS NOT NULL AND finish_time > duadate_time) THEN ((100-penalty) / 100) * man_hour ELSE man_hour END ELSE 0 END) as adjusted_man_hour,
                                    COUNT(*) AS total_jobs,
                                    SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as completed_jobs,
                                    SUM(CASE WHEN (status = 3 and (finish_time <= duadate_time OR  duadate_time IS NULL)) THEN 1 ELSE 0 END) as completed_ontime_jobs,
                                    SUM(CASE WHEN status in (0,1) THEN 1 ELSE 0 END) as uncompleted_jobs,
                                    SUM(CASE WHEN (finish_time IS NULL AND duadate_time < $time) OR (finish_time IS NOT NULL AND finish_time > duadate_time) THEN 1 ELSE 0 END) AS overdue_jobs,
                                    ROUND(SUM(CASE WHEN (finish_time IS NULL AND duadate_time < $time)
                                                       OR (finish_time IS NOT NULL AND finish_time > duadate_time) THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS overdue_percentage
                                    FROM `campaign_jobs`
                                    WHERE `del_status` = 0
                                    and  YEAR(IFNULL(due_date, created)) = '$year'
                                    GROUP BY YEAR(IFNULL(due_date, created)),username");
            $notifis = DB::select("SELECT 
                                        receive,
                                        SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) AS is_read,
                                        COUNT(*) AS total
                                    FROM 
                                        campaign_tasks_history 
                                    where type =3 and del_status =0
                                    and created >= $startYear
                                    and created < $endYear
                                    GROUP BY 
                                        receive");
        } else {
            $workdays = $this->getWorkingDays($year, $month);
            $statistics = DB::select("SELECT YEAR(IFNULL(due_date, created)) as year,MONTH(IFNULL(due_date, created)) as month,username,
                                    SUM(man_hour) as total_man_hour,
                                    SUM(CASE WHEN status = 3 THEN CASE WHEN (finish_time IS NULL AND duadate_time < $time) OR (finish_time IS NOT NULL AND finish_time > duadate_time) THEN ((100-penalty) / 100) * man_hour ELSE man_hour END ELSE 0 END) as adjusted_man_hour,
                                    COUNT(*) AS total_jobs,
                                    SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as completed_jobs,
                                    SUM(CASE WHEN (status = 3 and (finish_time <= duadate_time OR  duadate_time IS NULL)) THEN 1 ELSE 0 END) as completed_ontime_jobs,
                                    SUM(CASE WHEN status in (0,1) THEN 1 ELSE 0 END) as uncompleted_jobs,
                                    SUM(CASE WHEN (finish_time IS NULL AND duadate_time < $time) OR (finish_time IS NOT NULL AND finish_time > duadate_time) THEN 1 ELSE 0 END) AS overdue_jobs,
                                    ROUND(SUM(CASE WHEN (finish_time IS NULL AND duadate_time < $time)
                                                       OR (finish_time IS NOT NULL AND finish_time > duadate_time) THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS overdue_percentage
                                    FROM `campaign_jobs`
                                    WHERE `del_status` = 0
                                    and MONTH(IFNULL(due_date, created)) ='$month'
                                    and  YEAR(IFNULL(due_date, created)) = '$year'
                                    GROUP BY YEAR(IFNULL(due_date, created)),MONTH(IFNULL(due_date, created)),username");
            $notifis = DB::select("SELECT 
                                        receive,
                                        SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) AS is_read,
                                        COUNT(*) AS total
                                    FROM 
                                        campaign_tasks_history 
                                    where type =3 and del_status =0
                                    and created >= $startMonth
                                    and created < $endMonth
                                    GROUP BY 
                                        receive");
        }
//        Log::info(DB::getQueryLog());
        $results = [];
        Log::info("workdays $workdays");
        foreach ($users as $user) {
            $user->efficiency = 0;
            $user->workdays = $workdays;
            $user->total_man_hour = 0;
            $user->adjusted_man_hour = 0;
            $user->total_jobs = 0;
            $user->completed_jobs = 0;
            $user->completed_ontime_jobs = 0;
            $user->uncompleted_jobs = 0;
            $user->overdue_jobs = 0;
            $user->overdue_percentage = 0;
            $user->completed_percentage = 0;
            $user->completed_on_time_percentage = 0;
            $user->total_notify = 0;
            $user->read_notify = 0;
            $user->read_notify_percentage = 0;
            $user->role_name = "";
            $roles = explode(",", $user->role);
            $user->username = $user->user_name;
            $user->last_act = Utils::calcTimeText($user->last_activity);
            if (in_array("20", $roles)) {
                $user->role_name = "admin";
            }
            if (in_array("16", $roles)) {
                $user->role_name = "bass";
            }
            if (in_array("23", $roles)) {
                $user->role_name = "design";
            }
            if (in_array("25", $roles)) {
                $user->role_name = "sale";
            }
            foreach ($notifis as $notify) {
                if ($user->user_name == $notify->receive) {
                    $user->total_notify = $notify->total;
                    $user->read_notify = $notify->is_read;
                    if ($notify->total > 0) {
                        $user->read_notify_percentage = round($notify->is_read / $notify->total * 100);
                    }
                }
            }
            foreach ($statistics as $stat) {
                if ($user->user_name == $stat->username) {
                    $user->total_man_hour = $stat->total_man_hour;
                    //cộng thêm điểm chuyên cần, tính bằng % đọc notify
                    $user->adjusted_man_hour = $stat->adjusted_man_hour + (10 * $user->read_notify_percentage / 100);
                    if ($workdays > 0) {
                        //1 ngày làm việc 7 tiếng
                        $user->efficiency = round($user->adjusted_man_hour / ($workdays * 7) * 100);
                    }
                    $user->total_jobs = $stat->total_jobs;
                    $user->completed_jobs = $stat->completed_jobs;
                    $user->completed_ontime_jobs = $stat->completed_ontime_jobs;
                    $user->overdue_jobs = $stat->overdue_jobs;
                    $user->uncompleted_jobs = $stat->uncompleted_jobs;
                    $user->overdue_percentage = $stat->overdue_percentage;
                    if ($stat->total_jobs > 0) {
                        $user->completed_percentage = round($stat->completed_jobs / $stat->total_jobs * 100);
                        $user->completed_on_time_percentage = round(($stat->completed_ontime_jobs) / $stat->total_jobs * 100);
                    }
                }
            }

            $results[] = $user;
        }

        foreach ($results as $result) {
            //trường hợp người làm nhiều việc sẽ được + thêm % hoàn thành công việc để công bằng hơn với người làm ít việc
            $weight_factor = 0.05; // hệ số trọng số cho mỗi công việc
            // Tính hệ số trọng số = số job hoàn thành đúng hạn * trọng số
            $job_weight = 1 + (($result->total_jobs - $result->overdue_jobs) * $weight_factor);
            $ratingNotifyValue = $this->getRating($result->read_notify_percentage);
            // Tính tỷ lệ hoàn thành công việc đã điều chỉnh
            $completed_on_time_percentage_adjust = $result->completed_on_time_percentage * $job_weight;
            // Đảm bảo tỷ lệ không vượt quá 100%
            $completed_on_time_percentage_adjust = min($completed_on_time_percentage_adjust, 100);
            $ratingJobValue = $this->getRating($completed_on_time_percentage_adjust);

            $finalRatingValue = (0.3 * $ratingNotifyValue) + (0.7 * $ratingJobValue);
            $finalRatingLabel = $this->getRatingLabel(round($finalRatingValue));
            $result->final_rating_value = $finalRatingValue;
            $result->final_rating_label = $finalRatingLabel;
//            Log::info("$result->username ratingNotifyValue=$ratingNotifyValue ratingJobValue=$ratingJobValue finalRatingValue=$finalRatingValue finalRatingLabel=$finalRatingLabel");
        }
//        Log::info(json_encode($results));
        usort($results, function($a, $b) {
            if ($a->efficiency == $b->efficiency) {
                return $b->total_jobs > $a->total_jobs;
            }
            return $b->efficiency > $a->efficiency;
        });

        return response()->json(['statistics' => $results]);
    }

    public function getUserDropdown() {
        $users = $this->getTagUser();
        $result = [];
        foreach ($users as $user) {
            $result[] = (object) [
                        "username" => $user,
                        "avatar" => "/images/avatar/$user.jpg"
            ];
        }
        return $result;
    }

    public function listJobs(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        $page = $request->get('page', 1);
        $perPage = 20;
        $query = DB::table('campaign_jobs as cj');
        $query->join('campaign_tasks as ct', 'cj.task_id', '=', 'ct.id');
        $query->where("cj.del_status", 0);

        $today = date('Y-m-d');
        $currTime = time();

        // Start of the day
        $startOfDay = strtotime($today . ' 00:00:00 GMT+7');
        $endOfDay = strtotime($today . ' 23:59:59 GMT+7');

        // Start of the week
        $startOfWeek = strtotime('monday this week GMT+7');
        $endOfWeek = strtotime('sunday this week GMT+7');

        // Start of the month
        $startOfMonth = strtotime('first day of this month GMT+7');
        $endOfMonth = strtotime('last day of this month 23:59:59 GMT+7');

        if ($request->filter == "today") {
            $query->whereRaw("(cj.duadate_time >= $startOfDay and cj.duadate_time  <= $endOfDay or (cj.duadate_time < $currTime and cj.status <> 3)) ");
        } elseif ($request->filter == "week") {
            $query->whereRaw("(cj.duadate_time >= $startOfWeek and cj.duadate_time  <= $endOfWeek or (cj.duadate_time < $currTime and cj.status <> 3)) ");
        } elseif ($request->filter == "month") {
            $query->whereRaw("(cj.duadate_time >= $startOfMonth and cj.duadate_time  <= $endOfMonth or (cj.duadate_time < $currTime and cj.status <> 3)) ");
        }
        $query->where('cj.status', '<>', 3);
        if ($request->has('hide_completed') && $request->hide_completed) {
//            $query->where('status', '<>', 3);
        }

//        if (!$request->is_admin_music) {
//            $query->whereRaw("(`username` = '$user->user_name' or member like '%$user->user_name%')");
//        }
        //2024/10/07 nếu là tk của darrell hoặc james thì cho nhìn thấy job của nhau
        $whereMore = "";

        if ($user->user_name == 'jamesmusic' || $user->user_name == 'darrell') {
            $whereMore = "or cj.username in ('jamesmusic','darrell') or cj.member like '%jamesmusic%' or cj.member like '%darrell%'";
        }
        $query->whereRaw("(cj.username = '$user->user_name' or cj.member like '%$user->user_name%' $whereMore)");
        $total = $query->count();
        $query->orderBy("cj.id", "desc");
        $query->select('cj.id', 'cj.status', 'cj.due_date', 'cj.finish_time', 'cj.man_hour', 'cj.member', 'cj.admin', 'cj.username', 'cj.name', 'cj.job_type', 'ct.title as task_name', 'ct.id as task_id');
        $items = $query->paginate($perPage, ['*'], 'page', $page);
        $done = 0;
        $listId = $query->pluck("cj.id");
        $comments = CampaignTasksHistory::whereIn("job_id", $listId)->where("type", 2)->where("del_status", 0)
                        ->select('job_id', \DB::raw('count(*) as comment'))->groupBy('job_id')->get();
//        Log::info(json_encode($comments));
//        Log::info(json_encode($listId));
//        Log::info(DB::getQueryLog());
        foreach ($items->items() as $job) {

            $job->cal_duedate = "";
            $job->duatime = "";
            $job->remain = 0;
            $job->comment = 0;
//            $job->name = htmlentities($job->name);
            if ($job->due_date != null) {
                //nếu tời gian finish time 
                $dueTime = strtotime("$job->due_date GMT+7");
                $job->duatime = gmdate("M d, h:i A", $dueTime + 7 * 3600);
                if ($job->finish_time != null) {
                    $remain = $dueTime - $job->finish_time;
                } else {
                    $remain = $dueTime - time();
                }
                $job->remain = $remain;
                if ($remain < 7200 && $remain > 0) {
                    $job->cal_duedate = "Due soon";
                } elseif ($remain <= 0) {
                    $job->cal_duedate = "Over Due";
                } else {
                    $job->cal_duedate = Utils::countDuaDateHour($dueTime);
                }
//                //job mà finish trước thời hạn due thì ko hiện là over dua nữa.
                if ($job->status == 3 && $job->finish_time <= $dueTime) {
                    $job->cal_duedate = "";
                }
            }
            foreach ($comments as $cmt) {
                if ($cmt->job_id == $job->id) {
                    $job->comment = $cmt->comment;
                    break;
                }
            }
            if ($job->status == 3) {
                $done++;
            }
            $job->job_man_hour = "";
            $job->job_man_hour_unit = '';
            if ($job->man_hour != null) {
                $convert = Utils::convertHours($job->man_hour);
                $job->job_man_hour = round($convert["value"], 2);
                $job->job_man_hour_unit = $convert["unit"];
                if ($job->job_man_hour <= 1) {
                    if ($job->job_man_hour_unit == 'minutes') {
                        $job->job_man_hour_unit = 'minute';
                    } elseif ($job->job_man_hour_unit == 'hours') {
                        $job->job_man_hour_unit = 'hour';
                    }
                }
            }
        }
        return response()->json([
                    'items' => $items->items(),
                    'nextPage' => $items->currentPage() + 1,
                    'hasMorePages' => $items->hasMorePages(),
                    'total' => $total
        ]);
    }

    public function getUser($selected) {
        $groupMapping = [
            'bassteam' => 28,
            'zteam' => 29,
            'all_user' => 26
        ];
        $groups = array_filter($selected, function ($value) use ($groupMapping) {
            return array_key_exists($value, $groupMapping);
        });

        $usernames = array_filter($selected, function ($value) use ($groupMapping) {
            return !array_key_exists($value, $groupMapping);
        });

        $groupIds = array_map(function ($group) use ($groupMapping) {
            return $groupMapping[$group];
        }, $groups);
//        DB::enableQueryLog();
//        $username = User::query()
//                ->where("status",1)
//                ->where(function ($query) use ($groupIds) {
//                    foreach ($groupIds as $groupId) {
//                        $query->orWhere('role', 'LIKE', "%{$groupId}%");
//                    }
//                })
//                ->orWhere(function ($query) use ($usernames) {
//                    if (!empty($usernames)) {
//                        $query->whereIn('user_name', $usernames);
//                    }
//                })
//                ->pluck("user_name");
        
//        $username = User::query()
//                ->where(function ($query) use ($groupIds, $usernames) {
//                    $query->where('status', 1) 
//                    ->where(function ($query) use ($groupIds) {
//                        foreach ($groupIds as $groupId) {
//                            $query->orWhere('role', 'LIKE', "%{$groupId}%");
//                        }
//                    })
//                    ->orWhere(function ($query) use ($usernames) {
//                        if (!empty($usernames)) {
//                            $query->whereIn('user_name', $usernames);
//                        }
//                    });
//                })
//                ->pluck("user_name");
//                
//                
$username = User::query()
    ->where('status', 1)
    ->where(function ($query) use ($groupIds, $usernames) {
        $query->where(function ($query) use ($groupIds) {
            foreach ($groupIds as $groupId) {
                $query->orWhere('role', 'LIKE', "%{$groupId}%");
            }
        })
        ->orWhereIn('user_name', $usernames);
    })
    ->pluck("user_name");         
//        Log::info(DB::getQueryLog());
        return $username;
    }

}
