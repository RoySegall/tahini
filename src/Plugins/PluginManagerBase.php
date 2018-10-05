<?php

namespace App\Plugins;

use Doctrine\Common\Annotations\Reader;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * {@inheritdoc}
 */
abstract class PluginManagerBase implements PluginManagerInterface
{

  /**
   * @var Finder
   */
    protected $finder;

  /**
   * @var Reader
   */
    protected $annotationReader;

  /**
   * @var
   */
    protected $plugins;

  /**
   * @var ContainerInterface
   */
    protected $container;

  /**
   * PluginManagerBase constructor.
   *
   * @param Reader $annotationReader
   *  The ready service.
   * @param ContainerInterface $container
   *  The container service.
   */
    public function __construct(Reader $annotationReader, ContainerInterface $container)
    {
        $this->finder =  new Finder();
        $this->annotationReader = $annotationReader;
        $this->container = $container;
    }

  /**
   * {@inheritdoc}
   */
    public function getPlugins() : array
    {

        if ($this->plugins) {
            return $this->plugins;
        }

        // look on https://www.sitepoint.com/your-own-custom-annotations/
        $path = $this->convertNamespaceToPath($this->getNamespace());
        $this->finder->files()->in($path);

        foreach ($this->finder as $file) {
            $class = $this->getNamespace() . '\\' . $file->getBasename('.php');
            $annotation = $this->annotationReader->getClassAnnotation(
                new \ReflectionClass($class),
                $this->getAnnotationHandler()
            );

            if (!$annotation) {
                continue;
            }

            $this->plugins[$annotation->id] = [
            'name' => $annotation->name,
            'class' => $class,
            'annotation' => $annotation,
            ];
        }

        return $this->plugins;
    }

  /**
   * {@inheritdoc}
   */
    public function getPlugin(string $plugin_id) : PluginBase
    {

        if (!$this->plugins) {
          // No plugin was found.
            $this->getPlugins();
        }

        if (!in_array($plugin_id, array_keys($this->plugins))) {
            throw new \Exception("The plugins {$plugin_id} does not exists.");
        }

        foreach ($this->plugins as $id => $plugin) {
            if ($id == $plugin_id) {
                return $this->container->get($plugin['class']);
            }
        }
    }

  /**
   * {@inheritdoc}
   */
    public function convertNamespaceToPath(string $path) : string
    {
        $paths = explode('\\', $path);
        $paths[0] = getcwd() . '/src';

        return implode('/', $paths);
    }
}
