<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Local;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Str;

//php artisan make:seeder UserSeeder
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'Admin', 'display_name' => 'Administrador']);
        $sellerRole = Role::create(['name' => 'Seller', 'display_name' => 'Vendedor']);
        $grocerRole = Role::create(['name' => 'Grocer', 'display_name' => 'Almacenero']);

        Permission::create(['name' => 'User List', 'display_name' => 'Listar Usuarios', 'model_name' => 'User'])->SyncRoles([$adminRole]); //hay que analizar este para dar permiso de ver la lista
        Permission::create(['name' => 'User View', 'display_name' => 'Ver Usuario', 'model_name' => 'User'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'User Create', 'display_name' => 'Crear Usuario', 'model_name' => 'User'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'User Update', 'display_name' => 'Actualizar Usuario', 'model_name' => 'User'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'User Delete', 'display_name' => 'Eliminar Usuario', 'model_name' => 'User'])->SyncRoles([$adminRole]);

        Permission::create(['name' => 'Permission List', 'display_name' => 'Listar Permisos', 'model_name' => 'Permission'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Permission View', 'display_name' => 'Ver Permiso', 'model_name' => 'Permission'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Permission Update', 'display_name' => 'Actualizar Permiso', 'model_name' => 'Permission'])->SyncRoles([$adminRole]);

        Permission::create(['name' => 'Role List', 'display_name' => 'Listar Roles', 'model_name' => 'Role'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Role View', 'display_name' => 'Ver Rol', 'model_name' => 'Role'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Role Create', 'display_name' => 'Crear Rol', 'model_name' => 'Role'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Role Update', 'display_name' => 'Actualizar Rol', 'model_name' => 'Role'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Role Delete', 'display_name' => 'Eliminar Rol', 'model_name' => 'Role'])->SyncRoles([$adminRole]);

        Permission::create(['name' => 'Local List', 'display_name' => 'Listar Locales', 'model_name' => 'Local'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Local View', 'display_name' => 'Ver Local', 'model_name' => 'Local'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Local Create', 'display_name' => 'Crear Local', 'model_name' => 'Local'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Local Update', 'display_name' => 'Actualizar Local', 'model_name' => 'Local'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Local Delete', 'display_name' => 'Eliminar Local', 'model_name' => 'Local'])->SyncRoles([$adminRole]);



        User::find(1)->assignRole($adminRole);
        User::find(2)->assignRole($sellerRole);
        User::find(3)->assignRole($grocerRole);

    }
}
