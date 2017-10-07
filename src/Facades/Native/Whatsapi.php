<?php 
namespace Gentrobot\Whatsapi\Facades\Native;

use WhatsProt;
use UnexpectedValueException;
use Gentrobot\Whatsapi\Media\Media;
use Gentrobot\Whatsapi\Clients\MGP25;
use Gentrobot\Whatsapi\MessageManager;
use Gentrobot\Whatsapi\Events\Listener;
use Gentrobot\Whatsapi\Sessions\Native\Session;

class Whatsapi extends Facade 
{	
	use ResourceTrait;

    public static function create()
    {
        if(!$session = static::$session)
        {
            $session = new Session;
        }

        if(!$config = static::$config)
        {
            throw new UnexpectedValueException("You must provide config details in order to use Whatsapi");
        }

        // Setup Account details.
        $debug     = $config["debug"];
        $log       = $config["log"];
        $account   = $config["default"];
        $storage   = $config["data-storage"];
        $nickname  = $config["accounts"][$account]["nickname"];
        $number    = $config["accounts"][$account]["number"];
        $nextChallengeFile = $config["challenge-path"] . "/phone-" . $number . "-next-challenge.dat";
        
        $whatsProt =  new WhatsProt($number, $nickname, $debug, $log, $storage);
        $whatsProt->setChallengeName($nextChallengeFile);
        
        $media = new Media($config['media-path']);
        $manager = new MessageManager($media);
        $listener = new Listener($session, $config);

        $whatsapi = new MGP25($whatsProt, $manager, $listener, $session, $config);

        if($eventListener = static::$listener)
        {
            $whatsapi->setListener($eventListener);
        }

        return $whatsapi;
    }
}