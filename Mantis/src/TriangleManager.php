<?php

namespace Triangle;

use Triangle\Entity\TriCount;

class TriangleManager
{
    private $minLength, $maxLength;

    /**
     * TemplateManager constructor.
     */
    public function __construct(int $minLength, int $maxLength)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * @return mixed
     */
    public function getCalculateTri()
    {
        $tri = new TriCount();

        if($tri->validEntry($this->minLength, $this->maxLength)){
            return $tri->count($this->minLength, $this->maxLength);
        } else {
            return 'Bad entry';
        }
    }


}
