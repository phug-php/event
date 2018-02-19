<?php

namespace Phug\Event;

class ListenerQueue extends \SplPriorityQueue
{
    public function compare($priority, $priorityToCompare)
    {
        if ($priority === $priorityToCompare) {
            return 0;
        }

        return $priority > $priorityToCompare ? -1 : 1;
    }

    public function insert($value, $priority)
    {
        if (is_array($value)) {
            if ($value === []) {
                return;
            }

            reset($value);
            if (is_int(key($value))) {
                foreach ($value as $key => $callback) {
                    $this->insert($callback, $priority);
                }

                return;
            }
        }
        if (!is_callable($value)) {
            throw new \InvalidArgumentException(
                'Callback inserted into ListenerQueue needs to be callable'
            );
        }

        parent::insert($value, $priority);
    }
}
