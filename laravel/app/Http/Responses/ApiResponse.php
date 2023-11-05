<?php

namespace App\Http\Responses;

use Illuminate\Http\Response;

/**
 * A response class for returning JSON responses that contains the data, message, and status code.
 */
class ApiResponse extends Response
{

    /**
     * Create a new response instance.
     *
     * @param  mixed  $data    The data to be returned in the response.
     * @param  string $message The message to be returned in the response.
     * @param  int    $status  The status code to be returned in the response.
     * @param  array  $headers The headers to be returned in the response.
     * @return void
     */
    public function __construct(mixed $data, string $message, int $status = 200, array $headers = [])
    {
        parent::__construct([
            'data' => $data,
            'message' => $message,
            'status' => $status
        ], $status, $headers);
    }
}
