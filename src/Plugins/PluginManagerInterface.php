<?php

namespace App\Plugins;

/**
 * A plugin serve a small logic from a wide aspect of a buisness logic.
 *
 * For example, for authentication we ca use access token, cookie of IP. In this
 * case, the first one that return something would determine the user which hit
 * our API.
 *
 *
 * @package App\Plugins
 */
interface PluginManagerInterface
{

  /**
   * Get all the plugins which match a given namespace.
   *
   * @return array
   */
    public function getPlugins() : array;

  /**
   * @return string
   */
    public function getNamespace() : string;

  /**
   * Get the annotation handler.
   *
   * @return string
   */
    public function getAnnotationHandler() : string;

  /**
   * Get a single instance of a plugin.
   *
   * @param string $plugin_id
   *  The plugin ID.
   *
   * @return PluginBase
   * @throws \Exception
   */
    public function getPlugin(string $plugin_id) : PluginBase;

  /**
   * Getting the best plugin for the current task.
   *
   * Each plugin manager has a different logic on which plugin need to be pull
   * first - Authentication need to pull the first plugin which return a match,
   * mail plugin need to send according to what define in the DB etc. etc.
   */
    public function negotiate() : PluginBase;

  /**
   * Converting all the namespace of the plugins to a real path.
   *
   * @param string $path
   *  The namespace which the plugins need to sit.
   *
   * @return string
   */
    public function convertNamespaceToPath(string $path) : string;
}
