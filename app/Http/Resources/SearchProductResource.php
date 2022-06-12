<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $thumb_img = '';
        $url = '';
        $discount_price = 0;
        $selling_price = 0;
        $stock = 0;
        $hasDiscount = 0;
        
        // dd($this);
        
        return [
            'product_name' => $this->name,
            'thumb_img' => $thumb_img,
            'selling_price' => $selling_price,
            'hasDiscount' => $hasDiscount,
            'discount_price' => $discount_price,
            'stock' => $stock,
            'url' => $url
        ];
        
    }
}
