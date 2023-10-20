<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class PermissionDeniedResponse extends JsonResponse
{
    public function __construct($message = 'You do not have permission to perform this action', $status = 403,)
    {
        parent::__construct(
            ['success' => false, 'message' => $message],
            $status,
        );
    }
}
