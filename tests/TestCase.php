<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // В CI фронтенд не собирается: @vite не должен требовать manifest.
        $this->withoutVite();
    }
}
