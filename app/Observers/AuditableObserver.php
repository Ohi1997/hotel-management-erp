<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditableObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created');
    }

    public function updated(Model $model): void
    {
        $this->record($model, 'updated');
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted');
    }

    protected function record(Model $model, string $event): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        AuditLog::create([
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'user_id' => Auth::id(),
            'event' => $event,
            'old_values' => $event === 'created' ? null : $model->getOriginal(),
            'new_values' => $event === 'deleted' ? null : $model->getAttributes(),
        ]);
    }
}
