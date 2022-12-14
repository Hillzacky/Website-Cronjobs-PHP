<?php


namespace app\models\exceptions;


use Throwable;
use yii\base\Exception;

class DomainModelException extends Exception
{
    private array $errors = [];

    public function __construct(array $errors, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors() : array
    {
        return $this->errors;
    }

    public function getName()
    {
        return "Model Exception";
    }
}