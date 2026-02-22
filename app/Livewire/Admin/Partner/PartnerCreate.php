<?php

namespace App\Livewire\Admin\Partner;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Partner;
use App\Models\CompanyType;
use App\Models\DocumentType;
use App\Models\Pricelist;
use App\Models\Currency;
use App\Models\Account;

use App\Models\Department;
use App\Models\Province;
use App\Models\District;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\PortalAccessMail;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Http;


class PartnerCreate extends Component
{
    public string $tab = 'general';

    // ====== Campos partner ======
    public ?string $document_number = null;
    public ?int $company_type_id = null;
    public ?int $document_type_id = null;


    public bool $is_customer = true;
    public bool $is_supplier = false;

    public string $name = '';
    public ?string $street = null;

    // Ubigeo
    public ?string $department_id = null;
    public ?string $province_id = null;
    public ?string $district_id = null;

    public array $provinces = [];
    public array $districts = [];

    public ?int $pricelist_id = null;
    public ?int $currency_id = null;

    public int $status = 1; // 1 activo, 0 desactivo
    public bool $portal_access = false;


    public ?string $email = null;
    public ?string $phone = null;
    public ?string $whatsapp = null;
    public ?string $mobile = null;
    public ?string $website = null;

    public ?string $map = null;


    // ====== Contabilidad ======
    public ?int $account_receivable_id = null;
    public ?int $account_payable_id = null;

    // ====== Opciones ======
    public array $companyTypeOptions = [];
    public array $documentTypeOptions = [];
    public array $pricelistOptions = [];
    public array $currencyOptions = [];
    public array $accountOptions = [];

    public function mount($departmentId = null, $provinceId = null, $districtId = null): void
    {
        $this->companyTypeOptions = CompanyType::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($x) => ['id' => $x->id, 'name' => $x->name])
            ->all();

        $this->documentTypeOptions = DocumentType::query()
            ->where('state', 1)
            ->orderBy('order')
            ->get(['id', 'code'])
            ->map(fn($x) => ['id' => $x->id, 'code' => $x->code])
            ->all();

        $this->pricelistOptions = Pricelist::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($x) => ['id' => $x->id, 'name' => $x->name])
            ->all();

        $this->currencyOptions = Currency::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($x) => ['id' => $x->id, 'name' => $x->name])
            ->all();

        $this->accountOptions = Account::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name'])
            ->map(fn($a) => [
                'id' => $a->id,
                'label' => trim(($a->code ? $a->code . ' - ' : '') . $a->name),
            ])
            ->all();

        $this->department_id = $departmentId;
        $this->province_id = $provinceId;
        $this->district_id = $districtId;

        $this->provinces = [];
        $this->districts = [];


        //  Defaults: cuentas por cobrar / pagar
        $this->account_receivable_id = Account::query()
            ->where('code', '1212')
            ->value('id');

        $this->account_payable_id = Account::query()
            ->where('code', '421')
            ->value('id');
    }


    public function searchDocument(): void
    {
        $doc = preg_replace('/\D+/', '', (string) $this->document_number); // solo números
        $len = strlen($doc);

        if (!in_array($len, [8, 11], true)) {
            $this->addError('document_number', 'El documento debe tener 8 (DNI) o 11 (RUC) dígitos.');
            return;
        }

        // 1) Buscar en BD
        $exists = Partner::query()
            ->where('document_number', $doc)
            ->exists();

        if ($exists) {
            $this->addError('document_number', 'Este DNI/RUC ya existe en el sistema.');
            return;
        }

        // 2) Autoselección DNI/RUC y Persona/Empresa
        $this->document_number = $doc;

        $dniId = DocumentType::where('code', 'DNI')->value('id');
        $rucId = DocumentType::where('code', 'RUC')->value('id');

        $personId  = CompanyType::where('code', 'person')->value('id');
        $companyId = CompanyType::where('code', 'company')->value('id');

        if ($len === 8) {
            $this->document_type_id = $dniId;
            $this->company_type_id  = $personId;
        } else {
            $this->document_type_id = $rucId;
            $this->company_type_id  = $companyId;
        }

        // 3) Consultar API externa
        $token = config('services.consulta_ruc.token');
        $base  = rtrim((string) config('services.consulta_ruc.base_url'), '/');

        if (!$token || !$base) {
            $this->addError('document_number', 'No está configurado el servicio de consulta (token/base_url).');
            return;
        }

        $endpoint = $len === 11
            ? "{$base}/sunat/ruc?numero={$doc}"
            : "{$base}/reniec/dni?numero={$doc}";

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(15)
                ->get($endpoint);

            if (!$response->successful()) {
                $this->addError('document_number', 'Error al consultar API (' . $response->status() . ').');
                return;
            }

            $data = $response->json();


            if ($len === 11) {
                // RUC
                $this->name   = $data['nombre'] ?? $data['razonSocial'] ?? $data['razon_social'] ?? '';
                $this->street = $data['direccion'] ?? null;

                $this->mapUbigeoByCode($data['ubigeo'] ?? null);
            } else {
                // DNI
                $this->name = $data['full_name']
                    ?? trim(($data['first_last_name'] ?? '') . ' ' . ($data['second_last_name'] ?? '') . ' ' . ($data['first_name'] ?? ''));

                // DNI no trae dirección/ubigeo normalmente
            }

            // Limpia error si todo ok
            $this->resetErrorBag('document_number');
        } catch (\Throwable $e) {
            $this->addError('document_number', 'Error de conexión con la API: ' . $e->getMessage());
        }
    }


    private function mapUbigeoByCode($ubigeo): void
    {
        if (!$ubigeo) return;

        $ubigeo = str_pad((string)$ubigeo, 6, '0', STR_PAD_LEFT);

        $district = District::where('id', $ubigeo)->first();
        if (!$district) return;

        // Setear IDs
        $this->department_id = (string) $district->department_id;

        // Cargar provincias del dep
        $this->provinces = Province::where('department_id', $this->department_id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->province_id = (string) $district->province_id;

        // Cargar distritos de la provincia
        $this->districts = District::where('province_id', $this->province_id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
        $this->district_id = (string) $district->id;
    }



    public function setTab(string $tab): void
    {
        $allowed = ['general', 'accounting'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    protected function rules(): array
    {
        return [
            'document_number' => ['nullable', 'string', 'max:50'],
            'company_type_id' => ['nullable', 'integer', Rule::exists('company_types', 'id')],
            'document_type_id' => ['nullable', 'integer', Rule::exists('document_types', 'id')],

            'is_customer' => ['boolean'],
            'is_supplier' => ['boolean'],

            'name' => ['required', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],

            'department_id' => ['nullable', 'string'],
            'province_id' => ['nullable', 'string'],
            'district_id' => ['nullable', 'string'],

            'email' => [$this->portal_access ? 'required' : 'nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'string', 'max:255'],

            'pricelist_id' => ['nullable', 'integer', Rule::exists('pricelists', 'id')],
            'currency_id' => ['nullable', 'integer', Rule::exists('currencies', 'id')],

            'status' => ['required', 'integer', Rule::in([0, 1])],
            'portal_access' => ['boolean'],

            'map' => ['nullable', 'string', 'max:500'],

            'account_receivable_id' => ['nullable', 'integer', Rule::exists('accounts', 'id')],
            'account_payable_id' => ['nullable', 'integer', Rule::exists('accounts', 'id')],
        ];
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->portal_access) {
            $this->validate([
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            ]);
        }

        DB::transaction(function () use ($data) {
            $partner = Partner::create([
                'document_number' => $data['document_number'],
                'company_type_id' => $data['company_type_id'],
                'document_type_id' => $data['document_type_id'],

                'is_customer' => $data['is_customer'],
                'is_supplier' => $data['is_supplier'],

                'name' => $data['name'],
                'street' => $data['street'],

                'department_id' => $data['department_id'],
                'province_id' => $data['province_id'],
                'district_id' => $data['district_id'],

                'pricelist_id' => $data['pricelist_id'],
                'currency_id' => $data['currency_id'],

                'status' => $data['status'],
                'portal_access' => $data['portal_access'],

                'map' => $data['map'],

                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'mobile' => $data['mobile'] ?? null,
                'website' => $data['website'] ?? null,

                'account_receivable_id' => $data['account_receivable_id'],
                'account_payable_id' => $data['account_payable_id'],
            ]);

            //  Si habilita portal → crear User
            if ($data['portal_access']) {

                $user = User::create([
                    'name' => $partner->name,
                    'email' => $partner->email,
                    'password' => Hash::make(Str::random(32)), // random
                    'partner_id' => $partner->id,
                ]);

                // Asegurar rol y asignar
                $role = Role::firstOrCreate(['name' => 'clienteexterno', 'guard_name' => 'web']);
                $user->assignRole($role);

                // Enviar link para que cree su password
                //Password::sendResetLink(['email' => $user->email]);

                $token = Password::createToken($user);

                $resetUrl = url(route('password.reset', [
                    'token' => $token,
                    'email' => $user->email,
                ], false));

                Mail::to($user->email)->send(
                    new PortalAccessMail($partner->name, $resetUrl)
                );
            }
        });

        //session()->flash('success', 'Partner creado correctamente.');
        session()->flash(
            'success',
            $this->portal_access
                ? 'Partner creado. Se envió un correo para definir la contraseña.'
                : 'Partner creado correctamente.'
        );
        return redirect()->route('admin.partners.index');
    }


    public function updatedDepartmentId(): void
    {

        $this->province_id = null;
        $this->district_id = null;

        $this->provinces = Province::where('department_id', $this->department_id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->districts = [];
    }

    public function updatedProvinceId(): void
    {

        $this->district_id = null;

        $this->districts = District::where('province_id', $this->province_id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
    }


    public function getDepartmentsProperty()
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }

    public function getProvincesPropertyBorrar()
    {
        if (!$this->department_id) return collect();
        return Province::where('department_id', $this->department_id)
            ->orderBy('name')
            ->get(['id', 'name', 'department_id']);
    }


    public function render()
    {
        return view('livewire.admin.partner.partner-create');
    }
}
