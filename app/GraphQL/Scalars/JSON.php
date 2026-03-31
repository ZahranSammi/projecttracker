<?php

namespace App\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;

class JSON extends ScalarType
{
    public string $name = 'JSON';

    public function serialize($value): mixed
    {
        return $value;
    }

    public function parseValue($value): mixed
    {
        return $value;
    }

    public function parseLiteral(Node $valueNode, ?array $variables = null): mixed
    {
        throw new Error('Literal JSON values are not supported.');
    }
}
