<?php

declare(strict_types=1);

namespace Symplify\Astral\ValueObject;

/**
 * @api
 * @deprecated use a custom AST Visitor instead, see https://phpstan.org/blog/preprocessing-ast-for-custom-rules
 */
final class AttributeKey
{
    /**
     * Convention key name in php-parser and PHPStan for parent node
     *
     * @var string
     * @deprecated Use hook to parent node
     */
    public const PARENT = 'parent';

    /**
     * Used in php-paser, do not change
     *
     * @var string
     */
    public const KIND = 'kind';

    /**
     * @api
     * @var string
     */
    public const REFERENCED_CLASSES = 'referenced_classes';

    /**
     * Do not change, part of internal PHPStan naming
     *
     * @api
     * @var string
     * @deprecated Use hook to parent node
     */
    public const PREVIOUS = 'previous';

    /**
     * Do not change, part of internal PHPStan naming
     *
     * @api
     * @var string
     * @deprecated Use hook to parent node
     */
    public const NEXT = 'next';

    /**
     * PHPStan @api Do not change, part of internal PHPStan naming
     *
     * @api
     * @var string
     */
    public const STATEMENT_DEPTH = 'statementDepth';

    /**
     * Used by php-parser, do not change
     *
     * @var string
     */
    public const COMMENTS = 'comments';

    /**
     * @var string
     */
    public const REFERENCED_CLASS_CONSTANTS = 'referenced_class_constants';

    /**
     * @var string
     */
    public const REFERENCED_METHOD_CALLS = 'referenced_method_calls';

    /**
     * PHPStan @api Used in PHPStan for printed node content. Useful for printing error messages without need to reprint
     * it again.
     *
     * @var string
     */
    public const PHPSTAN_CACHE_PRINTER = 'phpstan_cache_printer';

    /**
     * @var string
     */
    public const ASSIGNED_TO = 'assigned_to';

    /**
     * @var string
     */
    public const NULLSAFE_CHECKED = 'nullsafe_checked';

    /**
     * PHPStan @api
     *
     * @var string
     */
    public const PARENT_STMT_TYPES = 'parentStmtTypes';
}
