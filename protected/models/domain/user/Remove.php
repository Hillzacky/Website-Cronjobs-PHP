<?php


namespace app\models\domain\user;


use app\models\User;

class Remove
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @throws \yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function execute() : bool
    {
        $this->user->profile->unlinkAvatar();
        $this->user->delete();
        return true;
    }
}