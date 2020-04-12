<?php

namespace App\Http\Controllers;

use App\Folder;
use App\Task;
use Illuminate\Http\Request;
use App\Http\Requests\CreateTask;
use App\Http\Requests\EditTask;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Folder $folder){

        // if (Auth::user()->id !== $folder->user_id) {
        //     abort(403);
        // }

        // ユーザーのフォルダを取得する
        $folders = Auth::user()->folders()->get();

        // 選ばれたフォルダを取得
        // $current_folder = Folder::find($id);
        // $current_folder = Auth::user()->folders()->find($id);

        // if (is_null($current_folder)) {
        //     abort(404);
        // }

        // 選ばれたフォルダに紐づくタスクを取得
        // $tasks = $current_folder->tasks()->get();
        $tasks = $folder->tasks()->get();

        // dd($folders);
        // dd($folder->id);
        // dd($tasks);

        return view('tasks/index',[
            'folders' => $folders,
            'current_folder_id' => $folder->id,
            'tasks' => $tasks,
        ]);
    }

    public function showCreateForm(Folder $folder){
        return view('tasks/create',[
            'folder_id' => $folder->id,
        ]);
    }

    public function create(Folder $folder, CreateTask $request){
        
        // フォルダモデルのインスタンスを作成する
        $task = new Task();
        // タイトルに入力値を代入する
        $task->title = $request->title;
        $task->due_date = $request->due_date;

        // インスタンスの状態をデータベースに書き込む
        $folder->tasks()->save($task);

        return redirect()->route('tasks.index', [
            'folder' => $folder->id,
        ]);
    }

    public function showEditForm(Folder $folder, Task $task){
        $this->checkRelation($folder, $task);
        // $task = Task::find($task_id);

        return view('tasks/edit', [
            'task' => $task,
        ]);
    }

    public function edit(Folder $folder, Task $task, EditTask $request){
        $this->checkRelation($folder, $task);
        // 1
        // $task = Task::find($task_id);

        // 2
        $task->title = $request->title;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->save();

        // 3
        return redirect()->route('tasks.index', [
            'folder' => $folder->id,
        ]);
    }

    public function checkRelation(Folder $folder, Task $task){
        if ($folder->id !== $task->folder_id) {
            abort(404);
        }
    }
}
