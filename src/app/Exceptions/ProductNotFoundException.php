<?php


namespace App\Exceptions;



use Throwable;

class ProductNotFoundException extends \Exception
{
    public function __construct($message = "Product not found.", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], $this->getCode());
    }
}
