<?php

namespace App\Livewire\Admin\SubscriptionPlan;

use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Planes de Suscripción')]
class SubscriptionPlanList extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    // ── Filtros ─────────────────────────────────────────────
    public string $search   = '';
    public string $status   = 'all';
    public int    $perPage  = 10;

    // ── Selección masiva ─────────────────────────────────────
    public array $selectedItems = [];
    public bool  $selectAll     = false;

    // ── Ordenamiento ─────────────────────────────────────────
    public string $sortField     = 'order';
    public string $sortDirection = 'asc';

    // ── Columnas opcionales ──────────────────────────────────
    public array $columns = [
        'order'          => false,
        'interval_label' => true,
    ];

    protected string $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->authorize('viewAny', SubscriptionPlan::class);
    }

    // ── Observadores ─────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    // ── Ordenamiento ─────────────────────────────────────────

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['id', 'name', 'order', 'active', 'interval_count'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->resetSelection();
    }

    // ── Selección masiva ─────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        if (! $value) {
            $this->selectedItems = [];
            return;
        }

        $this->selectedItems = $this->baseQuery()
            ->select('id')
            ->pluck('id')
            ->mapWithKeys(fn ($id) => [$id => true])
            ->toArray();
    }

    public function getSelectedCountProperty(): int
    {
        return count(array_keys(array_filter($this->selectedItems)));
    }

    private function resetSelection(): void
    {
        $this->selectAll     = false;
        $this->selectedItems = [];
    }

    // ── Toggle estado ────────────────────────────────────────

    public function toggleState(int $id): void
    {
        try {
            $plan = SubscriptionPlan::findOrFail($id);
            $this->authorize('update', $plan);

            $plan->active = ! $plan->active;
            $plan->save();

            $this->dispatch('show-swalindex', [
                'title' => 'Actualizado',
                'text'  => 'Estado del plan actualizado',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'No se pudo actualizar: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    // ── Eliminación individual ───────────────────────────────

    #[On('deleteSingle')]
    public function deleteSingle(int $id, string $name): void
    {
        try {
            $plan = SubscriptionPlan::findOrFail($id);
            $this->authorize('delete', $plan);

            // ✅ Bloquear si está en uso por productos o suscripciones
            if ($plan->isInUse()) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'El plan "' . $name . '" está asignado a productos o suscripciones activas.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            $plan->delete();
            unset($this->selectedItems[$id]);

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => 'El plan "' . $name . '" fue eliminado correctamente',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'Error al eliminar: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    // ── Eliminación masiva ───────────────────────────────────

    #[On('confirmDeleteSelected')]
    public function deleteSelected(): void
    {
        try {
            $ids = array_keys(array_filter($this->selectedItems));
            if (empty($ids)) return;

            $plans = SubscriptionPlan::whereIn('id', $ids)->get();

            foreach ($plans as $plan) {
                $this->authorize('delete', $plan);
            }

            // ✅ Bloquear si alguno está en uso
            $inUse = SubscriptionPlan::whereIn('id', $ids)
                ->where(fn ($q) =>
                    $q->whereHas('productTemplates')
                      ->orWhereHas('subscriptions')
                )
                ->exists();

            if ($inUse) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'Uno o más planes están en uso. Desvincúlalos primero.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            SubscriptionPlan::whereIn('id', $ids)->delete();
            $this->resetSelection();

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => count($ids) . ' plan(es) eliminado(s) correctamente',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'Error al eliminar: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    // ── Query base ───────────────────────────────────────────

    private function baseQuery()
    {
        $query = SubscriptionPlan::query();

        if ($this->search) {
            $s = trim($this->search);
            $query->where('name', 'like', "%{$s}%");
        }

        if ($this->status === 'active') {
            $query->where('active', true);
        } elseif ($this->status === 'inactive') {
            $query->where('active', false);
        }

        return $query;
    }

    // ── Render ───────────────────────────────────────────────

    public function render()
    {
        $plans = $this->baseQuery()
            ->withCount(['productTemplates', 'subscriptions'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.subscription-plan.subscription-plan-list', compact('plans'));
    }
}
