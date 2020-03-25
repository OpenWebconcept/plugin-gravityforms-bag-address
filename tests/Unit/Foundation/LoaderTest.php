<?php

namespace Yard\Tests\BAG\Foundation;

use WP_Mock;
use Yard\BAG\Foundation\Loader;
use Yard\Tests\BAG\TestCase;

class LoaderTest extends TestCase
{
    public function setUp(): void
    {
        WP_Mock::setUp();
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function it_adds_actions_to_the_loader_actions(): void
    {
        $loader = Loader::getInstance();

        $loader->addAction('test-hook', $this, 'test', 10, 1);

        $this->assertClassHasAttribute('actions', Loader::class);
        $this->assertCount(1, $loader->getActions());

        $loader->addAction('test-hook-2', $this, 'test', 10, 1);
        $loader->addAction('test-hook-3', $this, 'test', 10, 1);
        $this->assertCount(3, $loader->getActions());
    }

    /** @test */
    public function it_adds_filters_to_the_loader_actions(): void
    {
        $loader = Loader::getInstance();

        $loader->addFilter('test-hook', $this, 'test', 10, 1);

        $this->assertClassHasAttribute('filters', Loader::class);
        $this->assertCount(1, $loader->getFilters());

        $loader->addFilter('test-hook-2', $this, 'test', 10, 1);
        $loader->addFilter('test-hook-3', $this, 'test', 10, 1);
        $this->assertCount(3, $loader->getFilters());
    }

    /** @test */
    public function it_registers_the_hooks_correctly(): void
    {
        $loader = Loader::getInstance();

        $loader->addAction('test-action', $this, 'test', 10, 1);
        $loader->addFilter('test-filter', $this, 'test', 10, 1);

        WP_Mock::expectActionAdded('test-action', [$this, 'test'], 10, 1);
        WP_Mock::expectFilterAdded('test-filter', [$this, 'test'], 10, 1);

        $loader->register();

        $this->assertTrue(true);
    }
}
