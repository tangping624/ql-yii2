<?php

namespace app\framework\utils;

class Math
{

    /**
     * getRandomWeightedElement()
     * Utility function for getting random values with weighting.
     * Pass in an associative array, such as array('A'=>5, 'B'=>45, 'C'=>50)
     * An array like this means that "A" has a 5% chance of being selected, "B" 45%, and "C" 50%.
     * The return value is the array key, A, B, or C in this case.  Note that the values assigned
     * do not have to be percentages.  The values are simply relative to each other.  If one value
     * weight was 2, and the other weight of 1, the value with the weight of 2 has about a 66%
     * chance of being selected.  Also note that weights should be integers.
     *
     * @param array $weightedValues
     * @return int|string
     */
    public static function getRandomWeightedElement(array $weightedValues)
    {
        $rand = mt_rand(1, (int)array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }

        return false;
    }

    /**
     * eg:
     * for($i=0; $i<10000; $i++)
        {

        $key = getRandomPercentWeightedElement(['A'=>0.1, 'B'=>0.2, 'C'=>0.2]);
        $arry[] = $key ? $key : 'n';
        }
        print_r(array_count_values($arry));
      -------------------------------------------------
     *  output:
     *  Array
        (
        [n] => 4989
        [C] => 1967
        [A] => 1073
        [B] => 1971
        )
     * @param array $weightedValues ['A'=>0.1, 'B'=>0.2, 'C'=>0.2]
     * @return bool|string
     */
    public static function getRandomPercentWeightedElement(array $weightedValues)
    {
        $rand = (float)mt_rand() / (float)mt_getrandmax();

        $result = false;
        foreach ($weightedValues as $value => $weight) {
            if ($rand < $weight) {
                $result = $value;
                break;
            }
            $rand -= $weight;
        }
        return $result;
    }


}