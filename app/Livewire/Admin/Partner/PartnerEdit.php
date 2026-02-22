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

use Illuminate\Support\Facades\Mail;
use App\Mail\PortalAccessMail;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Http;


class PartnerEdit extends Component
{

    public Partner $partner;

    public string $tab = 'general';

    // ====== Campos partner ======
    public ?string $document_number = null;
    public ?int $company_type_id = null;
    public ?int $document_type_id = null;

    public bool $is_customer = true;
    public bool $is_supplier = false;

    public string $name = '';
    public ?string $street = null;

    // Ubigeo (ids string: "15", "1501", "150101")
    public ?string $department_id = null;
    public ?string $province_id = null;
    public ?string $district_id = null;

    // arrays para selects dependientes
    public array $provinces = [];
    public array $districts = [];

    public ?int $pricelist_id = null;
    public ?int $currency_id = null;

    public int $status = 1;
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

    public function mount(Partner $partner): void
    {
        $this->partner = $partner;

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

        // ✅ Precargar form desde partner
        $this->document_number = $partner->document_number;
        $this->company_type_id = $partner->company_type_id;
        $this->document_type_id = $partner->document_type_id;

        $this->is_customer = (bool) $partner->is_customer;
        $this->is_supplier = (bool) $partner->is_supplier;

        $this->name = (string) $partner->name;
        $this->street = $partner->street;

        $this->department_id = $partner->department_id;
        $this->province_id   = $partner->province_id;
        $this->district_id   = $partner->district_id;

        $this->pricelist_id = $partner->pricelist_id;
        $this->currency_id  = $partner->currency_id;

        $this->status = (int) $partner->status;
        $this->portal_access = (bool) $partner->portal_access;

        $this->email = $partner->email;
        $this->phone = $partner->phone;
        $this->whatsapp = $partner->whatsapp;
        $this->mobile = $partner->mobile;
        $this->website = $partner->website;

        $this->map = $partner->map;

        $this->account_receivable_id = $partner->account_receivable_id;
        $this->account_payable_id    = $partner->account_payable_id;

        // ✅ Cargar selects dependientes si ya hay ubigeo
        $this->provinces = $this->department_id
            ? Province::where('department_id', $this->department_id)->orderBy('name')->get(['id', 'name'])->toArray()
            : [];

        $this->districts = $this->province_id
            ? District::where('province_id', $this->province_id)->orderBy('name')->get(['id', 'name'])->toArray()
            : [];
    }




    public function setTab(string $tab): void
    {
        $allowed = ['general', 'accounting'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    protected function rules(): array
    {
        return [
            'document_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('partners', 'document_number')->ignore($this->partner->id),
            ],
            'company_type_id' => ['nullable', 'integer', Rule::exists('company_types', 'id')],
            'document_type_id' => ['nullable', 'integer', Rule::exists('document_types', 'id')],

            'is_customer' => ['boolean'],
            'is_supplier' => ['boolean'],

            'name' => ['required', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],

            'department_id' => ['nullable', 'string'],
            'province_id' => ['nullable', 'string'],
            'district_id' => ['nullable', 'string'],

            'email' => [
                Rule::requiredIf(fn() => (bool) $this->portal_access),
                'nullable',
                'email',
                'max:255',
                // email no debe existir en users, excepto el user del mismo partner
                Rule::when($this->portal_access, function () {
                    return Rule::unique('users', 'email')->ignore(
                        User::where('partner_id', $this->partner->id)->value('id')
                    );
                }),
            ],

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

    // ✅ (Opcional) Dejar disponible el botón buscar también en editar
    public function searchDocument(): void
    {
        $doc = preg_replace('/\D+/', '', (string) $this->document_number);
        $len = strlen($doc);

        if (!in_array($len, [8, 11], true)) {
            $this->addError('document_number', 'El documento debe tener 8 (DNI) o 11 (RUC) dígitos.');
            return;
        }

        // Si cambió el doc, validar que no exista en otros partners
        $exists = Partner::query()
            ->where('document_number', $doc)
            ->where('id', '!=', $this->partner->id)
            ->exists();

        if ($exists) {
            $this->addError('document_number', 'Este DNI/RUC ya existe en el sistema.');
            return;
        }

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
            $response = Http::withToken($token)->acceptJson()->timeout(15)->get($endpoint);

            if (!$response->successful()) {
                $this->addError('document_number', 'Error al consultar API (' . $response->status() . ').');
                return;
            }

            $data = $response->json();

            if ($len === 11) {
                $this->name   = $data['nombre'] ?? $data['razonSocial'] ?? $data['razon_social'] ?? '';
                $this->street = $data['direccion'] ?? null;
                $this->mapUbigeoByCode($data['ubigeo'] ?? null);
            } else {
                $this->name = $data['full_name']
                    ?? trim(($data['first_last_name'] ?? '') . ' ' . ($data['second_last_name'] ?? '') . ' ' . ($data['first_name'] ?? ''));
            }

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

        $this->department_id = (string) $district->department_id;

        $this->provinces = Province::where('department_id', $this->department_id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->province_id = (string) $district->province_id;

        $this->districts = District::where('province_id', $this->province_id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->district_id = (string) $district->id;
    }

    public function updatedDepartmentId(): void
    {
        $this->province_id = null;
        $this->district_id = null;

        $this->provinces = $this->department_id
            ? Province::where('department_id', $this->department_id)->orderBy('name')->get(['id', 'name'])->toArray()
            : [];

        $this->districts = [];
    }

    public function updatedProvinceId(): void
    {
        $this->district_id = null;

        $this->districts = $this->province_id
            ? District::where('province_id', $this->province_id)->orderBy('name')->get(['id', 'name'])->toArray()
            : [];
    }

    public function getDepartmentsProperty()
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }

    public function save()
    {
        $data = $this->validate();

        DB::transaction(function () use ($data) {

            $this->partner->update([
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

            // ✅ Portal access
            if (!empty($data['portal_access'])) {

                if (empty($this->partner->email)) {
                    throw new \RuntimeException('Email es requerido para habilitar acceso al portal.');
                }

                $user = User::where('partner_id', $this->partner->id)->first();

                if (!$user) {
                    // si no hay user asociado, crearlo
                    $user = User::firstOrCreate(
                        ['email' => $this->partner->email],
                        [
                            'name' => $this->partner->name,
                            'password' => Hash::make(Str::random(32)),
                            'partner_id' => $this->partner->id,
                        ]
                    );
                } else {
                    // si cambió el email en edición, actualizarlo
                    if ($user->email !== $this->partner->email) {
                        $user->email = $this->partner->email;
                    }
                    $user->name = $this->partner->name;
                    $user->partner_id = $this->partner->id;
                    $user->save();
                }

                $role = Role::firstOrCreate(['name' => 'clienteexterno', 'guard_name' => 'web']);
                $user->assignRole($role);

                // opcional: reenviar correo solo si quieres
                // $token = \Password::createToken($user);
                // $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));
                // Mail::to($user->email)->send(new PortalAccessMail($this->partner->name, $resetUrl));
            } else {
                // Si desactivan portal, no borramos user automáticamente (más seguro).
                // Si quieres, aquí puedes quitar rol, etc.
            }
        });

        session()->flash('success', 'Partner actualizado correctamente.');
        return redirect()->route('admin.partners.index');
    }


    public function render()
    {
        return view('livewire.admin.partner.partner-edit');
    }
}
