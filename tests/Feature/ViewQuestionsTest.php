<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewQuestionsTest extends TestCase
{
    /** @test */
    public function user_can_view_questions()
    {
        // 0. 拋出異常
        $this->withoutExceptionHandling();
        // 1. 跳轉至 /questions
        $test = $this->get('/questions');
        // 2. 正常返回 200
        $test->assertStatus(200);
    }
}
