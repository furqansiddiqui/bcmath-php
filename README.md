# BcMath OOP wrapper

BcMath OOP wrapper lib

## Installation

`composer require furqansiddiqui/bcmath-php`

## BcNumber Methods

Method | Description | Returns
--- | --- | ---
scale | Sets the scale parameter | `self`
trim | Trims unnecessary digits (0s on the extreme right after decimal point) | `self`
original | Returns original number (given on constructor) | `string`
value | Gets value as string | `string`
isInteger | Checks if value is integral (does not have decimals) | `bool`
isZero | Checks if value is zero | `bool`
isPositive| Checks if value is greater than zero | `bool`
isNegative| Checks if value is less than zero | `bool`
equals | Compares value with a number to check if both are equal | `bool`
greaterThan | Compares value with a number to check if value is greater than argument | `bool`
greaterThanOrEquals | Compares value with a number to check if value is greater than or equals argument | `bool`
lessThan | Compares value with a number to check if value is less than argument | `bool`
lessThanOrEquals | Compares value with a number to check if value is less than or equals argument | `bool`
inRange |  Checks if value is within (or equals) given min and max arguments | `bool`
add | *---* | `self`
subtract | *---* | `self`
multiply | *---* | `self`
multiplyByPow | *---* | `self`
divide | *---* | `self`
pow | *---* | `self`
mod | *---* | `self`