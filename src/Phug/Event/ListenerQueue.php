<?php

namespace Phug\Event;

class ListenerQueue extends \SplPriorityQueue
{
    public function compare($a, $b)
    {
        if ($a === $b) {
            return 0;
        }

        return $a > $b ? -1 : 1;
    }

    public function insert($value, $priority)
    {
        if (!is_callable($value)) {
            throw new \InvalidArgumentException(
                'Callback inserted into ListenerQueue needs to be callable'
            );
        }

        parent::insert($value, $priority);
    }
}
