<?php

namespace App\Livewire\Backoffice\Admin\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Matrix extends Component
{
    public ?int $selectedRoleId = null;

    /**
     * @var array<int, string>
     */
    public array $roleNames = [];

    /**
     * @var array<int, array{ id:int, name:string }>
     */
    public array $permissions = [];

    /**
     * @var array<int, string>
     */
    public array $selectedPermissions = [];

    public function mount(): void
    {
        $this->roleNames = Role::query()->orderBy('name')->pluck('name', 'id')->all();
        $this->permissions = Permission::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($permission) => ['id' => $permission->id, 'name' => $permission->name])
            ->all();

        $this->selectedRoleId = array_key_first($this->roleNames);

        if ($this->selectedRoleId) {
            $this->loadPermissions($this->selectedRoleId);
        }
    }

    public function updatedSelectedRoleId(?int $roleId): void
    {
        $this->loadPermissions($roleId);
    }

    public function save(): void
    {
        if (! $this->selectedRoleId) {
            return;
        }

        $role = Role::findOrFail($this->selectedRoleId);
        $role->syncPermissions($this->selectedPermissions);

        $this->dispatch('toast', type: 'success', message: 'Permissions updated successfully.');
    }

    public function togglePermission(string $permission): void
    {
        if (in_array($permission, $this->selectedPermissions, true)) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, [$permission]));
            return;
        }

        $this->selectedPermissions[] = $permission;
    }

    public function render()
    {
        return view('livewire.backoffice.admin.roles.matrix', [
            'roleNames' => $this->roleNames,
            'permissions' => $this->permissions,
        ])->layout('layouts.backoffice', ['pageTitle' => 'Role & Permission Management']);
    }

    protected function loadPermissions(?int $roleId): void
    {
        if (! $roleId) {
            $this->selectedPermissions = [];
            return;
        }

        $role = Role::findOrFail($roleId);
        $this->selectedPermissions = $role->permissions->pluck('name')->all();
    }
}
