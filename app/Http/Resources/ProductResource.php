<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string|null $description
 * @property-read string|null $image
 * @property-read string $barcode
 * @property-read float $price
 * @property-read int $quantity
 * @property-read bool $status
 * @property-read \Illuminate\Support\Carbon $created_at
 * @method string getImageUrl()
 */
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
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'barcode' => $this->barcode,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'image_url' => $this->getImageUrl(),
        ];
    }
}
