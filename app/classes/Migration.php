<?php

declare(strict_types=1);

namespace App\classes;
abstract class Migration
{
    abstract public function up();

    abstract public function down();
}
