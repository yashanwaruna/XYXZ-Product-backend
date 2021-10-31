<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,            
            'description'   => $this->description,            
            'status'        => $this->status,
            'is_editable'   => \Auth::id() == $this->user->id || \Auth::user()->role_id == User::ROLE_ADMIN,
            'created_at'    => $this->created_at->format('d-m-Y h:i:s'),
            'user'          => new userResource($this->user),
            'comments'      => CommentResource::collection($this->comments)
        ];
    }
}