<?php

namespace Tino\UserActivity\Tests\Unit\Listeners;

use Tino\Events\Role\Created;
use Tino\Events\Role\Deleted;
use Tino\Events\Role\PermissionsUpdated;
use Tino\Events\Role\Updated;

class RoleEventsSubscriberTest extends ListenerTestCase
{
    protected $role;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role = factory(\Tino\Role::class)->create();
    }

    /** @test */
    public function onCreate()
    {
        event(new Created($this->role));
        $this->assertMessageLogged("Created new role called {$this->role->display_name}.");
    }

    /** @test */
    public function onUpdate()
    {
        event(new Updated($this->role));
        $this->assertMessageLogged("Updated role with name {$this->role->display_name}.");
    }

    /** @test */
    public function onDelete()
    {
        event(new Deleted($this->role));
        $this->assertMessageLogged("Deleted role named {$this->role->display_name}.");
    }

    /** @test */
    public function onPermissionsUpdate()
    {
        event(new PermissionsUpdated());
        $this->assertMessageLogged("Updated role permissions.");
    }
}
