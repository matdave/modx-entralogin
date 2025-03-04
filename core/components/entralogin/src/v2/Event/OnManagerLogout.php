<?php

namespace MODX\EntraLogin\v2\Event;

use MatDave\MODXPackage\Elements\Event\Event;

class OnManagerLogout extends Event
{
    public function run()
    {
        if(isset($_SESSION['elog_access_token'])) {
            unset($_SESSION['elog_access_token']);
        }
    }
}