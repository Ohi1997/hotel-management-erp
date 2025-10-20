<?php

namespace App\Livewire\Backoffice\Admin\Users;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = 'all';
    public string $status = 'all';
    public ?int $filterHotelId = null;
    public ?int $editingUserId = null;
    public int $formInstance = 0;
    public ?string $lastGeneratedPassword = null;

    #[On('user-saved')]
    public function handleUserSaved(): void
    {
        $this->editingUserId = null;
        $this->formInstance++;
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->filterHotelId = auth()->user()?->hotel_id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterHotelId(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->editingUserId = null;
        $this->lastGeneratedPassword = null;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'user-form');
    }

    public function openEdit(int $userId): void
    {
        $this->editingUserId = $userId;
        $this->lastGeneratedPassword = null;
        $this->formInstance++;
        $this->dispatch('modal-open', id: 'user-form');
    }

    public function toggleStatus(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->is(auth()->user())) {
            $this->dispatch('toast', type: 'error', message: 'You cannot deactivate your own account.');
            return;
        }

        $user->update(['status' => ! $user->status]);

        $message = $user->status ? 'User activated successfully.' : 'User deactivated successfully.';
        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function resetPassword(int $userId): void
    {
        $user = User::findOrFail($userId);
        $password = Str::random(12);

        $user->update(['password' => Hash::make($password)]);

        $this->lastGeneratedPassword = $password;
        $this->dispatch('toast', type: 'success', message: 'Password reset successfully.');
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->with(['roles', 'hotel'])
            ->when($this->search, function ($query): void {
                $query->where(function ($builder): void {
                    $builder->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->role !== 'all', function ($query): void {
                $query->whereHas('roles', function ($roleQuery): void {
                    $roleQuery->where('name', $this->role);
                });
            })
            ->when($this->status !== 'all', function ($query): void {
                $query->where('status', $this->status === 'active');
            })
            ->when($this->filterHotelId, function ($query): void {
                $query->where('hotel_id', $this->filterHotelId);
            })
            ->orderBy('name')
            ->paginate(12);
    }

    #[Computed]
    public function roles()
    {
        return Role::query()->orderBy('name')->get();
    }

    #[Computed]
    public function hotels()
    {
        return Hotel::query()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.backoffice.admin.users.index', [
            'users' => $this->users,
            'roles' => $this->roles,
            'hotels' => $this->hotels,
        ])->layout('layouts.backoffice', ['pageTitle' => 'User Management']);
    }
}
