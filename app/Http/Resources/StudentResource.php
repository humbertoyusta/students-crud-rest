<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this -> id,
            'firstname' => $this -> firstname,
            'lastname' => $this -> lastname,
            'email' => $this -> email,
            'address' => $this -> address,
            'score' => $this -> score,
        ];
    }
}
