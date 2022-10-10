<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
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
            'id' => $this->id,
            'name'=> $this->name,
            'description' => $this->description,
            'media_path' => $this->media_path,
            'barcode' => $this->barcode,
            'price' => $this->price,
            'status' => $this->status,
            'store_id' => $this->store_id,
            'category_id' => $this->category_id,
        ];
    }
}
