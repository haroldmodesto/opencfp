<?php

namespace OpenCFP\Test\Http\Controller\Admin;

use Cartalyst\Sentry\Users\UserInterface;
use Mockery;
use OpenCFP\Domain\Services\Authentication;
use OpenCFP\Infrastructure\Auth\RoleAccess;
use OpenCFP\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RoleAccessTest extends WebTestCase
{
    public function testReturnsRedirectIfCheckFailed()
    {
        $auth = Mockery::mock(Authentication::class);
        $auth->shouldReceive('check')->andReturn(false);
        $this->swap(Authentication::class, $auth);

        $this->assertInstanceOf(RedirectResponse::class, RoleAccess::userHasAccess($this->app, 'admin'));
    }

    public function testReturnsFalseIfCheckSucceededButUserHasNoAdminPermission()
    {
        $user = Mockery::mock(UserInterface::class);
        $user->shouldReceive('hasPermission')->with('admin')->andReturn(false);

        $auth = Mockery::mock(Authentication::class);
        $auth->shouldReceive('check')->andReturn(true);
        $auth->shouldReceive('user')->andReturn($user);
        $this->swap(Authentication::class, $auth);

        $this->assertInstanceOf(RedirectResponse::class, RoleAccess::userHasAccess($this->app, 'admin'));
    }

    public function testReturnsNothingIfCheckSucceededAndUserHasAdminPermission()
    {
        $user = Mockery::mock(UserInterface::class);
        $user->shouldReceive('hasPermission')->with('admin')->andReturn(true);

        $auth = Mockery::mock(Authentication::class);
        $auth->shouldReceive('check')->andReturn(true);
        $auth->shouldReceive('user')->andReturn($user);
        $this->swap(Authentication::class, $auth);

        //The middleware doesn't do anything if the user is an admin, so it returns null (void)
        $this->assertNull(RoleAccess::userHasAccess($this->app, 'admin'));
    }

    public function testReviewerCantGetToAdminPages()
    {
        $this->asReviewer();
        $this->assertInstanceOf(RedirectResponse::class, RoleAccess::userHasAccess($this->app, 'admin'));
        $this->assertNull(RoleAccess::userHasAccess($this->app, 'reviewer'));
    }

    public function testAdminCantGetToReviewerPage()
    {
        $this->asAdmin();
        $this->assertInstanceOf(RedirectResponse::class, RoleAccess::userHasAccess($this->app, 'reviewer'));
        $this->assertNull(RoleAccess::userHasAccess($this->app, 'admin'));
    }
}
