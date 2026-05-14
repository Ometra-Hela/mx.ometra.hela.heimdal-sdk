<?php

namespace Ometra\HeimdalSdk\Dtos;

final class View360SearchResultDto extends DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes,
        public readonly ?string $identifierType,
        public readonly ?string $identifierValue,
        public readonly mixed $data,
        public readonly mixed $raw,
    ) {
        parent::__construct($attributes);
    }
}
