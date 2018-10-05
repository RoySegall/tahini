<?php

namespace App\Plugins\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Authentication
{

    public $id;

    public $name;
}
