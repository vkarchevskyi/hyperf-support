<?php

namespace OnixSystemsPHP\HyperfSupport\Model\Filter;

use OnixSystemsPHP\HyperfCore\Model\Filter\AbstractFilter;
use OpenApi\Attributes as OA;

#[OA\Parameter(parameter: 'TicketFilter__title', name: 'title', in: 'query', schema: new OA\Schema(
    type: 'string'
), example: 'Lorem ipsum')]
#[OA\Parameter(parameter: 'TicketFilter__source', name: 'source', in: 'query', schema: new OA\Schema(
    type: 'string'
), example: 'local')]
#[OA\Parameter(parameter: 'TicketFilter__user', name: 'user', in: 'query', schema: new OA\Schema(
    type: 'integer'
), example: 1)]
class TicketFilter extends AbstractFilter
{
    public function title(string $param): void
    {
        $this->builder->where('title', 'like', "%$param%");
    }

    public function source(string $param): void
    {
        $this->builder->where('source', 'like', "%$param%");
    }

    public function user(int $param): void
    {
        $this->builder
            ->where('created_by', 'like', "%$param%")
            ->orWhere('modified_by', 'like', "%$param%")
            ->orWhere('deleted_by', 'like', "%$param%");
    }
}
