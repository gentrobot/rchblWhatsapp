<?php 
namespace Gentrobot\Whatsapi\Facades\Laravel;

use Illuminate\Support\Facades\Facade;

class Whatsapi extends Facade 
{
    protected static function getFacadeAccessor()
    {
        return 'Gentrobot\Whatsapi\Contracts\WhatsapiInterface';
    }
}