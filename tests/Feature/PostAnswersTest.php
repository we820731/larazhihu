<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostAnswersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_may_not_post_an_answer()
    {
        // 未認證的原因導致操作不被允許
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $question = Question::factory()->published()->create();

        $response = $this->post("/questions/{$question->id}/answers", [
            'content' => 'This is an answer.'
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/login');
    }

    /** @test */
    public function signed_in_user_can_post_an_answer_to_a_published_question()
    {
        // 假測已存在某個問題
        $question = Question::factory()->published()->create();
        $this->signIn($user = create(User::class));

        // 我們要觸發某個路由
        $response = $this->post("/questions/{$question->id}/answers", [
            'content' => 'This is an answer.'
        ]);
        $response->assertStatus(302);

        // 我們要看到預期的結果
        $answer = $question->answers()->where('user_id', $user->id)->first();
        $this->assertNotNull($answer);
        $this->assertEquals(1, $question->answers()->count());
    }

    /** @test */
    public function can_not_post_an_answer_to_an_unpublished_question()
    {
        $question = Question::factory()->unpublished()->create();
        $this->signIn($user = create(User::class));

        $response = $this->withExceptionHandling()
            ->post("/questions/{$question->id}/answers", [
                'user_id' => $user->id,
                'content' => 'This is an answer.'
            ]);

        $response->assertStatus(404);

        $this->assertDatabaseMissing('answers', ['question_id' => $question->id]);
        $this->assertEquals(0, $question->answers()->count());
    }

    /** @test */
    public function content_is_required_to_post_answers()
    {
        $this->withExceptionHandling();

        $question = Question::factory()->published()->create();
        $this->signIn($user = create(User::class));

        $response = $this->post("/questions/{$question->id}/answers", [
            'user_id' => $user->id,
            'content' => null
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('content');
    }
}
