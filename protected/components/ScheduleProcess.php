<?php
namespace app\components;

use Symfony\Component\Process\Process;

class ScheduleProcess extends Process
{
    private $exceptionMessage;

    public function setExceptionMessage($message) {
        $this->exceptionMessage = $message;
    }

    public function isSuccessful(): bool
    {
        return (parent::isSuccessful() AND empty($this->exceptionMessage));
    }

    public function getExitCodeText(): string
    {
        return empty($this->exceptionMessage) ? parent::getExitCodeText() : (string) $this->exceptionMessage;
    }
}