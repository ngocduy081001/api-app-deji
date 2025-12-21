<?php

namespace App\Handlers;

use UniSharp\LaravelFilemanager\Handlers\ConfigHandler as BaseConfigHandler;

class ConfigHandler extends BaseConfigHandler
{
    /**
     * Get the user field name for private folder
     * This will return "2" as the default folder name
     */
    public function userField()
    {
        return '2';
    }
}
