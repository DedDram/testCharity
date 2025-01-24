<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'message' => 'Donation successfully created',
            'donation' => [
                'id' => $this->id,
                'charity_project_id' => $this->charity_project_id,
                'amount' => $this->amount,
                'donation_date' => $this->donation_date,
                'comment' => $this->comment,
            ]
        ];
    }

    public static function failedResponse($exception): JsonResponse
    {
        return response()->json(['message' => 'Failed to create donation - '.$exception], 500);
    }

    public static function emptyResponse(): JsonResponse
    {
        return response()->json(['message' => 'No project found'], 404);
    }
}
