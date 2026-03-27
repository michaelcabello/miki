<?php

namespace App\Livewire\Admin\SubscriptionPlan;

use App\Models\SubscriptionPlan;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Crear Plan de Suscripción')]
class SubscriptionPlanCreate extends Component
{
    use AuthorizesRequests;

    public string $tab = 'general';

    // ── Campos ───────────────────────────────────────────────
    public string $name           = '';
    public int    $interval_count = 1;
    public string $interval_unit  = 'month';
    public bool   $active         = true;
    public int    $order          = 0;

    public function mount(): void
    {
        $this->authorize('create', SubscriptionPlan::class);
    }

    public function setTab(string $tab): void
    {
        $allowed   = ['general'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    protected function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100', Rule::unique('subscription_plans', 'name')],
            'interval_count' => ['required', 'integer', 'min:1', 'max:365'],
            'interval_unit'  => ['required', Rule::in(['day', 'week', 'month', 'year'])],
            'active'         => ['boolean'],
            'order'          => ['required', 'integer', 'min:0', 'max:65535'],
        ];
    }

    protected array $messages = [
        'name.required'           => 'El nombre es obligatorio.',
        'name.unique'             => 'Ya existe un plan con ese nombre.',
        'interval_count.required' => 'El intervalo es obligatorio.',
        'interval_count.min'      => 'El intervalo mínimo es 1.',
        'interval_unit.in'        => 'Unidad de tiempo no válida.',
    ];

    /**
     * Guarda el plan con transacción DB.
     */
    public function save(): mixed
    {
        $this->authorize('create', SubscriptionPlan::class);

        // Capitaliza el nombre
        $this->name = mb_convert_case(
            mb_strtolower(trim($this->name), 'UTF-8'),
            MB_CASE_TITLE,
            'UTF-8'
        );

        $data = $this->validate();

        DB::beginTransaction();

        try {
            SubscriptionPlan::create($data);

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Bien hecho',
                'text'  => 'Plan "' . $data['name'] . '" creado correctamente.',
            ]);

            return redirect()->route('admin.subscription-plans.index');
        } catch (QueryException $e) {
            DB::rollBack();

            if ($e->getCode() === '23000') {
                $this->addError('name', 'Ya existe un plan con ese nombre.');
                return null;
            }

            Log::error('Error al crear SubscriptionPlan', [
                'name'    => $data['name'],
                'error'   => $e->getMessage(),
                'usuario' => auth()->id(),
            ]);

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error de base de datos',
                'text'  => 'No se pudo guardar. Por favor intenta nuevamente.',
            ]);

            return null;
        } catch (AuthorizationException $e) {
            DB::rollBack();

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Sin permiso',
                'text'  => 'No tienes permiso para crear planes.',
            ]);

            return redirect()->route('admin.subscription-plans.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error inesperado al crear SubscriptionPlan', [
                'error'   => $e->getMessage(),
                'usuario' => auth()->id(),
            ]);

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error inesperado',
                'text'  => 'Ocurrió un problema. Contacta al administrador.',
            ]);

            return null;
        }
    }

    public function render()
    {
        return view('livewire.admin.subscription-plan.subscription-plan-create');
    }
}
