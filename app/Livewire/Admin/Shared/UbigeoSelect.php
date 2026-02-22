<?php

namespace App\Livewire\Admin\Shared;

use Livewire\Component;
use App\Models\Department;
use App\Models\Province;
use App\Models\District;

class UbigeoSelect extends Component
{

    public ?string $department_id = null;
    public ?string $province_id = null;
    public ?string $district_id = null;

    public function mount($departmentId = null, $provinceId = null, $districtId = null): void
    {
        $this->department_id = $departmentId;
        $this->province_id = $provinceId;
        $this->district_id = $districtId;
    }

    public function updatedDepartmentId($value): void
    {
        // Al cambiar departamento, resetea provincia/distrito
        $this->province_id = null;
        $this->district_id = null;
    }

    public function updatedProvinceId($value): void
    {
        // Al cambiar provincia, resetea distrito
        $this->district_id = null;
    }

    public function getDepartmentsProperty()
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }

    public function getProvincesProperty()
    {
        if (!$this->department_id) return collect();
        return Province::where('department_id', $this->department_id)
            ->orderBy('name')
            ->get(['id', 'name', 'department_id']);
    }

    public function getDistrictsProperty()
    {
        if (!$this->province_id) return collect();
        return District::where('province_id', $this->province_id)
            ->orderBy('name')
            ->get(['id', 'name', 'province_id', 'department_id']);
    }

    public function render()
    {
        return view('livewire.admin.shared.ubigeo-select');
    }
}
