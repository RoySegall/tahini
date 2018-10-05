<?php

namespace App\Plugins;

use App\Plugins\Authentication\AuthenticationPluginBase;

/**
 * {@inheritdoc}
 */
class Authentication extends PluginManagerBase
{

  /**
   * {@inheritdoc}
   */
    public function getNamespace() : string
    {
        return 'App\Plugins\Authentication';
    }

  /**
   * {@inheritdoc}
   */
    public function getAnnotationHandler() : string
    {
        return 'App\Plugins\Annotations\Authentication';
    }

  /**
   * {@inheritdoc}
   */
    public function negotiate() : PluginBase
    {
        $plugins = array_keys($this->getPlugins());

        foreach ($plugins as $id) {
          /** @var AuthenticationPluginBase $plugin */
            $plugin = $this->getPlugin($id);

            if ($plugin->validateUser()) {
                return $plugin;
            }
        }
    }
}
