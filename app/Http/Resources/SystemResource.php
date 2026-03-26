<?php

namespace App\Http\Resources;

use App\Http\Resources\ProgressResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemResource extends JsonResource
{
      public function toArray($request)
    {
        $categoriesGrouped = $this->progress->groupBy('category_id');
        
        $categories = $categoriesGrouped->map(function ($progressItems) {
            $category = $progressItems->first()->category;
            
            return [
                'categoryName' => $category->name?? NULL,
                'progress' => ProgressResource::collection($progressItems)
            ];
        })->values();

        return [
            'id' => $this->id,
            'systemName' => $this->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'team' => TeamResource::collection($this->whenLoaded('team')),
            'categories' => $categories
        ];
    }
}
