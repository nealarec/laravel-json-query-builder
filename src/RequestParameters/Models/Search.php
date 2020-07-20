<?php

namespace Voice\JsonQueryBuilder\RequestParameters\Models;

use Illuminate\Support\Facades\Config;
use Voice\JsonQueryBuilder\Config\ModelConfig;
use Voice\JsonQueryBuilder\Config\OperatorsConfig;
use Voice\JsonQueryBuilder\Exceptions\SearchException;
use Voice\JsonQueryBuilder\Traits\RemovesEmptyValues;

class Search
{
    use RemovesEmptyValues;

    /**
     * Constant by which values will be split within a single parameter. E.g. parameter=value1;value2
     */
    const VALUE_SEPARATOR = ';';

    public string $column;
    public array  $values;
    public string $type;
    public string $operator;

    private string      $argument;
    private ModelConfig $modelConfig;

    /**
     * Search constructor.
     * @param ModelConfig $modelConfig
     * @param OperatorsConfig $operatorsConfig
     * @param string $column
     * @param string $argument
     * @throws SearchException
     */
    public function __construct(ModelConfig $modelConfig, OperatorsConfig $operatorsConfig, string $column, string $argument)
    {
        $this->modelConfig = $modelConfig;
        $this->column = $column;
        $this->argument = $argument;

        $this->checkForForbiddenColumns();

        $this->operator = $this->parseOperator($operatorsConfig->getOperators(), $argument);
        $arguments = str_replace($this->operator, '', $this->argument);
        $this->values = $this->splitValues($arguments);
        $this->type = $this->getColumnType();
    }

    /**
     * @param $operators
     * @param string $argument
     * @return string
     * @throws SearchException
     */
    protected function parseOperator($operators, string $argument): string
    {
        foreach ($operators as $operator) {
            $argumentHasOperator = strpos($argument, $operator) !== false;

            if (!$argumentHasOperator) {
                continue;
            }

            return $operator;
        }

        throw new SearchException("[Search] No valid callback registered for $argument. Are you missing an operator?");
    }

    /**
     * Split values by a given separator
     *
     * Input: val1;val2
     *
     * Output: val1
     *         val2
     *
     * @param $values
     * @return array
     * @throws SearchException
     */
    protected function splitValues(string $values): array
    {
        $valueArray = explode(self::VALUE_SEPARATOR, $values);
        $cleanedUpValues = $this->removeEmptyValues($valueArray);

        if (count($cleanedUpValues) < 1) {
            throw new SearchException("[Search] Column '$this->column' is missing a value.");
        }

        return $cleanedUpValues;
    }

    /**
     * @return string
     * @throws SearchException
     */
    public function getColumnType(): string
    {
        $columns = $this->modelConfig->getModelColumns();

        if (!array_key_exists($this->column, $columns)) {
            // TODO: integrate recursive column check for related models?
            return 'generic';
        }

        return $columns[$this->column];
    }

    /**
     * Check if global forbidden key is used
     *
     * @throws SearchException
     */
    protected function checkForForbiddenColumns()
    {
        $forbiddenKeys = Config::get('asseco-voice.search.globalForbiddenColumns');
        $forbiddenKeys = $this->modelConfig->getForbidden($forbiddenKeys);

        if (in_array($this->column, $forbiddenKeys)) {
            throw new SearchException("[Search] Searching by '$this->column' field is forbidden. Check the configuration if this is not a desirable behavior.");
        }
    }
}