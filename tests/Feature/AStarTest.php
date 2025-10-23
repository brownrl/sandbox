<?php

namespace Tests\Feature;

use Tests\TestCase;

class AStarTest extends TestCase
{
    public function test_a_star_page_loads_successfully(): void
    {
        $response = $this->get('/a-star');

        $response->assertStatus(200);
    }

    public function test_a_star_page_renders_correct_component(): void
    {
        $response = $this->get('/a-star');

        $response->assertInertia(fn ($page) => $page->component('AStar'));
    }

    public function test_a_star_route_name_is_accessible(): void
    {
        $response = $this->get(route('a-star'));

        $response->assertStatus(200);
    }
}
