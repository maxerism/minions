<?php

namespace Minions\Tests\Feature\Http\Console;

use Minions\TestCase as MinionsTestCase;
use Minions\Tests\TestCase;

class MakeRpcRequestTest extends MinionsTestCase
{
    protected $files = [
        'app/JsonRpc/Ping.php',
    ];

    /** @test */
    public function it_can_generate_rpc_request_file()
    {
        $this->artisan('minions:make', ['name' => 'Ping'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\JsonRpc;',
            'use Minions\Http\Request;',
            'use Minions\Http\ValidatesRequests;',
            'class Ping',
            'use ValidatesRequests;',
            'public function __invoke(Request $request)',
        ], 'app/JsonRpc/Ping.php');
    }

    /** @test */
    public function it_can_generate_rpc_request_file_with_middleware()
    {
        $this->artisan('minions:make', ['name' => 'Ping', '--middleware' => true])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\JsonRpc;',
            'use Minions\Http\Request;',
            'use Minions\Http\ValidatesRequests;',
            'class Ping',
            'use ValidatesRequests;',
            'public function middleware(): array',
            'public function __invoke(Request $request)',
        ], 'app/JsonRpc/Ping.php');
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Minions\Http\MinionsServiceProvider'];
    }
}
