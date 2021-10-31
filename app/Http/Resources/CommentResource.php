<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {        
        return [
            'id'            => $this->id,            
            'description'   => $this->description,
            'created_at'    => $this->created_at->format('d-m-Y h:i:s') || \Auth::user()->role_id == User::ROLE_ADMIN,
            'is_editable'   => \Auth::id() == $this->user->id,
            'user'          => new userResource($this->user)
        ];
    }
}
