<?php
/**
 * @copyright 2017-2018 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\TypeInference\Analyzer\Data\Type;

/**
 * Represents a scalar PHP-type.
 */
final class ScalarPhpType implements PhpTypeInterface
{
    public const TYPE_INT     = 'int';
    public const TYPE_FLOAT   = 'float';
    public const TYPE_STRING  = 'string';
    public const TYPE_BOOL    = 'bool';
    public const SCALAR_TYPES = [self::TYPE_INT, self::TYPE_FLOAT, self::TYPE_STRING, self::TYPE_BOOL];

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $is_nullable = false;

    /**
     * @param string $type
     * @param bool $is_nullable
     * @throws \InvalidArgumentException
     */
    public function __construct(string $type, bool $is_nullable = false)
    {
        if (!in_array($type, self::SCALAR_TYPES, true)) {
            throw new \InvalidArgumentException(sprintf("Given type '%s' is not a PHP-scalar.", $type));
        }

        $this->is_nullable = $is_nullable;
        $this->type        = $type;
    }

    public function getName(): string
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->is_nullable;
    }

    public function setNullable(bool $is_nullable): void
    {
        $this->is_nullable = $is_nullable;
    }
}
