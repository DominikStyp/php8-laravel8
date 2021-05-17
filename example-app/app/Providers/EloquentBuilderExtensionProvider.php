<?php

namespace App\Providers;

use App\Utils\LimitIdFinder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as DatabaseBuilder;
use Illuminate\Support\ServiceProvider;

class EloquentBuilderExtensionProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        DatabaseBuilder::macro('forPageAfterIdOptimised',
            function(int $count, ?int $lastId, string $column, string $table){
                /** @var DatabaseBuilder $this */
                $this->orders = $this->removeExistingOrdersFor($column);

                if(is_null($lastId)){
                    $c = clone $this;
                    $lastId = (int)$c->min($column);
                }

                $finder = new LimitIdFinder(
                    $table,
                    $count,
                    $lastId,
                    $column
                );
                $finder->setConnection($this->connection);
                $this
                    ->where($column, '>', $lastId)
                    ->where($column, '<=', $finder->findUpperId());


                return $this->orderBy($column, 'asc')
                    ->limit($count);
        });

        EloquentBuilder::macro('chunkWithIdOptimised',
            function(int $count, callable $callback, ?string $column = null, ?string $alias = null)
                {
                    /** @var EloquentBuilder $this */
                    $column = $column ?? $this->defaultKeyName();

                    $alias = $alias ?? $column;

                    $lastId = null;

                    $page = 1;

                    do {
                        $clone = clone $this;

                        // We'll execute the query for the given page and get the results. If there are
                        // no results we can just break and return from here. When there are results
                        // we will call the callback with the current chunk of these results here.
                        $table = $this->getModel()->getTable();

                        $results = $clone->forPageAfterIdOptimised($count, $lastId, $column, $table)->get();

                        $countResults = $results->count();

                        if ($countResults == 0) {
                            break;
                        }

                        // On each chunk result set, we will pass them to the callback and then let the
                        // developer take care of everything within the callback, which allows us to
                        // keep the memory low for spinning through large result sets for working.
                        if ($callback($results, $page) === false) {
                            return false;
                        }

                        $lastId = $results->last()->{$alias};

                        unset($results);

                        $page++;
                    } while ($countResults == $count);

                    return true;
                });
    }
}
