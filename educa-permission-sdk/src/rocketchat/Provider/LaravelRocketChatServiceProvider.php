<?php

namespace StuPla\CloudSDK\rocketchat\Provider;

use Illuminate\Support\ServiceProvider;
use StuPla\CloudSDK\rocketchat\Client\ChannelClient;
use StuPla\CloudSDK\rocketchat\Client\ChatClient;
use StuPla\CloudSDK\rocketchat\Client\GroupClient;
use StuPla\CloudSDK\rocketchat\Client\ImClient;
use StuPla\CloudSDK\rocketchat\Client\IntegrationClient;
use StuPla\CloudSDK\rocketchat\Client\LivechatClient;
use StuPla\CloudSDK\rocketchat\Client\RocketChatClient;
use StuPla\CloudSDK\rocketchat\Client\SettingClient;
use StuPla\CloudSDK\rocketchat\Client\UserClient;

class LaravelRocketChatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-rocket-chat.php' => config_path('laravel-rocket-chat.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravel-rocket-chat-client', RocketChatClient::class);
        $this->app->bind('rc-user-client', UserClient::class);
        $this->app->bind('rc-setting-client', SettingClient::class);
        $this->app->bind('rc-channel-client', ChannelClient::class);
        $this->app->bind('rc-group-client', GroupClient::class);
        $this->app->bind('rc-im-client', ImClient::class);
        $this->app->bind('rc-chat-client', ChatClient::class);
        $this->app->bind('rc-integration-client', IntegrationClient::class);
        $this->app->bind('rc-livechat-client', LivechatClient::class);
    }
}
