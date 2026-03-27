<?php

namespace App\Livewire\Admin\Company;

use Livewire\Component;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CompanyEdit extends Component
{

    use WithFileUploads;
    public Company $company;
    public string $tab = 'general';

    public ?string $ruc = null;
    public ?string $razonsocial = null;
    public ?string $nombrecomercial = null;

    public ?string $direccion = null;
    public ?string $celular = null;
    public ?string $telefono = null;

    public ?string $correo = null;
    public ?string $smtp = null;
    public ?string $password = null;
    public ?string $puerto = null;

    public ?string $department_id = null;
    public ?string $province_id = null;
    public ?string $district_id = null;
    public ?string $ubigeo = null;

    public ?string $logo = null;
    public $new_logo = null;

    public ?string $soluser = null;
    public ?string $solpass = null;
    public ?string $certificado = null;
    public ?string $certificate_path = null;
    public ?string $fechainiciocertificado = null;
    public ?string $fechafincertificado = null;
    public ?string $cliente_id = null;
    public ?string $cliente_secret = null;

    public $new_certificate = null;

    public bool $production = false;
    public bool $state = true;
    public ?string $ublversion = null;
    public ?string $detraccion = null;
    public ?string $pago = null;
    public $currency_id = null;

    public array $provinces = [];
    public array $districts = [];

    public function mount(): void
    {
        $this->company = Company::current();

        $this->fillFromModel();
        $this->loadProvinces();
        $this->loadDistricts();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    protected function fillFromModel(): void
    {
        $this->ruc = $this->company->ruc;
        $this->razonsocial = $this->company->razonsocial;
        $this->nombrecomercial = $this->company->nombrecomercial;

        $this->direccion = $this->company->direccion;
        $this->celular = $this->company->celular;
        $this->telefono = $this->company->telefono;

        $this->correo = $this->company->correo;
        $this->smtp = $this->company->smtp;
        $this->password = $this->company->password;
        $this->puerto = $this->company->puerto;

        $this->department_id = $this->company->department_id;
        $this->province_id = $this->company->province_id;
        $this->district_id = $this->company->district_id;
        $this->ubigeo = $this->company->ubigeo;

        $this->logo = $this->company->logo;

        $this->soluser = $this->company->soluser;
        $this->solpass = $this->company->solpass;
        $this->certificado = $this->company->certificado;
        $this->certificate_path = $this->company->certificate_path;
        $this->fechainiciocertificado = optional($this->company->fechainiciocertificado)->format('Y-m-d');
        $this->fechafincertificado = optional($this->company->fechafincertificado)->format('Y-m-d');
        $this->cliente_id = $this->company->cliente_id;
        $this->cliente_secret = $this->company->cliente_secret;

        $this->production = (bool) $this->company->production;
        $this->state = (bool) $this->company->state;
        $this->ublversion = $this->company->ublversion;
        $this->detraccion = $this->company->detraccion !== null ? (string) $this->company->detraccion : null;
        $this->pago = $this->company->pago;
        $this->currency_id = $this->company->currency_id;
    }

    public function rules(): array
    {
        return [
            'ruc' => ['nullable', 'string', 'max:20'],
            'razonsocial' => ['nullable', 'string', 'max:255'],
            'nombrecomercial' => ['nullable', 'string', 'max:255'],

            'direccion' => ['nullable', 'string', 'max:255'],
            'celular' => ['nullable', 'string', 'max:30'],
            'telefono' => ['nullable', 'string', 'max:30'],

            'correo' => ['nullable', 'email', 'max:255'],
            'smtp' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'puerto' => ['nullable', 'string', 'max:20'],

            'department_id' => ['nullable', 'exists:departments,id'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'ubigeo' => ['nullable', 'string', 'max:20'],

            'new_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'soluser' => ['nullable', 'string', 'max:255'],
            'solpass' => ['nullable', 'string', 'max:255'],
            'certificado' => ['nullable', 'string'],

            'certificate_path' => ['nullable', 'string', 'max:255'],
            'new_certificate' => ['nullable', 'file', 'max:2048'],

            'fechainiciocertificado' => ['nullable', 'date'],
            'fechafincertificado' => ['nullable', 'date', 'after_or_equal:fechainiciocertificado'],
            'cliente_id' => ['nullable', 'string', 'max:255'],
            'cliente_secret' => ['nullable', 'string', 'max:255'],

            'production' => ['boolean'],
            'state' => ['boolean'],
            'ublversion' => ['nullable', 'string', 'max:50'],
            'detraccion' => ['nullable', 'numeric', 'between:0,999999.9999'],
            'pago' => ['nullable', 'string', 'max:255'],
            'currency_id' => ['nullable', 'exists:currencies,id'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'ruc' => 'RUC',
            'razonsocial' => 'razón social',
            'nombrecomercial' => 'nombre comercial',
            'correo' => 'correo',
            'department_id' => 'departamento',
            'province_id' => 'provincia',
            'district_id' => 'distrito',
            'new_logo' => 'logo',
            'new_certificate' => 'certificado PEM',
            'certificate_path' => 'ruta del certificado',
            'fechainiciocertificado' => 'fecha inicio certificado',
            'fechafincertificado' => 'fecha fin certificado',
            'currency_id' => 'moneda',
        ];
    }

    public function updatedDepartmentId($value): void
    {
        $this->province_id = null;
        $this->district_id = null;
        $this->ubigeo = null;

        $this->loadProvinces();
        $this->districts = [];
    }

    public function updatedProvinceId($value): void
    {
        $this->district_id = null;
        $this->ubigeo = null;

        $this->loadDistricts();
    }

    public function updatedDistrictId($value): void
    {
        if (!$value) {
            $this->ubigeo = null;
            return;
        }

        $district = District::query()->find($value);
        $this->ubigeo = $district?->id;
    }

    protected function loadProvinces(): void
    {
        if (!$this->department_id) {
            $this->provinces = [];
            return;
        }

        $this->provinces = Province::query()
            ->where('department_id', $this->department_id)
            ->orderBy('name')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
            ])
            ->toArray();
    }

    protected function loadDistricts(): void
    {
        if (!$this->province_id) {
            $this->districts = [];
            return;
        }

        $this->districts = District::query()
            ->where('province_id', $this->province_id)
            ->orderBy('name')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
            ])
            ->toArray();
    }




    /**
     * Obtiene la carpeta raíz fija de la empresa en AWS/S3.
     *
     * IMPORTANTE:
     * - Esta carpeta NO debe depender de la razón social visible.
     * - La razón social puede cambiar en el sistema, pero la carpeta en S3 debe mantenerse estable.
     * - Por eso usamos el campo `razonsocialaws` como identificador persistente.
     * - Si aún no existe, se genera una sola vez con el formato: company-{id}.
     *
     * Ejemplos:
     * - company-1
     * - company-25
     *
     * Allí se almacenan recursos de la empresa como:
     * - logos
     * - certificados
     * - documentos electrónicos
     * - otros archivos privados o públicos
     */
    protected function getTenantAwsFolder(): string
    {
        // Carpeta persistente ya guardada en BD
        $folder = $this->company->razonsocialaws;

        // Si todavía no existe, se crea usando el ID de la empresa.
        // El ID es estable y no cambia aunque cambie la razón social.
        if (blank($folder)) {
            $folder = 'company-' . $this->company->id;
        }

        return $folder;
    }

    public function save(): void
    {
        // Validar todos los campos del formulario antes de procesar archivos o guardar cambios
        $this->validate();

        // Obtener la carpeta raíz fija de la empresa en AWS/S3
        // Esta carpeta se reutiliza siempre para no romper rutas antiguas
        $razonSocialAwsFinal = $this->getTenantAwsFolder();

        /*
    |--------------------------------------------------------------------------
    | LOGO
    |--------------------------------------------------------------------------
    |
    | El logo se guarda en S3 público porque debe poder mostrarse en la web.
    | Ruta esperada:
    | company-{id}/logos/archivo.png
    |
    | Si existe un logo anterior, primero intentamos eliminarlo para evitar
    | archivos huérfanos y mantener limpio el bucket.
    |
    */
        if ($this->new_logo) {
            if (!empty($this->company->logo)) {
                try {
                    if (Storage::disk('s3_public')->exists($this->company->logo)) {
                        Storage::disk('s3_public')->delete($this->company->logo);
                    }
                } catch (\Throwable $e) {
                    // Si falla la eliminación del archivo anterior, no detenemos el proceso.
                    // El objetivo principal es permitir que el nuevo logo sí se guarde.
                }
            }

            // Guardar el nuevo logo en la carpeta pública fija de la empresa
            $this->logo = $this->new_logo->store($razonSocialAwsFinal . '/logos', 's3_public');
        }

        /*
    |--------------------------------------------------------------------------
    | CERTIFICADO PEM (S3 PRIVADO)
    |--------------------------------------------------------------------------
    |
    | El certificado digital se guarda en S3 privado porque se utilizará para
    | firmar documentos electrónicos y NO debe ser accesible públicamente.
    |
    | Ruta esperada:
    | company-{id}/certificates/certificate_YYYYmmdd_His.pem
    |
    */
        if ($this->new_certificate) {

            // 1) Validar extensión real del archivo subido
            $originalName = $this->new_certificate->getClientOriginalName();
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if ($extension !== 'pem') {
                $this->addError('new_certificate', 'El certificado debe estar en formato PEM.');
                return;
            }

            // 2) Validar tamaño máximo permitido (2MB)
            $maxBytes = 2 * 1024 * 1024;
            if ($this->new_certificate->getSize() > $maxBytes) {
                $this->addError('new_certificate', 'El archivo PEM es demasiado grande (máx. 2 MB).');
                return;
            }

            // 3) Leer contenido del archivo PEM para validar que tenga:
            //    - CERTIFICATE
            //    - PRIVATE KEY
            // Esto es importante porque el archivo servirá para firmar comprobantes electrónicos.
            $pem = file_get_contents($this->new_certificate->getRealPath());

            $hasCert = preg_match('/-----BEGIN CERTIFICATE-----.*?-----END CERTIFICATE-----/s', $pem);
            $hasKey  = preg_match('/-----BEGIN (?:RSA |EC |ENCRYPTED )?PRIVATE KEY-----.*?-----END (?:RSA |EC |ENCRYPTED )?PRIVATE KEY-----/s', $pem);

            if (!$hasCert || !$hasKey) {
                $this->addError(
                    'new_certificate',
                    'El archivo PEM debe contener CERTIFICATE y PRIVATE KEY.'
                );
                return;
            }

            // 4) Si ya existe un certificado anterior, intentamos eliminarlo del S3 privado
            // para no dejar archivos viejos sin uso.
            if (!empty($this->company->certificate_path)) {
                try {
                    if (Storage::disk('s3_private')->exists($this->company->certificate_path)) {
                        Storage::disk('s3_private')->delete($this->company->certificate_path);
                    }
                } catch (\Throwable $e) {
                    // Si falla la eliminación del certificado anterior, no detenemos el guardado.
                    // Lo importante es permitir guardar el nuevo certificado válido.
                }
            }

            // 5) Definir carpeta y nombre del nuevo certificado
            $folder = $razonSocialAwsFinal . '/certificates';
            $newName = 'certificate_' . now()->format('Ymd_His') . '.pem';

            // 6) Guardar el nuevo certificado en S3 privado
            $path = Storage::disk('s3_private')->putFileAs(
                $folder,
                $this->new_certificate,
                $newName,
                ['visibility' => 'private']
            );

            // Guardar la ruta final del certificado en la propiedad para luego persistirla en BD
            $this->certificate_path = $path;
        }

        /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR EMPRESA
    |--------------------------------------------------------------------------
    |
    | Aquí persistimos todos los cambios en la tabla companies.
    |
    | IMPORTANTE:
    | - `razonsocial` puede cambiar libremente en el sistema.
    | - `razonsocialaws` debe permanecer fijo una vez creado.
    | - Eso evita que cambien las rutas de S3 y se rompan logos, certificados
    |   o documentos ya almacenados.
    |
    */
        $this->company->update([
            'ruc' => $this->ruc,
            'razonsocial' => $this->razonsocial,
            'razonsocialaws' => $razonSocialAwsFinal, // carpeta raíz fija y persistente en AWS
            'nombrecomercial' => $this->nombrecomercial,

            'direccion' => $this->direccion,
            'celular' => $this->celular,
            'telefono' => $this->telefono,

            'correo' => $this->correo,
            'smtp' => $this->smtp,
            'password' => $this->password,
            'puerto' => $this->puerto,

            'department_id' => $this->department_id,
            'province_id' => $this->province_id,
            'district_id' => $this->district_id,
            'ubigeo' => $this->ubigeo,

            'logo' => $this->logo,

            'soluser' => $this->soluser,
            'solpass' => $this->solpass,
            'certificado' => $this->certificado,
            'certificate_path' => $this->certificate_path,
            'fechainiciocertificado' => $this->fechainiciocertificado ?: null,
            'fechafincertificado' => $this->fechafincertificado ?: null,
            'cliente_id' => $this->cliente_id,
            'cliente_secret' => $this->cliente_secret,

            'production' => $this->production,
            'state' => $this->state,
            'ublversion' => $this->ublversion,
            'detraccion' => $this->detraccion ?: null,
            'pago' => $this->pago,
            'currency_id' => $this->currency_id ?: null,
        ]);

        // Limpiar archivos temporales de Livewire después de guardar
        $this->new_certificate = null;
        $this->new_logo = null;

        // Recargar modelo y propiedades para reflejar datos actualizados en pantalla
        $this->company->refresh();
        $this->fillFromModel();

        // Notificación de éxito
        $this->dispatch(
            'notifyd',
            title: 'TICOM',
            text: 'La información de la empresa fue actualizada correctamente.',
            icon: 'success'
        );
    }




    public function removeLogo(): void
    {
        if (!empty($this->company->logo)) {
            try {
                if (Storage::disk('s3_public')->exists($this->company->logo)) {
                    Storage::disk('s3_public')->delete($this->company->logo);
                }
            } catch (\Throwable $e) {
            }
        }

        $this->company->update(['logo' => null]);

        $this->logo = null;
        $this->new_logo = null;
        $this->company->refresh();

        $this->dispatch(
            'notify',
            title: 'TICOM',
            text: 'El logo fue eliminado correctamente.',
            icon: 'success'
        );
    }

    public function render()
    {
        return view('livewire.admin.company.company-edit', [
            'departments' => Department::query()->orderBy('name')->get(),
            'currencies' => class_exists(Currency::class)
                ? Currency::query()->orderBy('name')->get()
                : collect(),
        ]);
    }
}
