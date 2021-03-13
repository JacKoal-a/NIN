<?php
class Sigmoid{
    public static $y_values;

    public static function setupSigmoid() {
        for ($i = 0; $i < 200; $i++) {
          $x = ($i / 20.0) - 5.0;
          static::$y_values[$i] = 2.0 / (1.0 + exp(-2.0 * $x)) - 1.0;
        }
    }

    public function lookupSigmoid($x) {
        return static::$y_values[  max (min((int) floor(($x + 5.0) * 20.0), 199),0)   ];
    }
}
?>