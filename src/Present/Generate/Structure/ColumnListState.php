<?php

namespace Rapid\Laplus\Present\Generate\Structure;

use Illuminate\Support\Fluent;

class ColumnListState
{

    public function __construct(
        /** @var Fluent[] */
        public array $added = [],
        /** @var Fluent[] */
        public array $changed = [],
        /** @var string[] */
        public array $removed = [],
        /** @var string[] */
        public array $renamed = [],
    )
    {
    }

    public function renamed(string $from, string $to)
    {
        $this->renamed[$from] = $to;
    }

    public function changed(string $from, Fluent $to)
    {
        $this->changed[$from] = $to;
    }

    public function removed(string $name)
    {
        $this->removed[] = $name;
    }

    public function added(string $name, Fluent $column)
    {
        $this->added[$name] = $column;
    }

    public function isEmpty()
    {
        return empty($this->added) && empty($this->changed) && empty($this->removed) && empty($this->renamed);
    }

}