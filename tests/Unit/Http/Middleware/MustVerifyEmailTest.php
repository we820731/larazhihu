<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\MustVerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Tests\Testcase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MustVerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unverified_user_must_verify_email_before_do_something_not_allowed()
    {
        $this->signIn(create(User::class, [
            'email_verified_at' => null
        ]));

        $middleware = new MustVerifyEmail();

        // handle() 方法接收一個Request 實例和一個閉包
        // 如果閉包函數被執行，說明中間件未生效，測試失敗
        $response = $middleware->handle(new Request, function ($request) {
            $this->fail("Next middleware was called.");
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(url('/email/verify'), $response->getTargetUrl());
    }

    /** @test */
    public function verified_user_can_continue()
    {
        // be方法:設定目前經過驗證的使用者
        $this->be(create(User::class, [
            'email_verified_at' => Carbon::now()
        ]));

        $request = new Request();

        // 當嘗試以調用函數的方式調用一個對象時，__invoke() 方法會被自動調用
        // 非常適合用來測試閉包函數是否被調用
        $next = new class {
            public $called = false;

            public function __invoke($request)
            {
                $this->called = true;

                return $request;
            }
        };

        $middleware = new MustVerifyEmail();

        $response = $middleware->handle($request, $next);

        $this->assertTrue($next->called);
        $this->assertSame($request, $response);
    }
}
