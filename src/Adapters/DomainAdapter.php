<?php

namespace Gamebetr\Api\Adapters;

use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Gamebetr\Api\Models\Domain;
use Illuminate\Support\Collection;

class DomainAdapter extends AbstractAdapter
{
    /**
     * Mapping of JSON API attribute field names to model keys.
     * @var array
     */
    protected $attributes = [];

    /**
     * Mapping of JSON API filter names to model scopes.
     * @var array
     */
    protected $filterScopes = [];

    /**
     * Adapter constructor.
     * @param \CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy $paging
     * @return void
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new Domain(), $paging);
    }

    /**
     * Filter.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Support\Collection $filters
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        $this->filterWithScopes($query, $filters);
    }

    /**
     * Has many users.
     */
    public function users()
    {
        return $this->hasMany();
    }
}
