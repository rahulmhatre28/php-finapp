<?php

namespace App\Traits;


/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ApiResponser
{
    /**
     * Return a success JSON response.
     *
     * @param array|string $data
     * @param string $message
     * @param int|null $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data, string $message = null, int $code = 200)
    {
        return response()->json([
            'status' => true,
            'type' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return a success info JSON response.
     *
     * @param array|string $data
     * @param string $message
     * @param int|null $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function info($data, string $message = null, int $code = 200)
    {
        return response()->json([
            'status' => true,
            'type' => 'info',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param array|string|null $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = null, int $code, $data = null)
    {
        return response()->json([
            'status' => false,
            'type' => 'error',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return an validation error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param array|string|null $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validation_error($errors)
    {
        return response()->json([
            'status' => false,
            'type' => 'warning',
            'errors' => $errors,
        ], 200);
    }

}
