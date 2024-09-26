<?php

namespace Asseco\JsonQueryBuilder\SQLProviders;

use Asseco\JsonQueryBuilder\Exceptions\JsonQueryBuilderException;

class SQLFunctions
{
    public const DB_FUNCTIONS = [
        'avg',
        'count',
        'max',
        'min',
        'sum',
        'distinct',

        'year',
        'month',
        'day',
    ];

    final public static function validateArgument(string $argument): void
    {
        $columnRegex = "/^(((_*)?([A-Za-z0-9]+))+|\*)$/";
        if(strpos($argument, " as ")) {
            [$argument, $alias] = explode(" as ", $argument);
            if(!preg_match($columnRegex, $alias)) {
                throw new JsonQueryBuilderException(
                    "Invalid alias name: {$alias}."
                ); 
            }
        }

        $split = explode(':', $argument);
        $column = array_pop($split);
        if (!preg_match($columnRegex, $column) || in_array($column, self::DB_FUNCTIONS)) {
            throw new JsonQueryBuilderException(
                "Invalid column name: {$column}."
            );
        }

        if ($invalidFns = array_diff($split, self::DB_FUNCTIONS)) {
            throw new JsonQueryBuilderException(
                'Invalid function: ' . join(',', $invalidFns) . '.'
            );
        }
    }

    final public static function __callStatic($fn, $args)
    {
        if (!in_array($fn, self::DB_FUNCTIONS)) {
            throw new JsonQueryBuilderException(
                "Invalid function: $fn."
            );
        }

        if (method_exists(self::class, $fn)) {
            return self::$fn($args);
        }

        return $fn . "($args[0])";
    }
}
