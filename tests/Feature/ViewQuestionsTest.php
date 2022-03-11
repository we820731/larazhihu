<?php

namespace Tests\Feature;

use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewQuestionsTest extends TestCase
{
    use RefreshDatabase;

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

    /** @test */
    public function user_can_view_a_single_question()
    {
        // 1. 新增一個問題
        $question = Question::factory()->create();

        // 2. 訪問連結
        $test = $this->get('/questions/' . $question->id);

        // 3. 應該看到問題內容
        $test->assertStatus(200)
            ->assertSee($question->title)
            ->assertSee($question->content);
    }
}
