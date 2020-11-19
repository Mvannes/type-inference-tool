<?php
/**
 * @copyright 2017-2018 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\TypeInference\Analyzer\DynamicMethod\Tracer\Parser\Mapper;

use Hostnet\Component\TypeInference\Analyzer\Data\Type\NonScalarPhpType;
use Hostnet\Component\TypeInference\Analyzer\Data\Type\PhpTypeInterface;
use Hostnet\Component\TypeInference\Analyzer\Data\Type\ScalarPhpType;
use Hostnet\Component\TypeInference\Analyzer\Data\Type\UnresolvablePhpType;

/**
 * Maps trace types to php types.
 */
class TracerPhpTypeMapper
{
    public const NAMESPACE_GLOBAL   = '';
    public const TYPE_ARRAY         = 'array';
    public const TYPE_UNKNOWN       = 'unknown';
    private const TYPE_VARIADIC     = '...';
    public const FUNCTION_CLOSURE   = '{closure}';
    private const REGEX_TO_TYPE_MAP = [
        '/^string(\(\d+\))?|String/'           => 'string',
        '/array(\(\d+\))?/'                    => 'array',
        '/\{closure\}|class Closure|callable/' => 'callable',
        '/true|false|bool|boolean/i'           => 'bool',
        '/long|int|integer/'                   => 'int',
        '/double|float|number/'                => 'float',
    ];

    /**
     * Returns the corresponding PHP type of a type as defined in trace files.
     *
     * @param string $trace_type
     * @return PhpTypeInterface PHP type
     * @throws \InvalidArgumentException
     */
    public static function toPhpType(string $trace_type): PhpTypeInterface
    {
        foreach (self::REGEX_TO_TYPE_MAP as $regex => $php_type) {
            if (!preg_match($regex, $trace_type)) {
                continue;
            }

            if (in_array($php_type, ScalarPhpType::SCALAR_TYPES, true)) {
                return new ScalarPhpType($php_type);
            }

            return new NonScalarPhpType(self::NAMESPACE_GLOBAL, $php_type, '', null, []);
        }

        if (self::TYPE_VARIADIC === $trace_type) {
            return new UnresolvablePhpType(UnresolvablePhpType::INCONSISTENT);
        }

        [$namespace, $class_name] = self::extractTraceFunctionName($trace_type);

        if (self::TYPE_UNKNOWN === $class_name || 'null' === $trace_type) {
            return new UnresolvablePhpType(UnresolvablePhpType::NONE);
        }

        return new NonScalarPhpType($namespace ?? self::NAMESPACE_GLOBAL, $class_name, '', null, []);
    }

    /**
     * Takes a function name as defined in traces and splits it into a namespace,
     * class name and function name.
     *
     * @param string $trace_function_name
     * @return string[] [namespace (if not global), class name, function name (if present)]
     */
    public static function extractTraceFunctionName(string $trace_function_name): array
    {
        if ('???' === $trace_function_name) {
            return [null, self::TYPE_UNKNOWN, null];
        }

        $regex_trace_parts = '/(class\s)?((\w+\\\)*)(\w+)(->|::)?(\w+)?(\\\{closure})?/';
        preg_match_all($regex_trace_parts, $trace_function_name, $matches, PREG_PATTERN_ORDER);

        $namespace     = substr($matches[2][0], 0, -1);
        $is_closure    = $matches[7][0] !== '';
        $function_name = $is_closure ? self::FUNCTION_CLOSURE : $matches[6][0];
        $class_name    = ($namespace === false ? '\\' : '') . $matches[4][0];

        return [
            $namespace !== false ? $namespace : '',
            $class_name,
            $function_name !== '' ? $function_name : null,
        ];
    }
}
