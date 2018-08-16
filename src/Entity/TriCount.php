<?php

namespace Triangle\Entity;

class TriCount
{

    const MIN_NUMBER = 0;
    const MAX_NUMBER = 1000001;

    /**
     * TriCount constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param int $minLength
     * @param int $maxLength
     * @return int
     */
    public function count(int $minLength, int $maxLength): int
    {
        $total = 0;
        for ($i = $minLength; $i <= $maxLength; $i++){
            for($j = $i ; $j <= $maxLength; $j++){
                for($k = $j ; $k <= $maxLength; $k++){
                    if( ($i + $j ) > $k ) {
                        $total++;
                        if ($total > 1000000000) {
                            return -1;
                        }
                    }
                }
            }
        }
       return $total;
    }

    /**
     * @param int $minLength
     * @param int $maxLength
     * @return bool
     */
    public function validEntry(int $minLength, int $maxLength): bool
    {
        if ((TriCount::MIN_NUMBER < $minLength) && ($minLength < $maxLength) && ($maxLength < TriCount::MAX_NUMBER)) {
            return true;
        }
        return false;
    }
}
