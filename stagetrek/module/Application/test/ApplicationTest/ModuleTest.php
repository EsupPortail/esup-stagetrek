<?php

namespace ApplicationTest;

use Application\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    /**
     * @var Module
     */
    private $module;

    protected function setUp()
    {
        $this->module = new Module();
    }

    public function testCanGetArrayConfig()
    {
        $this->assertIsArray($this->module->getConfig());
    }
}
