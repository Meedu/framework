<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Tests\Feature\Page;

use Tests\TestCase;
use App\Services\Member\Models\User;
use Illuminate\Support\Facades\Hash;

class FindPasswordTest extends TestCase
{

    // 测试找回密码
    public function test_visit()
    {
        $this->get(route('password.request'))
            ->assertResponseStatus(200)
            ->seeInElement('button', __('重置密码'));
    }

    public function test_submit()
    {
        $user = factory(User::class)->create([
            'mobile' => '12398762345',
            'password' => Hash::make('meedu123'),
        ]);

        $this->session(['sms_password_reset' => 'smscode']);

        $this->visit(route('password.request'))
            ->type($user->mobile, 'mobile')
            ->type('smscode', 'sms_captcha')
            ->type('password_reset', 'sms_captcha_key')
            ->type('123123', 'password')
            ->type('123123', 'password_confirmation')
            ->press(__('重置密码'))
            ->seePageIs('login');

        $user->refresh();
        $this->assertTrue(Hash::check('123123', $user->password));
    }
}
