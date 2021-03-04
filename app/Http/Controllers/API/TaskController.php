<?php

namespace App\Http\Controllers\API;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; //Carbon Library is used when we want to manipulate Date and Time
use Dotenv\Store\File\Reader; // Dotenv is used to Manage Environment Variables in Node.js
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Task as TaskResource;
use App\Http\Controllers\API\BaseController as BaseController;

class TaskController extends BaseController
{

    /*Show All Tasks

    public function index()
    {
        $user = Auth::user();
        $tasks = $user->tasks()->latest()->get();

        return $this->sendResponse(TaskResource::collection($tasks), 'Retriverd Tasks Successfully');
    }
    */

    //  (1) ---->  Show Today's Ongoing Tasks
    public function ongoingTasks(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'today' => 'required|date_format:d-m-Y'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validate Error', $validator->errors());
        }

        $today = $request->today;

        $user = Auth::user();
        $startOfDay = Carbon::create($today)->startOfDay();
        $endOfDay = Carbon::create($today)->endOfDay();

        //---------- All Today's Ongoing/Incomplete Tasks ---------- //

        $tasks = $user->tasks()
            ->where('is_completed', false)
            ->where('status', 0)
            ->where('task_date', '>=', $startOfDay)
            ->where('task_date', '<=', $endOfDay)->latest()->get();

        return $this->sendResponse(TaskResource::collection($tasks), 'All Today_s Incomplete Tasks');


        //--------- All Today's Incomplete Tasks Transfered To Tomorrow ---------- //
        $inCompleteTasks = $user->tasks()
            ->where('is_completed', false)        // and/or ->where('status', 0)
            ->where('task_date', '<', $startOfDay)->get(); // or ->where('task_date', '>=', $endOfDay)->get();

        if (count($inCompleteTasks) > 0) {
            foreach ($inCompleteTasks as $task) {
                $task->task_date = Carbon::create($today);
                // $task->task_date = $today->addDay(1);
                $task->save();
                return $this->sendResponse(new TaskResource($task), 'Incomplete Tasks Successfully Reported to Tomorrow');
            }
            return 'Task Already Completed Today';
        }

        /*----- All Today's Incomplete Tasks Transfered To Tomorrow(2nd method) -----

         public function incompleteToday($id)
         {
           $task = Task::find($id);

        if (is_null($task)) {
            return $this->sendError('No Task Found');
        }

        if ($task->user_id != Auth::id()) {
         // return $this->sendError('You are NOT Allowed to do this action !!', $validator->errors());
    //or//  return $this->sendError('Please Check your Auth and Try again', ['error' => 'Unauthorized']);
        }

        $dt=Carbon::now()->toDateString();
        if(Carbon::parse($dt)->gte($task->task_date)){   //gte= greater than or equal !!!
            $task->is_completed = false; // ongoing Task Incomplete
     // or  $task->status=0;
            $task->task_date = $today->addDays(1);
            $task->save();
            return $this->sendResponse(new TaskResource($task), 'Incomplete Tasks Successfully Reported to Tomorrow');
        }
         else{
             return 'Task Already Completed Today';
         }
    }
            ** -----------------------------------------------------------------**  */
    }


    //  (2) ----> Show Today's Complete Tasks Function
    public function completeTasks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'today' => 'required|date_format:d-m-Y'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validate Error', $validator->errors());
        }

        $today = $request->today;
        $user = Auth::user();
        $startOfDay = Carbon::create($today)->startOfDay();
        $endOfDay = Carbon::create($today)->endOfDay();
        $tasks = $user->tasks()
            ->where('is_completed', true)
            //->where('status', 1)
            ->where('task_date',  '>=', $startOfDay)
            ->where('task_date', '<=', $endOfDay)->latest()->get();

        return $this->sendResponse(TaskResource::collection($tasks), ' All Complete Tasks Successfully Retriverd');
    }


    //  (3) ----> Show Tomorrow Tasks
    public function tomorrowTasks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'today' => 'required|date_format:d-m-Y'
            // 'tomorrow'  => 'required|date_format:Y-m-d\TH:i:sP'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validate Error', $validator->errors());
        }

        $today = $request->today;

        $user = Auth::user();
        $endOfDay = Carbon::create($today)->endOfDay();
        $tasks = $user->tasks()->where('is_completed', false)
            ->where('task_date', '>', $endOfDay)->latest()->get();

        /*
             $tomorrow = $request->tomorrow;
        $user = Auth::user();
        $startOfDay = Carbon::create($tomorrow)->startOfDay();
        $endOfDay = Carbon::create($tomorrow)->endOfDay();
        $tasks = $user->tasks()->where('status', 0)
            ->where('task_date',  '>=', $startOfDay)
            ->where('task_date', '<=', $endOfDay)->latest()->get();
             */
        return $this->sendResponse(TaskResource::collection($tasks), 'All Tomorrow_s Incomplete Tasks');
    }


    // (4) ----> Add New Task in Today's Tasks
    public function newTodayTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'today' => 'required|date_format:Y-m-d\TH:i:sP' // W3S Format Time
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validate Error', $validator->errors());
        }

        $input['user_id'] = Auth::id();
        $input['title'] = $request->title;
        if ($request->has('content')) $input['content'] = $request->content;
        $input['task_date'] = $request->today;

        $task = Task::create($input);
        return $this->sendResponse($task, 'New Task Successfully Added For Today');
    }

    // (5) ----> Add New Task in Tomorrow's Tasks
    public function newTomorrowTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'tomorrow' => 'required|date_format:Y-m-d\TH:i:sP' // W3S Format Time
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validate Error', $validator->errors());
        }

        $input['user_id'] = Auth::id();
        $input['title'] = $request->title;
        if ($request->has('content')) $input['content'] = $request->content;
        $input['task_date'] = $request->tomorrow;

        $task = Task::create($input);
        return $this->sendResponse($task, 'New Task Successfully Added For Tomorrow');
    }



    // (6) ----> Function To Change Task Status (Completed or Not)
    public function changeStatusTask($id)
    {
        $task = Task::find($id);
        if (!is_null($task)) {
            if ($task->user_id === Auth::id()) {
                if ($task->is_completed == false) {
                    $task->is_completed = true;
                } else {
                    $task->is_completed = false;
                }
                $task->save();
                //   return $this->sendResponse(new TaskResource($task), 'Task Successfully Changed Status');
                return $this->sendResponse($task->is_completed == true ? 'Task Completed' : 'Task Not Completed Yet', 'Task Successfully Changed Status');
            } else {
                return $this->sendError('You Are Not Allowed To Do This Action');
            }
        } else {
            return $this->sendError('The Task Can Not be Found');
        }
    }

    //  (7) ---->Send Tasks To Tomorrow List
    public function goTomorrow(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'today' => 'required|date_format:d-m-Y'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validate Error', $validator->errors());
        }

        $today = $request->today;

        $task = Task::find($id);
        if (!is_null($task)) {
            if ($task->user_id === Auth::id()) {
                // $today = Carbon::today(); depends On The Server
                $task->task_date = Carbon::create($today)->addDay();
                $task->save();
                return $this->sendResponse([], 'Task Successfully Sent To Tomorrow List');
                // return $this->sendResponse(new TaskResource($task), 'Task Successfully Sent To Tomorrow List');
            } else {
                return $this->sendError('You Are Not Allowed To Do This Action');
            }
        } else {
            return $this->sendError('Task Can Not Be Found');
        }
    }


    //  (8) ----> Bring Back A Task To Today List
    public function backTaskToday(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'today' => 'required|date_format:d-m-Y'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validate Error', $validator->errors());
        }

        $today = $request->today;
        //  $tomorrow = $request->tomorrow;
        $task = Task::find($id);
        if (!is_null($task)) {
            if ($task->user_id === Auth::id()) {
                // $tomorrow = Carbon::today()->addDay(); // Depends On The Server Time
                //  $task->task_date = Carbon::createFromFormat('Y-m-d h:i:s', $tomorrow)->subDay();
                $task->task_date = Carbon::create($today);
                $task->save();

                return $this->sendResponse([], 'Task Successfully Transfered To Today');
                //      return $this->sendResponse(new TaskResource($task), 'Task Successfully Transfered To Today ');

            } else {
                return $this->sendError('You Do Not Have Access Right To This Task');
            }
        } else {
            return $this->sendError('Task Can Not Be Found');
        }
    }


    // (9) ----> Create Any New Task
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'today' => 'required|date_format:d-m-Y'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validate Error', $validator->errors());
        }

        $today = Carbon::create($request->today);
        $input['user_id'] = Auth::id();
        $input['title'] = $request->title;
        $input['task_date'] = $today;
        $input['is_completed'] = false;

        $task = Task::create($input);
        return $this->sendResponse(new TaskResource($task), 'Task Successfully Created');

        /*or we can use this method:
        $input['user_id'] = Auth::id();
        $input['title'] = $request->title;
        if ($request->has('content')) $input['content'] = $request->content;
        $input['task_date'] = $request->today; we can put date in case we choose a different day from the calendar !!!
        $input['created_at'] = $request->today;
        $task = Task::create($input);
        return $this->sendResponse($task, 'Task Successfully Created'); */
    }


    // (10) ----> Show Any Task
    public function show($id)
    {
        $task = Task::find($id);
        if (!is_null($task)) {
            if ($task->user_id === Auth::id()) {
                return $this->sendResponse(new TaskResource($task), 'Task Successfully Shown');
            } else {
                return $this->sendError('You Do Not Have Access Right To This Task');
            }
        } else {
            return $this->sendError('This Task Can Not Be Found');
        }
    }


    // (11) ----> Update  Any Task
    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        if (!is_null($task)) {
            $validatar = Validator::make($request->all(), [
                'title' => 'required'
            ]);

            if ($validatar->fails()) {
                return $this->sendError('Validate Error', $validatar->errors());
            }

            if ($task->user_id === Auth::id()) {
                $task->title = $request->title;
                $task->save();
                return $this->sendResponse([], 'Task Successfully Updated');
            } else {
                return $this->sendError('You Do Not Have Access Right To This Task');
            }
        } else {
            return $this->sendError('This Task Can Not Be Found');
        }
    }


    // (12) ----> Delete Any Task
    public function destroy($id)
    {
        $task = Task::find($id);
        if (!is_null($task)) {
            if ($task->user_id === Auth::id()) {
                $task->delete();
                return $this->sendResponse([], 'Task Successfully Deleted');
            } else {
                return $this->sendError('You are NOT Allowed to do this action !!');
            }
        } else {
            return $this->sendError('This Task Can Not Be Found');
        }
    }
}
