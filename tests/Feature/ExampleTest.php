<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_tasks(): void
    {
        $this->get('/')->assertRedirect('/tasks');
    }

    public function test_tasks_index_returns_success(): void
    {
        $this->get('/tasks')->assertOk();
    }
}
