<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Tenant;

class StartupConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:startup-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant1 = Tenant::create(['id' => 'client1']);
        $tenant1->domains()->create(['domain' => 'client1.agendaonline.local']);

        $tenant2 = Tenant::create(['id' => 'client2']);
        $tenant2->domains()->create(['domain' => 'client2.agendaonline.local']);

        $user1 = User::create([
            'name' => 'Admin User',
            'email' => 'email@email.pt',
            'username' => 'email@email.pt',
            'password' => bcrypt('password'),
            'tenant_id' => 'client1']);

        $user2 = User::create([
            'name' => 'Client 2',
            'email' => 'email2@email.pt',
            'username' => 'email2@email.pt',
            'password' => bcrypt('password'),
            'tenant_id' => 'client2']);

        return "ok";
    }
}
