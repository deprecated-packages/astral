<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue;

use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\UnionType;

final class UnionTypeValueResolver
{
    /**
     * @return mixed[]
     */
    public function resolveConstantTypes(UnionType $unionType): array
    {
        $resolvedValues = [];

        foreach ($unionType->getTypes() as $unionedType) {
            if (! $unionedType instanceof ConstantScalarType) {
                continue;
            }

            $resolvedValues[] = $unionedType->getValue();
        }

        return $resolvedValues;
    }
}
