<?php
/**
 * This file is a part of "furqansiddiqui/bcmath-php" package.
 * https://github.com/furqansiddiqui/bcmath-php
 *
 * Copyright (c) 2019 Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/furqansiddiqui/bcmath-php/blob/master/LICENSE
 */

declare(strict_types=1);

require "../vendor/autoload.php";

// Grab BcMath instance
$bcMath = new FurqanSiddiqui\BcMath\BcMath();
$bcMath->scale(8); // Set default scale value

// Demo value
$float = 1.234e-2;

// BcNumber instance
$number = $bcMath->number($float)
    ->add(0.01)
    ->value();

var_dump($number); // string(10) "0.02234000"