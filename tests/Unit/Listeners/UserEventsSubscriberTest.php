<?php

namespace Tino\UserActivity\Tests\Unit\Listeners;

use Tests\UpdatesSettings;

class UserEventsSubscriberTest extends ListenerTestCase
{
    use UpdatesSettings;

    protected $theUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->theUser = factory(\Tino\User::class)->create();
    }

    /** @test */
    public function onLogin()
    {
        event(new \Tino\Events\User\LoggedIn);
        $this->assertMessageLogged('Logged in.');
    }

    /** @test */
    public function onLogout()
    {
        event(new \Tino\Events\User\LoggedOut());
        $this->assertMessageLogged('Logged out.');
    }

    /** @test */
    public function onRegister()
    {
        $this->setSettings([
            'reg_enabled' => true,
            'reg_email_confirmation' => true,
        ]);

        $user = factory(\Tino\User::class)->create();

        event(new \Illuminate\Auth\Events\Registered($user));

        $this->assertMessageLogged('Created an account.', $user);
    }

    /** @test */
    public function onAvatarChange()
    {
        event(new \Tino\Events\User\ChangedAvatar);
        $this->assertMessageLogged('Updated profile avatar.');
    }

    /** @test */
    public function onProfileDetailsUpdate()
    {
        event(new \Tino\Events\User\UpdatedProfileDetails);
        $this->assertMessageLogged('Updated profile details.');
    }

    /** @test */
    public function onDelete()
    {
        event(new \Tino\Events\User\Deleted($this->theUser));

        $message = sprintf(
            "Deleted user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    /** @test */
    public function onBan()
    {
        event(new \Tino\Events\User\Banned($this->theUser));

        $message = sprintf(
            "Banned user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    /** @test */
    public function onUpdateByAdmin()
    {
        event(new \Tino\Events\User\UpdatedByAdmin($this->theUser));

        $message = sprintf(
            "Updated profile details for %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    /** @test */
    public function onCreate()
    {
        event(new \Tino\Events\User\Created($this->theUser));

        $message = sprintf(
            "Created an account for user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    /** @test */
    public function onSettingsUpdate()
    {
        event(new \Tino\Events\Settings\Updated);
        $this->assertMessageLogged('Updated website settings.');
    }

    /** @test */
    public function onTwoFactorEnable()
    {
        event(new \Tino\Events\User\TwoFactorEnabled);
        $this->assertMessageLogged('Enabled Two-Factor Authentication.');
    }

    /** @test */
    public function onTwoFactorDisable()
    {
        event(new \Tino\Events\User\TwoFactorDisabled);
        $this->assertMessageLogged('Disabled Two-Factor Authentication.');
    }

    /** @test */
    public function onTwoFactorEnabledByAdmin()
    {
        event(new \Tino\Events\User\TwoFactorEnabledByAdmin($this->theUser));

        $message = sprintf(
            "Enabled Two-Factor Authentication for user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    /** @test */
    public function onTwoFactorDisabledByAdmin()
    {
        event(new \Tino\Events\User\TwoFactorDisabledByAdmin($this->theUser));

        $message = sprintf(
            "Disabled Two-Factor Authentication for user %s.",
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    /** @test */
    public function onPasswordResetEmailRequest()
    {
        event(new \Tino\Events\User\RequestedPasswordResetEmail($this->user));
        $this->assertMessageLogged("Requested password reset email.");
    }

    /** @test */
    public function onPasswordReset()
    {
        event(new \Illuminate\Auth\Events\PasswordReset($this->user));
        $this->assertMessageLogged("Reseted password using \"Forgot Password\" option.");
    }

    /** @test */
    public function onStartImpersonating()
    {
        $impersonated = factory(\Tino\User::class)->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        event(new \Lab404\Impersonate\Events\TakeImpersonation($this->user, $impersonated));

        $this->assertMessageLogged("Started impersonating user John Doe (ID: {$impersonated->id})");
    }

    /** @test */
    public function onStopImpersonating()
    {
        $impersonated = factory(\Tino\User::class)->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        event(new \Lab404\Impersonate\Events\LeaveImpersonation($this->user, $impersonated));

        $this->assertMessageLogged("Stopped impersonating user John Doe (ID: {$impersonated->id})");
    }
}
