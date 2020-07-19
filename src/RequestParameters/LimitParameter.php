<?php

namespace Voice\JsonQueryBuilder\RequestParameters;

use Voice\JsonQueryBuilder\Exceptions\SearchException;

class LimitParameter extends AbstractParameter
{
    public function getParameterName(): string
    {
        return 'limit';
    }

    public function areArgumentsValid(): void
    {
        if (count($this->arguments) != 1) {
            throw new SearchException("[Search] Parameter '{$this->getParameterName()}' expects only one argument.");
        }
    }

    public function appendQuery(): void
    {
        $this->builder->limit($this->getArguments()[0]);
    }


}
