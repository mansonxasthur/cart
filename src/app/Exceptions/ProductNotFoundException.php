<?php


namespace App\Exceptions;



use Throwable;

class ProductNotFoundException extends \Exception
{
    public function __construct($productName, $code = 404, Throwable $previous = null)
    {
        $message = "Product with name {$productName} not found.";
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], $this->getCode());
    }
}
