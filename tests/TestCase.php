<?php

namespace Rainwater\Active\Tests;

use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Rainwater\Active\Active;
use Rainwater\Active\ActiveFacade;
use Rainwater\Active\ActiveServiceProvider;

class TestCase extends OrchestraTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return  void
     */
    protected function getEnvironmentSetup($app)
    {
        $app['config']->set('auth.providers.users.model', 'Rainwater\Active\Tests\FakeUser');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ]);
    }

    public function setUp()
    {
        parent::setUp();

        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->integer('last_activity');
        });
    }

    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return Rainwater\Active\ActiveServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [ActiveServiceProvider::class];
    }

    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Active' => ActiveFacade::class,
        ];
    }

    protected function createSession($time = null)
    {
        $session = new Active;
        $session->last_activity = ($time) ? time() - $time : time();
        $session->save();

        return $session;
    }
    protected function createSessionWithUser($time = null)
    {
        $faker = Factory::create();

        $user = FakeUser::create([
            'name' => $faker->unique()->name
        ]);
        $session = new Active;
        $session->last_activity = ($time) ? time() - $time : time();
        $session->user()->associate($user);
        $session->save();

        return $session;
    }
}
