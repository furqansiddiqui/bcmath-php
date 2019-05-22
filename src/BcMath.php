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

namespace FurqanSiddiqui\BcMath;

/**
 * Class BcMath
 * @package FurqanSiddiqui\BcMath
 * @property-read int $scale
 */
class BcMath
{
    /** string Version (Major.Minor.Release-Suffix) */
    public const VERSION = "0.2.10";
    /** int Version (Major * 10000 + Minor * 100 + Release) */
    public const VERSION_ID = 210;

    /** @var int */
    private $scale;

    /**
     * BcMath constructor.
     * @param int $scale
     */
    public function __construct(int $scale = 0)
    {
        $this->scale($scale);
    }

    /**
     * @param int $scale
     * @return BcMath
     */
    public function scale(int $scale): self
    {
        if ($scale < 0) {
            throw new \InvalidArgumentException('BcMath scale value must be a positive integer');
        }

        $this->scale = $scale;
        return $this;
    }

    /**
     * @param $num
     * @return BcNumber
     */
    public function number($num): BcNumber
    {
        return new BcNumber($num, $this);
    }

    /**
     * @param $prop
     * @return mixed
     */
    public function __get($prop)
    {
        switch ($prop) {
            case "scale":
                return $this->$prop;
        }

        throw new \DomainException('Cannot get value of inaccessible BcMath property');
    }

    /**
     * Encodes/converts integral numbers (Integer or strings comprised of integral numbers) from Base10 to Base16/Hexadecimal
     * If resulting hexits are not even, this method will prefix "0" to even out
     * @param $decs
     * @param bool $prefixed
     * @return string
     */
    public static function Encode($decs, bool $prefixed = false): string
    {
        if (is_int($decs)) {
            $decs = strval($decs);
        }

        if (!is_string($decs) || !preg_match('/^(0|[1-9]+[0-9]*)$/', $decs)) {
            throw new \InvalidArgumentException('First argument must be an integral number');
        }

        $hexits = BcBaseConvert::fromBase10(new BcNumber($decs), BcBaseConvert::CHARSET_BASE16);
        if (strlen($hexits) % 2 !== 0) {
            $hexits = "0" . $hexits; // Even-out resulting hexits
        }

        return $prefixed ? "0x" . $hexits : $hexits;
    }

    /**
     * Converts/decodes from hexadecimals to Base10/decimals
     * @param string $hexits
     * @return string
     */
    public static function Decode(string $hexits): string
    {
        if (!preg_match('/^(0x)?[a-f0-9]+$/i', $hexits)) {
            throw new \InvalidArgumentException('Only hexadecimal numbers can be decoded');
        }

        if (substr($hexits, 0, 2) === "0x") {
            $hexits = substr($hexits, 2);
        }

        return BcBaseConvert::toBase10String($hexits, BcBaseConvert::CHARSET_BASE16, false);
    }

    /**
     * Returns instance of BcNumber if given argument is a valid numeric value of any data type,
     * otherwise returns NULL without throwing any Exception. This method may be used in IF statements to check if
     * argument is a valid number (of any data type)
     *
     * @param $num
     * @return BcNumber|null
     */
    public static function isNumeric($num): ?BcNumber
    {
        try {
            return new BcNumber($num);
        } catch (\Exception $e) {
        }

        return null;
    }

    /**
     * Checks and accepts Integers, Double/Float values or numeric Strings for BcMath operations
     * @param $num
     * @return string
     */
    public static function Value($num): string
    {
        // Instances of self are obviously valid numbers
        if ($num instanceof BcNumber) {
            return $num->value();
        }

        // Integers are obviously valid numbers
        if (is_int($num)) {
            return strval($num);
        }

        // Floats are valid numbers too but must be checked for scientific E-notations
        if (is_float($num)) {
            $floatAsString = strval($num);
            // Look if scientific E-notation
            if (preg_match('/e\-/i', $floatAsString)) {
                // Auto-detect decimals
                $decimals = preg_split('/e\-/i', $floatAsString);
                $decimals = intval(strlen($decimals[0])) + intval($decimals[1]);
                return number_format($num, $decimals, ".", "");
            } elseif (preg_match('/e\+/i', $floatAsString)) {
                return number_format($num, 0, "", "");
            }

            return $floatAsString;
        }

        // Check with in String
        if (is_string($num)) {
            if (preg_match('/^\-?(0|[1-9]+[0-9]*)(\.[0-9]+)?$/', $num)) {
                return $num;
            }
        }

        throw new \InvalidArgumentException('Passed value cannot be used as number with BcMath lib');
    }
}