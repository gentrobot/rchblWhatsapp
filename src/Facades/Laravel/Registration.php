<?php 
namespace Gentrobot\Whatsapi\Facades\Laravel;

use Illuminate\Support\Facades\Facade;

class Registration extends Facade 
{
    protected static function getFacadeAccessor()
    {
        return 'Gentrobot\Whatsapi\Contracts\WhatsapiToolInterface';
    }
}