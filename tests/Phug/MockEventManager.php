<?php

namespace Phug\Test;

use Phug\EventManagerInterface;
use Phug\EventManagerTrait;

class MockEventManager implements EventManagerInterface
{
    use EventManagerTrait;
}
