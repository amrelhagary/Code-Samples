<?php

namespace Authenticate\Model;


use yellow\Models\Web\Configurable;


class GoogleOauth2 extends Configurable
{
    public static $ClientId;
    public static $Secret;
    public static $RedirectURL;
}
?>