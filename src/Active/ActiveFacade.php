<?php

namespace Rainwater\Active;

use Illuminate\Support\Facades\Facade;

class ActiveFacade extends Facade
{
    /**
     * The name of the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'active-users';
    }
}
