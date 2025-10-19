<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditableObserver
{
    /**
     * Fields excluded from audit payloads.
     *
     * @var array<int, string>
     */
    protected array $except = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function created(Model $model): void
    {
        $this->store($model, 'created', [], $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->store(
            $model,
            'updated',
            $model->getOriginal(),
            $model->getChanges()
        );
    }

    public function deleted(Model $model): void
    {
        $this->store($model, 'deleted', $model->getOriginal(), []);
    }

    protected function store(Model $model, string $event, array $old, array $new): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'event' => $event,
            'old_values' => $this->filter($old),
            'new_values' => $this->filter($new),
            'created_at' => now(),
        ]);
    }

    protected function filter(array $values): array
    {
        return collect($values)
            ->except($this->except)
            ->toArray();
    }
}
