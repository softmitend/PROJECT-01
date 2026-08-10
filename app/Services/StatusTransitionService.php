<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\MemberOrder;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StatusTransitionService
{
    public function transition(Model $trackable, OrderStatus $newStatus, ?User $changedBy = null, ?string $note = null): Model
    {
        $scope = $this->scopeFor($trackable);

        if (! $newStatus->isUsableFor($scope)) {
            throw ValidationException::withMessages([
                'status_id' => 'Status tidak aktif atau scope status tidak sesuai.',
            ]);
        }

        return DB::transaction(function () use ($trackable, $newStatus, $changedBy, $note) {
            $field = $this->statusFieldFor($trackable);
            $oldStatusId = $trackable->{$field};

            $trackable->forceFill([$field => $newStatus->id])->save();

            $trackable->statusHistories()->create([
                'old_status_id' => $oldStatusId,
                'new_status_id' => $newStatus->id,
                'note' => $note,
                'changed_by' => $changedBy?->id,
            ]);

            return $trackable->refresh();
        });
    }

    public function clearOverride(MemberOrder|OrderItem $trackable, ?User $changedBy = null, ?string $note = null): Model
    {
        return DB::transaction(function () use ($trackable, $changedBy, $note) {
            $oldStatusId = $trackable->override_status_id;

            $trackable->forceFill(['override_status_id' => null])->save();
            $trackable->loadMissing($trackable instanceof MemberOrder ? ['batch.currentStatus'] : ['order.batch.currentStatus', 'order.overrideStatus']);

            $newEffectiveStatus = $trackable->effective_status;

            if ($oldStatusId && $newEffectiveStatus) {
                $trackable->statusHistories()->create([
                    'old_status_id' => $oldStatusId,
                    'new_status_id' => $newEffectiveStatus->id,
                    'note' => $note ?: 'Override status dihapus.',
                    'changed_by' => $changedBy?->id,
                ]);
            }

            return $trackable->refresh();
        });
    }

    private function statusFieldFor(Model $trackable): string
    {
        return match (true) {
            $trackable instanceof Batch => 'current_status_id',
            $trackable instanceof MemberOrder, $trackable instanceof OrderItem => 'override_status_id',
            default => throw ValidationException::withMessages(['trackable' => 'Objek status tidak didukung.']),
        };
    }

    private function scopeFor(Model $trackable): string
    {
        return match (true) {
            $trackable instanceof Batch => 'batch',
            $trackable instanceof MemberOrder => 'member_order',
            $trackable instanceof OrderItem => 'order_item',
            default => throw ValidationException::withMessages(['trackable' => 'Objek status tidak didukung.']),
        };
    }
}
