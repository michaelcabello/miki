<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;
use App\Models\Partner;
use App\Models\CompanyType;
use App\Models\DocumentType;
use App\Models\Currency;
use App\Models\Pricelist;
use App\Models\Department;
use App\Models\Province;
use App\Models\District;

class PartnerSeeder extends Seeder
{

public function run(): void
    {
        // ✅ Company types
        $companyId = CompanyType::where('code', 'company')->value('id')
            ?? CompanyType::where('name', 'Empresa')->value('id')
            ?? CompanyType::min('id');

        $personId = CompanyType::where('code', 'person')->value('id')
            ?? CompanyType::where('name', 'Persona')->value('id')
            ?? CompanyType::max('id');

        if (!$companyId || !$personId) {
            throw new \RuntimeException("No hay registros en company_types. Crea/seedéa CompanyType primero.");
        }

        // ✅ Document types
        $rucId = DocumentType::where('code', 'RUC')->value('id') ?? DocumentType::min('id');
        $dniId = DocumentType::where('code', 'DNI')->value('id') ?? DocumentType::max('id');

        // ✅ Currency / Pricelists
        $currencyId = Currency::first()?->id;
        $pricelistIds = Pricelist::pluck('id')->all();

        // ✅ Ubigeo pick (100% consistente con FKs string)
        $pickUbigeo = function (): array {
            $d = District::inRandomOrder()->first();
            if (!$d) return [null, null, null];
            return [$d->department_id, $d->province_id, $d->id];
        };

        $makeDni = fn() => str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        $makeRuc = fn() => str_pad((string) random_int(0, 99999999999), 11, '0', STR_PAD_LEFT);

        $fakeImage = function (bool $isCompany) {
            $companies = [
                'fe/default/partners/company1.png',
                'fe/default/partners/company2.png',
                'fe/default/partners/company3.png',
            ];
            $people = [
                'fe/default/partners/avatar1.png',
                'fe/default/partners/avatar2.png',
                'fe/default/partners/avatar3.png',
            ];
            $arr = $isCompany ? $companies : $people;
            return $arr[array_rand($arr)];
        };

        $fakeMap = function (string $name) {
            $q = urlencode($name . ' Lima Peru');
            return "https://www.google.com/maps/search/?api=1&query={$q}";
        };

        $fakeSocial = function (string $base) {
            $slug = Str::slug($base);
            return [
                'facebook'  => "https://facebook.com/{$slug}",
                'instagram' => "https://instagram.com/{$slug}",
                'tiktok'    => "https://www.tiktok.com/@{$slug}",
                'youtube'   => "https://www.youtube.com/@{$slug}",
            ];
        };

        $usedEmails = [];
        $makeEmail = function (string $name) use (&$usedEmails) {
            $email = Str::lower(Str::slug($name, '.')) . '@demo.com';
            if (in_array($email, $usedEmails, true)) {
                $email = Str::lower(Str::slug($name . '-' . random_int(10, 99), '.')) . '@demo.com';
            }
            $usedEmails[] = $email;
            return $email;
        };

        // --------------------------
        // CLIENTES (30) + 3 contactos
        // --------------------------
        for ($i = 1; $i <= 30; $i++) {

            $isCompany = (bool) random_int(0, 1);
            $name = $isCompany ? "Cliente Empresa {$i} SAC" : "Cliente Persona {$i}";

            [$department_id, $province_id, $district_id] = $pickUbigeo();

            $social = $fakeSocial($name);

            $partner = Partner::create([
                'parent_id' => null,

                'name' => $name,
                'company_type_id'  => $isCompany ? $companyId : $personId,
                'document_type_id' => $isCompany ? $rucId : $dniId,
                'document_number'  => $isCompany ? $makeRuc() : $makeDni(),

                'image'    => $fakeImage($isCompany),
                'email'    => $makeEmail($name),
                'phone'    => '01-' . random_int(400, 899) . '-' . random_int(1000, 9999),
                'whatsapp' => '9' . random_int(10000000, 99999999),
                'mobile'   => '9' . random_int(10000000, 99999999),
                'website'  => "https://cliente{$i}.demo",

                'facebook'  => $social['facebook'],
                'instagram' => $social['instagram'],
                'youtube'   => $social['youtube'],
                'tiktok'    => $social['tiktok'],

                'street' => 'Av. Cliente ' . random_int(100, 999),
                'street2' => 'Int. ' . random_int(1, 60),
                'zip' => (string) random_int(10000, 99999),
                'map' => $fakeMap($name),

                'department_id' => $department_id,
                'province_id'   => $province_id,
                'district_id'   => $district_id,

                'is_customer' => true,
                'is_supplier' => false,
                'customer_rank' => random_int(0, 30),
                'supplier_rank' => 0,
                'status' => true,

                'pricelist_id' => !empty($pricelistIds) ? $pricelistIds[array_rand($pricelistIds)] : null,
                'currency_id' => $currencyId,

                'bank_account' => 'BCP-' . random_int(100000, 999999) . '-' . random_int(1000, 9999),

                'portal_access' => (bool) random_int(0, 1),
                'portal_enabled_at' => now(),
            ]);

            // 3 contactos hijos
            for ($c = 1; $c <= 3; $c++) {
                $cname = "Contacto {$c} - {$name}";
                $csocial = $fakeSocial($cname);

                Partner::create([
                    'parent_id' => $partner->id,

                    'name' => $cname,
                    'company_type_id' => $personId,

                    'document_type_id' => null,
                    'document_number' => null,

                    'image' => $fakeImage(false),
                    'email' => $makeEmail($cname),
                    'phone' => null,
                    'whatsapp' => '9' . random_int(10000000, 99999999),
                    'mobile' => '9' . random_int(10000000, 99999999),
                    'website' => null,

                    'facebook'  => $csocial['facebook'],
                    'instagram' => $csocial['instagram'],
                    'youtube'   => $csocial['youtube'],
                    'tiktok'    => $csocial['tiktok'],

                    'street' => $partner->street,
                    'street2' => $partner->street2,
                    'zip' => $partner->zip,
                    'map' => $partner->map,

                    'department_id' => $partner->department_id,
                    'province_id'   => $partner->province_id,
                    'district_id'   => $partner->district_id,

                    'is_customer' => false,
                    'is_supplier' => false,
                    'customer_rank' => 0,
                    'supplier_rank' => 0,
                    'status' => true,

                    'currency_id' => $currencyId,
                    'pricelist_id' => null,
                    'bank_account' => null,

                    'portal_access' => false,
                    'portal_enabled_at' => null,
                ]);
            }
        }

        // --------------------------
        // PROVEEDORES (30) + 3 contactos
        // --------------------------
        for ($i = 1; $i <= 30; $i++) {
            $name = "Proveedor {$i} EIRL";

            [$department_id, $province_id, $district_id] = $pickUbigeo();

            $social = $fakeSocial($name);

            $partner = Partner::create([
                'parent_id' => null,

                'name' => $name,
                'company_type_id' => $companyId,
                'document_type_id' => $rucId,
                'document_number' => $makeRuc(),

                'image' => $fakeImage(true),
                'email' => $makeEmail($name),
                'phone' => '01-' . random_int(400, 899) . '-' . random_int(1000, 9999),
                'whatsapp' => '9' . random_int(10000000, 99999999),
                'mobile' => '9' . random_int(10000000, 99999999),
                'website' => "https://proveedor{$i}.demo",

                'facebook'  => $social['facebook'],
                'instagram' => $social['instagram'],
                'youtube'   => $social['youtube'],
                'tiktok'    => $social['tiktok'],

                'street' => 'Jr. Proveedor ' . random_int(100, 999),
                'street2' => null,
                'zip' => (string) random_int(10000, 99999),
                'map' => $fakeMap($name),

                'department_id' => $department_id,
                'province_id'   => $province_id,
                'district_id'   => $district_id,

                'is_customer' => false,
                'is_supplier' => true,
                'customer_rank' => 0,
                'supplier_rank' => random_int(0, 30),
                'status' => true,

                'pricelist_id' => null,
                'currency_id' => $currencyId,

                'bank_account' => 'BBVA-' . random_int(100000, 999999) . '-' . random_int(1000, 9999),

                'portal_access' => false,
                'portal_enabled_at' => null,
            ]);

            for ($c = 1; $c <= 3; $c++) {
                $cname = "Contacto {$c} - {$name}";
                $csocial = $fakeSocial($cname);

                Partner::create([
                    'parent_id' => $partner->id,

                    'name' => $cname,
                    'company_type_id' => $personId,

                    'document_type_id' => null,
                    'document_number' => null,

                    'image' => $fakeImage(false),
                    'email' => $makeEmail($cname),
                    'phone' => null,
                    'whatsapp' => '9' . random_int(10000000, 99999999),
                    'mobile' => '9' . random_int(10000000, 99999999),

                    'facebook'  => $csocial['facebook'],
                    'instagram' => $csocial['instagram'],
                    'youtube'   => $csocial['youtube'],
                    'tiktok'    => $csocial['tiktok'],

                    'street' => $partner->street,
                    'street2' => $partner->street2,
                    'zip' => $partner->zip,
                    'map' => $partner->map,

                    'department_id' => $partner->department_id,
                    'province_id'   => $partner->province_id,
                    'district_id'   => $partner->district_id,

                    'is_customer' => false,
                    'is_supplier' => false,
                    'customer_rank' => 0,
                    'supplier_rank' => 0,
                    'status' => true,

                    'currency_id' => $currencyId,
                    'pricelist_id' => null,

                    'portal_access' => false,
                    'portal_enabled_at' => null,
                ]);
            }
        }
    }





}
