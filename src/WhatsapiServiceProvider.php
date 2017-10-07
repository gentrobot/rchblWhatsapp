<?php 
namespace Gentrobot\Whatsapi;

use Config;
use WhatsProt;
use Illuminate\Support\ServiceProvider;

class WhatsapiServiceProvider extends ServiceProvider 
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $this->publishConfigFiles();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerWhatsProt();
        $this->registerEventListener();
        $this->registerMediaManager();
        $this->registerMessageManager();
        $this->registerSessionManager();
        $this->registerRegistrationTool();
        $this->registerWhatsapi();

        $this->mergeConfigFrom(__DIR__ . '/Config/config.php', 'whatsapi');
    }

    private function publishConfigFiles()
    {
        $this->publishes([
            __DIR__.'/Config/config.php' => config_path('whatsapi.php'),
        ], 'config');
    }

    private function registerWhatsProt()
    {        
        // Set up how the create the WhatsProt object when using MGP25 fork
        $this->app->singleton('WhatsProt', function ()
        {
            // Setup Account details.
            $debug     = Config::get("whatsapi.debug");
            $log       = Config::get("whatsapi.log");
            $storage   = Config::get("whatsapi.data-storage");
            $account   = Config::get("whatsapi.default");
            $nickname  = Config::get("whatsapi.accounts.$account.nickname");
            $number    = Config::get("whatsapi.accounts.$account.number");
            $nextChallengeFile = $storage . "/phone-" . $number . "-next-challenge.dat";

            $whatsProt =  new WhatsProt($number, $nickname, $debug, $log, $storage);
            $whatsProt->setChallengeName($nextChallengeFile);

            return $whatsProt;
        });
    }

    private function registerEventListener()
    {
        $this->app->singleton('Gentrobot\Whatsapi\Events\Listener', function($app)
        {   
            $session = $app->make('Gentrobot\Whatsapi\Sessions\SessionInterface');

            return new \Gentrobot\Whatsapi\Events\Listener($session, Config::get('whatsapi'));
        });
    }

    private function registerMediaManager()
    {
        $this->app->singleton('Gentrobot\Whatsapi\Media\Media', function($app)
        {   
            return new \Gentrobot\Whatsapi\Media\Media(Config::get('whatsapi.data-storage') . '/media');
        });
    }

    private function registerMessageManager()
    {
        $this->app->singleton('Gentrobot\Whatsapi\MessageManager', function($app)
        {   
            $media = $app->make('Gentrobot\Whatsapi\Media\Media');

            return new \Gentrobot\Whatsapi\MessageManager($media);
        });
    }

    private function registerSessionManager()
    {
        $this->app->singleton('Gentrobot\Whatsapi\Sessions\SessionInterface', function ($app)
        {
             return $app->make('Gentrobot\Whatsapi\Sessions\Laravel\Session');
        });
    }

    private function registerWhatsapi()
    {
        $this->app->singleton('Gentrobot\Whatsapi\Contracts\WhatsapiInterface', function ($app)
        {
             // Dependencies
             $whatsProt = $app->make('WhatsProt');
             $manager = $app->make('Gentrobot\Whatsapi\MessageManager');
             $session = $app->make('Gentrobot\Whatsapi\Sessions\SessionInterface');
             $listener = $app->make('Gentrobot\Whatsapi\Events\Listener');

             $config = Config::get('whatsapi');

             return new \Gentrobot\Whatsapi\Clients\MGP25($whatsProt, $manager, $listener, $session, $config);
        });

    }

    private function registerRegistrationTool()
    {
        $this->app->singleton('Gentrobot\Whatsapi\Contracts\WhatsapiToolInterface', function($app)
        {
            $listener = $app->make('Gentrobot\Whatsapi\Events\Listener');

            return new \Gentrobot\Whatsapi\Tools\MGP25($listener, Config::get('whatsapi.debug'), Config::get('whatsapi.data-storage'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Gentrobot\Whatsapi\Contracts\WhatsapiInterface', 'Gentrobot\Whatsapi\Contracts\WhatsapiToolInterface'];
    }
}