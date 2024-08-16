<?php
/*
 * This file is part of e3n/symfony/symfony-dev
 *
 * Copyright (c) e3n GmbH & Co. KG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/../.env');

$kernel = new Kernel('test', (bool)$_SERVER['APP_DEBUG']);
$kernel->boot();

return $kernel->getContainer();
