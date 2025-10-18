<?php

namespace App\Livewire\Backoffice\WakeUps;

use App\Models\WakeUp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.backoffice')]
class Index extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', WakeUp::class);
    }

    #[Url(history: true)]
    public string $search = '';

    #[Url(as: 'status', history: true)]
    public string $statusFilter = 'all';

    public bool $showFormModal = false;
    public ?int $wakeUpId = null;
    public int $formKey = 0;

    #[On('wake-up-saved')]
    public function handleSaved(): void
    {
        $this->dispatch('toast-notify', type: 'success', message: 'Wake-up scheduled.');
        $this->closeModal();
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openModal(?int $wakeUpId = null): void
    {
        if ($wakeUpId) {
            $wakeUp = WakeUp::findOrFail($wakeUpId);
            $this->authorize('update', $wakeUp);
        } else {
            $this->authorize('create', WakeUp::class);
        }
        $this->wakeUpId = $wakeUpId;
        $this->formKey++;
        $this->showFormModal = true;
    }

    public function closeModal(): void
    {
        $this->showFormModal = false;
        $this->wakeUpId = null;
    }

    public function markCompleted(int $id): void
    {
        $wakeUp = WakeUp::findOrFail($id);
        $this->authorize('update', $wakeUp);
        $wakeUp->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $this->dispatch('toast-notify', type: 'success', message: 'Wake-up marked complete.');
    }

    public function delete(int $id): void
    {
        $wakeUp = WakeUp::findOrFail($id);
        $this->authorize('delete', $wakeUp);
        $wakeUp->delete();
        $this->dispatch('toast-notify', type: 'deleted', message: 'Wake-up removed.');
    }

    public function getWakeUpsProperty()
    {
        return WakeUp::query()
            ->with(['customer', 'booking'])
            ->when($this->statusFilter !== 'all', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when($this->search, function (Builder $query) {
                $term = "%{$this->search}%";
                $query->whereHas('customer', fn (Builder $customerQuery) => $customerQuery->where('name', 'like', $term));
            })
            ->orderByDesc('scheduled_for')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.backoffice.wake-ups.index', [
            'wakeUps' => $this->wakeUps,
        ]);
    }
}
