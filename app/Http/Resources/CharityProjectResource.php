<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class CharityProjectResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'status' => $this->status,
            'launch_date' => $this->launch_date,
        ];
        // Добавляем сумму пожертвований, если это запрос из метода show
        if ($request->routeIs('charity-projects.show')) {
            $data['donation_amount'] = ceil($this->donation_amount / 100) * 100;
            $data['additional_description'] = $this->additional_description;
        }

        return $data;
    }

    /**
     * Return a successful response message.
     *
     * @return JsonResponse
     */
    public static function emptyResponse(): JsonResponse
    {
        return response()->json(['message' => 'No project found'], 404);
    }
}
