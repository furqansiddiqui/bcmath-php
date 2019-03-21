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

namespace furqansiddiqui\BcMath;

/**
 * Class BcBaseConvert
 * @package furqansiddiqui\BcMath
 */
class BcBaseConvert
{
    public const CHARSET_BINARY = "01";
    public const CHARSET_OCTAL = "01234567";
    public const CHARSET_BASE16 = "0123456789abcdef";
    public const CHARSET_BASE36 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    public const CHARSET_BASE62 = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    public const CHARSET_HEX = self::CHARSET_BASE16;

    /**
     * @param int $base
     * @return string
     */
    public static function Charset(int $base): string
    {
        switch ($base) {
            case 2:
                return self::CHARSET_BINARY;
            case 8:
                return self::CHARSET_OCTAL;
            case 16:
                return self::CHARSET_BASE16;
            case 36:
                return self::CHARSET_BASE36;
            case 62:
                return self::CHARSET_BASE62;
        }

        throw new \InvalidArgumentException(sprintf('No charset found for base %d', $base));
    }

    /**
     * Converts between bases (using Base10 as middle)
     * @param string $value
     * @param int $fromBase
     * @param int $targetBase
     * @return string
     */
    public static function BaseConvert(string $value, int $fromBase, int $targetBase): string
    {
        if ($fromBase === $targetBase) { // Both from and target bases are, what's the point?
            return $value;
        }

        // Case sensitivity (primarily for Hexadecimals)
        $fromBaseIsCaseSensitive = true;
        if ($fromBase < 36) {
            $fromBaseIsCaseSensitive = false;
        }

        // No need to convert to decimals (Base10) if it already is Base10
        $decs = $fromBase === 10 ?
            new BcNumber($value) : self::toBase10($value, self::Charset($fromBase), $fromBaseIsCaseSensitive);

        return $targetBase === 10 ? $decs->value() : self::fromBase10($decs, self::Charset($targetBase));
    }

    /**
     * @param BcNumber $dec
     * @param string $charset
     * @return string
     */
    public static function fromBase10(BcNumber $dec, string $charset): string
    {
        if (!$dec->isInteger() || $dec->isNegative()) {
            throw new \InvalidArgumentException('First argument must be a positive integer');
        }

        if (!$charset) {
            throw new \InvalidArgumentException('Invalid charset');
        }

        $num = $dec->value();
        $charsetLen = strval(strlen($charset));
        $encoded = "";

        while (true) {
            if (bccomp($num, $charsetLen, 0) === -1) {
                break;
            }

            $div = bcdiv($num, $charsetLen, 0);
            $mod = bcmod($num, $charsetLen, 0);
            $char = $charset{intval($mod)};
            $encoded = $char . $encoded;
            $num = $div;
        }

        if (bccomp($num, "0", 0) !== -1) {
            $encoded = $charset{intval($num)} . $encoded;
        }

        return $encoded;
    }

    /**
     * @param string $encoded
     * @param string $charset
     * @param bool $isCaseSensitive
     * @return BcNumber
     */
    public static function toBase10(string $encoded, string $charset, bool $isCaseSensitive = true): BcNumber
    {
        return new BcNumber(self::toBase10String($encoded, $charset, $isCaseSensitive));
    }

    /**
     * @param string $encoded
     * @param string $charset
     * @param bool $isCaseSensitive
     * @return string
     */
    public static function toBase10String(string $encoded, string $charset, bool $isCaseSensitive = true): string
    {
        if (!$isCaseSensitive) { // If case in-sensitive, convert all to lowercase first
            $encoded = strtolower($encoded);
        }

        $len = strlen($encoded);
        $charsetLen = strval(strlen($charset));
        $decs = "0";
        $multiplier = "1";

        for ($i = $len - 1; $i >= 0; $i--) { // Start in reverse order
            $decs = bcadd($decs, bcmul($multiplier, strval(strpos($charset, $encoded[$i]))));
            $multiplier = bcmul($multiplier, $charsetLen);
        }

        return $decs;
    }
}