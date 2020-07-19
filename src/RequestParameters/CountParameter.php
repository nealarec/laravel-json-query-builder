<?php

namespace Voice\JsonQueryBuilder\RequestParameters;

use Illuminate\Support\Facades\DB;
use Voice\JsonQueryBuilder\Exceptions\SearchException;

class CountParameter extends AbstractParameter
{
    public function getParameterName(): string
    {
        return 'count';
    }

    public function areArgumentsValid(): void
    {
        if (count($this->arguments) != 1) {
            throw new SearchException("[Search] Parameter '{$this->getParameterName()}' expects only one argument.");
        }

        if (!in_array($this->arguments[0], [1, '1', true, 'true'])) {
            throw new SearchException("[Search] Parameter '{$this->getParameterName()}' expects to be 'true' if it is to be used.");
        }
    }

    public function appendQuery(): void
    {
        $this->builder->select(DB::raw('count(*) as count'));
    }
}
