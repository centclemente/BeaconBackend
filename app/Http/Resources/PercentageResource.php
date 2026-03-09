<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PercentageResource extends JsonResource
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
 'total' => $totalProgress,
        'completed' => $completedProgress,
        'pending' => $totalProgress - $completedProgress,
        'holding' => Progress::where('status', 'hold')->count(),
        'percentage' => round($progressPercentage, 2) . '%',
        ];
    }
}
