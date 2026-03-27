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

#[Title('Editar Plan de Suscripción')]
class SubscriptionPlanEdit extends Component
{
    use AuthorizesRequests;

    public SubscriptionPlan $plan;

    public string $tab = 'general';

    // ── Campos editables ─────────────────────────────────────
    public string $name           = '';
    public int    $interval_count = 1;
    public string $interval_unit  = 'month';
    public bool   $active         = true;
    public int    $order          = 0;

    public function mount(SubscriptionPlan $plan): void
    {
        $this->authorize('update', $plan);

        $this->plan           = $plan;
        $this->name           = (string) $plan->name;
        $this->interval_count = (int)    $plan->interval_count;
        $this->interval_unit  = (string) $plan->interval_unit;
        $this->active         = (bool)   $plan->active;
        $this->order          = (int)    $plan->order;
    }

    public function setTab(string $tab): void
    {
        $allowed   = ['general'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    protected function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100', Rule::unique('subscription_plans', 'name')->ignore($this->plan->id)],
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
     * Actualiza el plan con transacción DB.
     */
    public function update(): mixed
    {
        $this->authorize('update', $this->plan);

        $this->name = mb_convert_case(
            mb_strtolower(trim($this->name), 'UTF-8'),
            MB_CASE_TITLE,
            'UTF-8'
        );

        $data = $this->validate();

        // Sin cambios reales → no escribir en BD
        $sinCambios = $this->plan->name           === $data['name']
            && $this->plan->interval_count === $data['interval_count']
            && $this->plan->interval_unit  === $data['interval_unit']
            && $this->plan->active         === $data['active']
            && $this->plan->order          === $data['order'];

        if ($sinCambios) {
            session()->flash('swal', [
                'icon'  => 'info',
                'title' => 'Sin cambios',
                'text'  => 'No se detectaron cambios en el registro.',
            ]);
            return redirect()->route('admin.subscription-plans.index');
        }

        DB::beginTransaction();

        try {
            $this->plan->update($data);

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Bien hecho',
                'text'  => 'Plan "' . $data['name'] . '" actualizado correctamente.',
            ]);

            return redirect()->route('admin.subscription-plans.index');
        } catch (QueryException $e) {
            DB::rollBack();

            if ($e->getCode() === '23000') {
                $this->addError('name', 'Ese nombre ya fue registrado por otro proceso.');
                return null;
            }

            Log::error('Error al actualizar SubscriptionPlan', [
                'id'      => $this->plan->id,
                'error'   => $e->getMessage(),
                'usuario' => auth()->id(),
            ]);

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error de base de datos',
                'text'  => 'No se pudo actualizar. Por favor intenta nuevamente.',
            ]);

            return null;
        } catch (AuthorizationException $e) {
            DB::rollBack();

            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Sin permiso',
                'text'  => 'No tienes permiso para actualizar planes.',
            ]);

            return redirect()->route('admin.subscription-plans.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error inesperado al actualizar SubscriptionPlan', [
                'id'      => $this->plan->id,
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
        return view('livewire.admin.subscription-plan.subscription-plan-edit');
    }
}
