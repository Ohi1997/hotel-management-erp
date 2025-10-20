<?php

namespace App\Livewire\Backoffice\Admin\Users;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Form extends Component
{
    public ?int $userId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public array $roles = [];
    public array $hotels = [];

    public function mount(?int $userId = null): void
    {
        $this->roles = Role::query()->orderBy('name')->pluck('name')->all();
        $this->hotels = Hotel::query()->orderBy('name')->pluck('name', 'id')->all();
        $this->form = $this->defaults();
        $this->loadUser($userId);
    }

    public function updatedUserId(?int $userId): void
    {
        $this->loadUser($userId);
    }

    public function save(): void
    {
        $validated = $this->validate();
        $payload = $validated['form'];
        $role = $payload['role'];

        unset($payload['role'], $payload['password_confirmation']);

        if (blank($payload['password'])) {
            unset($payload['password']);
        }

        $payload['status'] = (bool) $payload['status'];

        $user = User::updateOrCreate(
            ['id' => $this->userId],
            $payload
        );

        $user->syncRoles([$role]);

        $message = $this->userId ? 'User updated successfully.' : 'User created successfully.';

        $this->dispatch('user-saved', id: $user->id);
        $this->dispatch('modal-close', id: 'user-form');
        $this->dispatch('toast', type: 'success', message: $message);

        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.backoffice.admin.users.form', [
            'roles' => $this->roles,
            'hotels' => $this->hotels,
        ]);
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'form.password' => [
                Rule::requiredIf($this->userId === null),
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'form.password_confirmation' => ['nullable', 'string', 'min:8'],
            'form.role' => ['required', Rule::in($this->roles ?: ['admin'])],
            'form.hotel_id' => ['nullable', 'exists:hotels,id'],
            'form.status' => ['required', 'boolean'],
        ];
    }

    protected function loadUser(?int $userId): void
    {
        $this->userId = $userId;
        $this->resetValidation();
        $this->form = $this->defaults();

        if ($userId) {
            $user = User::with('roles')->findOrFail($userId);
            $this->form = array_merge(
                $this->form,
                $user->only(['name', 'email', 'hotel_id', 'status'])
            );
            $this->form['role'] = $user->roles->first()?->name ?? ($this->roles[0] ?? 'admin');
        }
    }

    protected function defaults(): array
    {
        return [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'role' => $this->roles[0] ?? 'admin',
            'hotel_id' => auth()->user()?->hotel_id,
            'status' => true,
        ];
    }

    protected function resetForm(): void
    {
        $this->userId = null;
        $this->form = $this->defaults();
        $this->resetValidation();
    }
}
