<?php
namespace RateCard\Model;

class AuthService
{
    private $userTable;

    public function __construct(UserTable $userTable)
    {
        $this->userTable = $userTable;
    }

    public function checkAuth($userEmail,$userToken)
    {
        return $this->userTable->checkAuth($userEmail,$userToken);
    }
}