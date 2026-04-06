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


        Permission::create(['name' => 'Brand List', 'display_name' => 'Listar Marcas', 'model_name' => 'Brand'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Brand View', 'display_name' => 'Ver Marca', 'model_name' => 'Brand'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Brand Create', 'display_name' => 'Crear Marca', 'model_name' => 'Brand'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Brand Update', 'display_name' => 'Actualizar Marca', 'model_name' => 'Brand'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Brand Delete', 'display_name' => 'Eliminar Marca', 'model_name' => 'Brand'])->SyncRoles([$adminRole]);


        Permission::create(['name' => 'Category List', 'display_name' => 'Listar Categorias', 'model_name' => 'Category'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Category View', 'display_name' => 'Ver Categoria', 'model_name' => 'Category'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Category Create', 'display_name' => 'Crear Categoria', 'model_name' => 'Category'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Category Update', 'display_name' => 'Actualizar Categoria', 'model_name' => 'Category'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Category Delete', 'display_name' => 'Eliminar Categoria', 'model_name' => 'Category'])->SyncRoles([$adminRole]);

        Permission::create(['name' => 'SubAccountType List', 'display_name' => 'Listar Sub tipo de cuentas contables', 'model_name' => 'SubAccountType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubAccountType View', 'display_name' => 'Ver Sub tipo de cuenta contable', 'model_name' => 'SubAccountType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubAccountType Create', 'display_name' => 'Crear Sub tipo de cuenta contable', 'model_name' => 'SubAccountType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubAccountType Update', 'display_name' => 'Actualizar Sub tipo de cuenta contable', 'model_name' => 'SubAccountType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubAccountType Delete', 'display_name' => 'Eliminar Sub tipo de cuenta contable', 'model_name' => 'SubAccountType'])->SyncRoles([$adminRole]);


        Permission::create(['name' => 'AccountType List', 'display_name' => 'Listar tipo de cuentas contables', 'model_name' => 'AccountType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'AccountType View', 'display_name' => 'Ver tipo de cuenta contable', 'model_name' => 'AccountType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'AccountType Create', 'display_name' => 'Crear tipo de cuenta contable', 'model_name' => 'AccountType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'AccountType Update', 'display_name' => 'Actualizar tipo de cuenta contable', 'model_name' => 'AccountType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'AccountType Delete', 'display_name' => 'Eliminar tipo de cuenta contable', 'model_name' => 'AccountType'])->SyncRoles([$adminRole]);

        Permission::create(['name' => 'Account List', 'display_name' => 'Listar cuentas contables', 'model_name' => 'Account'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Account View', 'display_name' => 'Ver cuenta contable', 'model_name' => 'Account'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Account Create', 'display_name' => 'Crear cuenta contable', 'model_name' => 'Account'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Account Update', 'display_name' => 'Actualizar cuenta contable', 'model_name' => 'Account'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Account Delete', 'display_name' => 'Eliminar cuenta contable', 'model_name' => 'Account'])->SyncRoles([$adminRole]);
        //Tipo de Diario
        Permission::create(['name' => 'JournalType List', 'display_name' => 'Listar tipos de diarios', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'JournalType View', 'display_name' => 'Ver tipo de diario', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'JournalType Create', 'display_name' => 'Crear tipo de diario', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'JournalType Update', 'display_name' => 'Actualizar tipo de diario', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'JournalType Delete', 'display_name' => 'Eliminar tipo de diario', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'JournalType ExportExcel', 'display_name' => 'Exportar tipos de diarios a Excel', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'JournalType ExportPdf', 'display_name' => 'Exportar tipos de diarios a PDF', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'JournalType ImportExcel', 'display_name' => 'Importar tipos de diarios de Excel', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'JournalType Restore', 'display_name' => 'Restaurar tipos de diarios', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'JournalType ForceDelete', 'display_name' => 'Eliminar permanentemente tipos de diarios', 'model_name' => 'JournalType'])->SyncRoles([$adminRole]);


        Permission::create(['name' => 'Journal List', 'display_name' => 'Listar diarios', 'model_name' => 'Journal'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Journal View', 'display_name' => 'Ver diario', 'model_name' => 'Journal'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Journal Create', 'display_name' => 'Crear diario', 'model_name' => 'Journal'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Journal Update', 'display_name' => 'Actualizar diario', 'model_name' => 'Journal'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Journal Delete', 'display_name' => 'Eliminar diario', 'model_name' => 'Journal'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Journal ExportExcel', 'display_name' => 'Exportar diarios a Excel', 'model_name' => 'Journal'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Journal ExportPdf', 'display_name' => 'Exportar diarios a PDF', 'model_name' => 'Journal'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Journal ImportExcel', 'display_name' => 'Importar diarios de Excel', 'model_name' => 'Journal'])->SyncRoles([$adminRole]);

        Permission::create(['name' => 'Attribute List', 'display_name' => 'Listar atributos', 'model_name' => 'Attribute'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Attribute View', 'display_name' => 'Ver atributo', 'model_name' => 'Attribute'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Attribute Create', 'display_name' => 'Crear atributo', 'model_name' => 'Attribute'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Attribute Update', 'display_name' => 'Actualizar atributo', 'model_name' => 'Attribute'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Attribute Delete', 'display_name' => 'Eliminar atributo', 'model_name' => 'Attribute'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Attribute ExportExcel', 'display_name' => 'Exportar atributos a Excel', 'model_name' => 'Attribute'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Attribute ExportPdf', 'display_name' => 'Exportar atributos a PDF', 'model_name' => 'Attribute'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Attribute ImportExcel', 'display_name' => 'Importar atributos de Excel', 'model_name' => 'Attribute'])->SyncRoles([$adminRole]);

        Permission::create(['name' => 'SubscriptionPlan List', 'display_name' => 'Listar Planes de Subscripción', 'model_name' => 'SubscriptionPlan'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubscriptionPlan View', 'display_name' => 'Ver Planes de Subscripción', 'model_name' => 'SubscriptionPlan'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubscriptionPlan Create', 'display_name' => 'Crear Planes de Subscripción', 'model_name' => 'SubscriptionPlan'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubscriptionPlan Update', 'display_name' => 'Actualizar Planes de Subscripción', 'model_name' => 'SubscriptionPlan'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubscriptionPlan Delete', 'display_name' => 'Eliminar Planes de Subscripción', 'model_name' => 'SubscriptionPlan'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubscriptionPlan ExportExcel', 'display_name' => 'Exportar Planes de Subscripciones a Excel', 'model_name' => 'SubscriptionPlan'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubscriptionPlan ExportPdf', 'display_name' => 'Exportar Planes de Subscripciones a PDF', 'model_name' => 'SubscriptionPlan'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'SubscriptionPlan ImportExcel', 'display_name' => 'Importar Planes de Subscripciones de Excel', 'model_name' => 'SubscriptionPlan'])->SyncRoles([$adminRole]);


        Permission::create(['name' => 'Tax List', 'display_name' => 'Listar Impuestos', 'model_name' => 'Tax'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Tax View', 'display_name' => 'Ver Impuestos', 'model_name' => 'Tax'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Tax Create', 'display_name' => 'Crear Impuestos', 'model_name' => 'Tax'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Tax Update', 'display_name' => 'Actualizar Impuestos', 'model_name' => 'Tax'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Tax Delete', 'display_name' => 'Eliminar Impuestos', 'model_name' => 'Tax'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Tax ExportExcel', 'display_name' => 'Exportar Impuestos a Excel', 'model_name' => 'Tax'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Tax ExportPdf', 'display_name' => 'Exportar Impuestos a PDF', 'model_name' => 'Tax'])->SyncRoles([$adminRole]);
        Permission::create(['name' => 'Tax ImportExcel', 'display_name' => 'Importar Impuestos de Excel', 'model_name' => 'Tax'])->SyncRoles([$adminRole]);




        User::find(1)->assignRole($adminRole);
        User::find(2)->assignRole($sellerRole);
        User::find(3)->assignRole($grocerRole);
    }
}
