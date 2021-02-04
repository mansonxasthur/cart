<?php


namespace App\Exceptions;



use App\Models\Currency;
use Throwable;

class InvalidCurrencyException extends \Exception
{
    public function __construct($code = 422, Throwable $previous = null)
    {
        $message = 'The selected currency is invalid. Valid currencies: ' . implode(', ', Currency::VALID_CURRENCIES) . '.';
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['currency' => $this->getMessage()], $this->getCode());
    }
}
