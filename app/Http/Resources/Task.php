<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Task extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        //we send only elements we want to send/show to the user (in this case element from DB: injazz ->  table: tasks) <=> return parent::toArray($request)
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'task_date' => $this->task_date,
            'status' => $this->status,
            'is_completed' => $this->is_completed,  //can replace status with boolean values
            'created_at' => $this->created_at->format('d/m/Y'), // can be also 'created_at' => $this->created_at->diffForhumans(),
            'updated_at' => $this->updated_at->format('d/m/Y'), //'created_at' and 'updated_at' we can send them or not depending if we want to show them to the user or not !!
        ];
    }
}
