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
 * Class BcNumber
 * @package FurqanSiddiqui\BcMath
 */
class BcNumber
{
    /** @var BcMath|null */
    private $bcMath;
    /** @var null|int */
    private $scale;
    /** @var string */
    private $original;
    /** @var string */
    private $value;

    /**
     * @param string $hexits
     * @return BcNumber
     */
    public static function Decode(string $hexits): self
    {
        return new self(BcMath::Decode($hexits));
    }

    /**
     * BcNumber constructor.
     * @param null $num
     * @param BcMath|null $bcMath
     */
    public function __construct($num = null, ?BcMath $bcMath = null)
    {
        $this->original = $num ? $this->checkValidNum($num) : "0";
        $this->bcMath = $bcMath;
        $this->value = $this->original;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            "original" => $this->original,
            "value" => $this->value,
            "scale" => $this->getScale()
        ];
    }

    /**
     * Sets the scale parameter
     * @param int $scale
     * @return BcNumber
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
     * Trims unnecessary digits (0s on the extreme right after decimal point)
     * @param int $retain
     * @return BcNumber
     */
    public function trim(int $retain = 0): self
    {
        if ($this->isInteger()) {
            return $this;
        }

        $trimmed = rtrim(rtrim($this->value, "0"), ".");
        if ($retain) {
            $trimmed = explode(".", $trimmed);
            $decimals = $trimmed[1] ?? "";
            $required = $retain - strlen($decimals);
            if ($required > 0) {
                $trimmed = $trimmed[0] . "." . $decimals . str_repeat("0", $required);
            }
        }

        $this->value = $trimmed;
        return $this;
    }

    /**
     * Returns original number (given on constructor)
     * @return string
     */
    public function original(): string
    {
        return $this->original;
    }

    /**
     * Gets value as string
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Checks if value is integral (does not have decimals)
     * @return bool
     */
    public function isInteger(): bool
    {
        return preg_match('/^(\-)?(0|[1-9]+[0-9]*)$/', $this->value) ? true : false;
    }

    /**
     * Checks if value is zero
     * @return bool
     */
    public function isZero(): bool
    {
        return bccomp($this->value, "0", $this->getScale()) === 0 ? true : false;
    }

    /**
     * Checks if value is greater than zero
     * @return bool
     */
    public function isPositive(): bool
    {
        return bccomp($this->value, "0", $this->getScale()) === 1 ? true : false;
    }

    /**
     * Checks if value is less than zero
     * @return bool
     */
    public function isNegative(): bool
    {
        return bccomp($this->value, "0", $this->getScale()) === -1 ? true : false;
    }

    /**
     * Compares value with a number to check if both are equal
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function equals($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === 0 ? true : false;
    }

    /**
     * Compares value with a number to check if value is greater than argument
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function greaterThan($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === 1 ? true : false;
    }

    /**
     * Compares value with a number to check if value is greater than or equals argument
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function greaterThanOrEquals($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === -1 ? false : true;
    }

    /**
     * Compares value with a number to check if value is less than argument
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function lessThan($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === -1 ? true : false;
    }

    /**
     * Compares value with a number to check if value is less than or equals argument
     * @param $comp
     * @param int|null $scale
     * @return bool
     */
    public function lessThanOrEquals($comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->getScale($scale)) === 1 ? false : true;
    }

    /**
     * Checks if value is within (or equals) given min and max arguments
     * @param $min
     * @param $max
     * @param int|null $scale
     * @return bool
     */
    public function inRange($min, $max, ?int $scale = null): bool
    {
        $min = $this->checkValidNum($min);
        $max = $this->checkValidNum($max);

        $scale = $this->getScale($scale);
        if (bccomp($this->value, $min, $scale) !== -1) {
            if (bccomp($this->value, $max, $scale) !== 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function add($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        $this->value = bcadd($this->value, $num, $this->getScale($scale));
        return $this;
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function subtract($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        $this->value = bcsub($this->value, $num, $this->getScale($scale));
        return $this;
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function multiply($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        $this->value = bcmul($this->value, $num, $this->getScale($scale));
        return $this;
    }

    /**
     * @param int $base
     * @param int $exponent
     * @param int|null $scale
     * @return BcNumber
     */
    public function multiplyByPow(int $base, int $exponent, ?int $scale = null): self
    {
        if ($base < 1) {
            throw new \InvalidArgumentException('Value for param "base" must be a positive integer');
        } elseif ($exponent < 1) {
            throw new \InvalidArgumentException('Value for param "exponent" must be a positive integer');
        }

        $this->value = bcmul(
            $this->value,
            bcpow(strval($base), strval($exponent), 0),
            $this->getScale($scale)
        );

        return $this;
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function divide($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        $this->value = bcdiv($this->value, $num, $this->getScale($scale));
        return $this;
    }

    /**
     * @param $num
     * @param int|null $scale
     * @return BcNumber
     */
    public function pow($num, ?int $scale = null): self
    {
        $num = $this->checkValidNum($num);
        $this->value = bcpow($this->value, $num, $this->getScale($scale));
        return $this;
    }

    /**
     * @param $divisor
     * @param int|null $scale
     * @return BcNumber
     */
    public function mod($divisor, ?int $scale = null): self
    {
        $num = $this->checkValidNum($divisor);
        $this->value = bcmod($this->value, $num, $this->getScale($scale));
        return $this;
    }

    /**
     * @param $divisor
     * @param int|null $scale
     * @return BcNumber
     */
    public function remainder($divisor, ?int $scale = null): self
    {
        return $this->mod($divisor, $scale);
    }

    /**
     * This method will create new instance of BcNumber with current value, this should be used before calling any
     * arithmetic method (add, subtract, multiply, divide, etc...) where you expect resulting value as different Instance
     * without modifying existing value
     * @return BcNumber
     */
    public function clone(): self
    {
        return new self($this->value, $this->bcMath);
    }

    /**
     * Alias of clone() method
     * @return BcNumber
     */
    public function new(): self
    {
        return $this->clone();
    }

    /**
     * Encodes current value in hexadecimal (base16)
     * @param bool $prefix
     * @return string
     */
    public function encode(bool $prefix = false): string
    {
        return BcMath::Encode($this->value, $prefix);
    }

    /**
     * @param int|null $scale
     * @return int
     */
    private function getScale(?int $scale = null): int
    {
        if (is_int($scale) && $scale > 0) {
            return $scale;
        }

        if (is_int($this->scale)) {
            return $this->scale;
        }

        if ($this->bcMath) {
            return $this->bcMath->scale ?? 0;
        }

        return 0;
    }

    /**
     * Checks and accepts Integers, Double/Float values or numeric Strings for BcMath operations
     * @param $num
     * @return string
     */
    private function checkValidNum($num): string
    {
        return BcMath::Value($num);
    }
}