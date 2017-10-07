<?php 
namespace Gentrobot\Whatsapi\Facades\Native;

use Gentrobot\Whatsapi\Sessions\SessionInterface;
use Gentrobot\Whatsapi\Contracts\ListenerInterface;

trait ResourceTrait
{	
    /**
     * SessionInterface implementation
     * 
     * @var Gentrobot\Whatsapi\Sessions\SessionInterface
     */
	protected static $session;

	/**
     * ListenerInterface implementation
     * 
     * @var \Gentrobot\Whatsapi\Contracts\ListenerInterface
     */
	protected static $listener;

	/**
	 * Config values
	 * 
	 * @var array
	 */
	protected static $config;

    /**
     * Sets the session manager
     * 
     * @param \Gentrobot\Whatsapi\Sessions\SessionInterface $session
     */
    public static function setSessionManager(SessionInterface $session)
    {
        static::$session = $session;
    }

    /**
     * Sets the event listener
     * 
     * @param Gentrobot\Whatsapi\Contracts\ListenerInterface $listener
     */
    public static function setEventListener(ListenerInterface $listener)
    {
    	static::$listener = $listener;
    }

    /**
     * Sets the config to use. See the example config file in Config/config.php
     * 
     * @param array $config
     */
    public static function setConfig(array $config)
    {
    	static::$config = $config;
    }
}