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

use FurqanSiddiqui\BcMath\BcBaseConvert;
use FurqanSiddiqui\BcMath\BcNumber;

$base58Charset = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
$dec = "16412139222523533441826036571855191704233796980785479268813122696618653597816845269268365983";
var_dump($dec);

$dec = new BcNumber($dec);
$base58 = BcBaseConvert::fromBase10($dec, $base58Charset);
$base16 = BcBaseConvert::fromBase10($dec, BcBaseConvert::CHARSET_HEX);
$base16 = strtoupper($base16); // Changing to uppercase for testing case-sensitivity when converting back
$base2 = BcBaseConvert::fromBase10($dec, BcBaseConvert::CHARSET_BINARY);
$octal = BcBaseConvert::fromBase10($dec, BcBaseConvert::CHARSET_OCTAL);

var_dump($octal);
var_dump($base58);
var_dump($base16);
var_dump($base2);

var_dump(BcBaseConvert::BaseConvert($octal, 8, 16));

var_dump(BcBaseConvert::toBase10($base58, $base58Charset)->value());
var_dump(BcBaseConvert::toBase10($base16, BcBaseConvert::CHARSET_HEX, false)->value());
var_dump(BcBaseConvert::toBase10($base2, BcBaseConvert::CHARSET_BINARY)->value());