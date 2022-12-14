<?php


namespace app\models\domain\user;


use app\models\User;

class RestoreSoftDelete
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function execute(): bool
    {
        $this->user->updateAttributes([
            "status"=>User::STATUS_ACTIVE
        ]);
        return true;
    }
}