<?php

namespace Voice\JsonQueryBuilder\RequestParameters;

use Illuminate\Database\Eloquent\Builder;
use Voice\JsonQueryBuilder\Config\ModelConfig;
use Voice\JsonQueryBuilder\Exceptions\SearchException;

abstract class AbstractParameter
{
    public Builder     $builder;
    public ModelConfig $modelConfig;
    protected array    $arguments;

    /**
     * AbstractParameter constructor.
     * @param array $arguments
     * @param Builder $builder
     * @param ModelConfig $modelConfig
     * @throws SearchException
     */
    public function __construct(array $arguments, Builder $builder, ModelConfig $modelConfig)
    {
        $this->arguments = $arguments;
        $this->builder = $builder;
        $this->modelConfig = $modelConfig;
    }

    /**
     * JSON key by which the parameter will be recognized.
     * @return string
     */
    abstract public static function getParameterName(): string;

    /**
     * Append the query to Eloquent builder
     * @throws SearchException
     */
    abstract public function appendQuery(): void;

    /**
     * @throws SearchException
     */
    public function run()
    {
        $this->areArgumentsValid();
        $this->appendQuery();
    }

    /**
     * Check validity of fetched arguments and throw exception if it fails
     * @throws SearchException
     */
    protected function areArgumentsValid(): void
    {
        if (count($this->arguments) < 1) {
            throw new SearchException("[Search] Couldn't get values for '{$this->getParameterName()}'.");
        }

        // Override or extend on child objects if needed
    }
}