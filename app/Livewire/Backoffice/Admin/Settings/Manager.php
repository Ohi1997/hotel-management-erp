<?php

namespace App\Livewire\Backoffice\Admin\Settings;

use App\Models\Hotel;
use App\Models\HotelSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Manager extends Component
{
    public ?int $hotelId = null;

    /**
     * @var array<string, mixed>
     */
    public array $hotelForm = [];

    /** @var array<string, mixed> */
    public array $booking = [];

    /** @var array<string, mixed> */
    public array $invoice = [];

    /** @var array<string, mixed> */
    public array $notifications = [];

    /** @var array<string, mixed> */
    public array $financial = [];

    /** @var array<string, mixed> */
    public array $operational = [];

    /** @var array<string, mixed> */
    public array $access = [];

    /** @var array<string, mixed> */
    public array $audit = [];

    public array $hotels = [];

    public function mount(): void
    {
        $this->hotels = Hotel::query()->orderBy('name')->pluck('name', 'id')->all();
        $this->hotelId = auth()->user()?->hotel_id ?? array_key_first($this->hotels);

        if ($this->hotelId) {
            $this->hydrateSettings();
        }
    }

    public function updatedHotelId(?int $hotelId): void
    {
        if (! $hotelId) {
            $this->resetForms();
            return;
        }

        $this->hydrateSettings();
    }

    public function saveHotelProfile(): void
    {
        if (! $this->hotelId) {
            return;
        }

        $this->validate($this->hotelRules());

        $hotel = Hotel::findOrFail($this->hotelId);
        $hotel->fill($this->hotelForm);
        $hotel->save();

        $this->dispatch('toast', type: 'success', message: 'Hotel profile updated successfully.');
    }

    public function saveBooking(): void
    {
        $this->persistSetting('booking', $this->booking, $this->bookingRules());
    }

    public function saveInvoice(): void
    {
        $this->persistSetting('invoice', $this->invoice, $this->invoiceRules());
    }

    public function saveNotifications(): void
    {
        $this->persistSetting('notifications', $this->notifications, $this->notificationRules());
    }

    public function saveFinancial(): void
    {
        $this->persistSetting('financial', $this->financial, $this->financialRules());
    }

    public function saveOperational(): void
    {
        $this->persistSetting('operational', $this->operational, $this->operationalRules());
    }

    public function saveAccess(): void
    {
        $this->persistSetting('access', $this->access, $this->accessRules());
    }

    public function saveAudit(): void
    {
        $this->persistSetting('audit', $this->audit, $this->auditRules());
    }

    public function render()
    {
        return view('livewire.backoffice.admin.settings.manager', [
            'hotels' => $this->hotels,
        ])->layout('layouts.backoffice', ['pageTitle' => 'System Configuration']);
    }

    protected function persistSetting(string $group, array $values, array $rules): void
    {
        if (! $this->hotelId) {
            return;
        }

        $this->validate($rules);

        $normalized = $this->normalize($group, $values);

        DB::transaction(function () use ($group, $normalized): void {
            HotelSetting::updateOrCreate(
                ['hotel_id' => $this->hotelId, 'group' => $group],
                ['values' => $normalized]
            );
        });

        $this->dispatch('toast', type: 'success', message: 'Settings updated successfully.');
    }

    protected function hydrateSettings(): void
    {
        $hotel = Hotel::findOrFail($this->hotelId);
        $this->hotelForm = array_merge($this->hotelDefaults(), $hotel->only(array_keys($this->hotelDefaults())));

        $this->booking = $this->resolveSetting('booking', $this->bookingDefaults());
        $this->invoice = $this->resolveSetting('invoice', $this->invoiceDefaults());
        $this->notifications = $this->resolveSetting('notifications', $this->notificationDefaults());
        $this->financial = $this->resolveSetting('financial', $this->financialDefaults());
        $this->operational = $this->resolveSetting('operational', $this->operationalDefaults());
        $this->access = $this->resolveSetting('access', $this->accessDefaults());
        $this->audit = $this->resolveSetting('audit', $this->auditDefaults());
    }

    protected function resetForms(): void
    {
        $this->hotelForm = $this->hotelDefaults();
        $this->booking = $this->bookingDefaults();
        $this->invoice = $this->invoiceDefaults();
        $this->notifications = $this->notificationDefaults();
        $this->financial = $this->financialDefaults();
        $this->operational = $this->operationalDefaults();
        $this->access = $this->accessDefaults();
        $this->audit = $this->auditDefaults();
    }

    protected function resolveSetting(string $group, array $defaults): array
    {
        if (! $this->hotelId) {
            return $defaults;
        }

        $setting = HotelSetting::where('hotel_id', $this->hotelId)
            ->where('group', $group)
            ->first();

        if (! $setting) {
            return $defaults;
        }

        $values = is_array($setting->values) ? $setting->values : [];

        $merged = array_merge($defaults, $values);

        if ($group === 'financial') {
            $merged['payment_modes'] = implode(', ', Arr::wrap($merged['payment_modes']));
        }

        return $merged;
    }

    protected function normalize(string $group, array $values): array
    {
        return match ($group) {
            'booking' => [
                'check_in_time' => $values['check_in_time'],
                'check_out_time' => $values['check_out_time'],
                'max_guests' => (int) $values['max_guests'],
                'default_deposit' => (float) $values['default_deposit'],
                'cancellation_policy' => $values['cancellation_policy'],
            ],
            'invoice' => [
                'prefix' => $values['prefix'],
                'tax_percentage' => (float) $values['tax_percentage'],
                'service_charge' => (float) $values['service_charge'],
                'footer_text' => $values['footer_text'],
            ],
            'notifications' => [
                'booking_confirmation' => $values['booking_confirmation'],
                'cancellation' => $values['cancellation'],
                'check_out' => $values['check_out'],
            ],
            'financial' => [
                'vat_rate' => (float) $values['vat_rate'],
                'service_charge_rate' => (float) $values['service_charge_rate'],
                'payment_modes' => collect(explode(',', (string) $values['payment_modes']))
                    ->map(fn ($mode) => trim($mode))
                    ->filter()
                    ->values()
                    ->all(),
                'gateway_provider' => $values['gateway_provider'],
                'gateway_merchant_id' => $values['gateway_merchant_id'],
                'gateway_api_key' => $values['gateway_api_key'],
            ],
            'operational' => [
                'housekeeping_start' => $values['housekeeping_start'],
                'housekeeping_end' => $values['housekeeping_end'],
                'maintenance_window' => $values['maintenance_window'],
                'housekeeping_notes' => $values['housekeeping_notes'],
            ],
            'access' => [
                'feature_wake_up_calls' => (bool) $values['feature_wake_up_calls'],
                'feature_loyalty_program' => (bool) $values['feature_loyalty_program'],
                'feature_maintenance' => (bool) $values['feature_maintenance'],
                'role_permissions_ui' => (bool) $values['role_permissions_ui'],
            ],
            'audit' => [
                'retain_logs_for_days' => (int) $values['retain_logs_for_days'],
                'backup_enabled' => (bool) $values['backup_enabled'],
                'backup_frequency' => $values['backup_frequency'],
            ],
            default => $values,
        };
    }

    protected function hotelRules(): array
    {
        return [
            'hotelForm.name' => ['required', 'string', 'max:255'],
            'hotelForm.code' => ['required', 'string', 'max:20', Rule::unique('hotels', 'code')->ignore($this->hotelId)],
            'hotelForm.tax_id' => ['nullable', 'string', 'max:50'],
            'hotelForm.timezone' => ['required', 'string', 'max:60'],
            'hotelForm.currency' => ['required', 'string', 'size:3'],
            'hotelForm.language' => ['required', 'string', 'max:5'],
            'hotelForm.address_line1' => ['nullable', 'string', 'max:255'],
            'hotelForm.address_line2' => ['nullable', 'string', 'max:255'],
            'hotelForm.city' => ['nullable', 'string', 'max:100'],
            'hotelForm.state' => ['nullable', 'string', 'max:100'],
            'hotelForm.postal_code' => ['nullable', 'string', 'max:20'],
            'hotelForm.country' => ['nullable', 'string', 'size:2'],
            'hotelForm.phone' => ['nullable', 'string', 'max:50'],
            'hotelForm.email' => ['nullable', 'email', 'max:255'],
            'hotelForm.website' => ['nullable', 'url', 'max:255'],
            'hotelForm.logo_path' => ['nullable', 'string', 'max:255'],
            'hotelForm.default_check_in' => ['nullable', 'date_format:H:i'],
            'hotelForm.default_check_out' => ['nullable', 'date_format:H:i'],
            'hotelForm.default_max_guests' => ['required', 'integer', 'min:1', 'max:10'],
            'hotelForm.default_deposit' => ['required', 'numeric', 'min:0'],
            'hotelForm.cancellation_policy' => ['nullable', 'string'],
            'hotelForm.is_primary' => ['boolean'],
            'hotelForm.is_active' => ['boolean'],
        ];
    }

    protected function bookingRules(): array
    {
        return [
            'booking.check_in_time' => ['required', 'date_format:H:i'],
            'booking.check_out_time' => ['required', 'date_format:H:i'],
            'booking.max_guests' => ['required', 'integer', 'min:1', 'max:10'],
            'booking.default_deposit' => ['required', 'numeric', 'min:0'],
            'booking.cancellation_policy' => ['nullable', 'string'],
        ];
    }

    protected function bookingDefaults(): array
    {
        return [
            'check_in_time' => '15:00',
            'check_out_time' => '11:00',
            'max_guests' => 2,
            'default_deposit' => 0,
            'cancellation_policy' => '',
        ];
    }

    protected function invoiceRules(): array
    {
        return [
            'invoice.prefix' => ['required', 'string', 'max:50'],
            'invoice.tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'invoice.service_charge' => ['required', 'numeric', 'min:0', 'max:100'],
            'invoice.footer_text' => ['nullable', 'string'],
        ];
    }

    protected function invoiceDefaults(): array
    {
        return [
            'prefix' => 'INV-' . now()->year . '-',
            'tax_percentage' => 12,
            'service_charge' => 10,
            'footer_text' => 'Thank you for staying with us.',
        ];
    }

    protected function notificationRules(): array
    {
        return [
            'notifications.booking_confirmation' => ['required', 'string'],
            'notifications.cancellation' => ['required', 'string'],
            'notifications.check_out' => ['required', 'string'],
        ];
    }

    protected function notificationDefaults(): array
    {
        return [
            'booking_confirmation' => 'Dear {{ guest_name }}, your booking {{ booking_reference }} is confirmed.',
            'cancellation' => 'Your booking has been cancelled. We hope to host you again.',
            'check_out' => 'Thank you for staying with us. We hope to see you soon.',
        ];
    }

    protected function financialRules(): array
    {
        return [
            'financial.vat_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'financial.service_charge_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'financial.payment_modes' => ['nullable', 'string', 'max:255'],
            'financial.gateway_provider' => ['nullable', 'string', 'max:100'],
            'financial.gateway_merchant_id' => ['nullable', 'string', 'max:100'],
            'financial.gateway_api_key' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function financialDefaults(): array
    {
        return [
            'vat_rate' => 12,
            'service_charge_rate' => 10,
            'payment_modes' => 'cash, card',
            'gateway_provider' => '',
            'gateway_merchant_id' => '',
            'gateway_api_key' => '',
        ];
    }

    protected function operationalRules(): array
    {
        return [
            'operational.housekeeping_start' => ['required', 'date_format:H:i'],
            'operational.housekeeping_end' => ['required', 'date_format:H:i'],
            'operational.maintenance_window' => ['nullable', 'string', 'max:255'],
            'operational.housekeeping_notes' => ['nullable', 'string'],
        ];
    }

    protected function operationalDefaults(): array
    {
        return [
            'housekeeping_start' => '08:00',
            'housekeeping_end' => '16:00',
            'maintenance_window' => 'Sunday 10:00-14:00',
            'housekeeping_notes' => '',
        ];
    }

    protected function accessRules(): array
    {
        return [
            'access.feature_wake_up_calls' => ['boolean'],
            'access.feature_loyalty_program' => ['boolean'],
            'access.feature_maintenance' => ['boolean'],
            'access.role_permissions_ui' => ['boolean'],
        ];
    }

    protected function accessDefaults(): array
    {
        return [
            'feature_wake_up_calls' => true,
            'feature_loyalty_program' => false,
            'feature_maintenance' => true,
            'role_permissions_ui' => true,
        ];
    }

    protected function auditRules(): array
    {
        return [
            'audit.retain_logs_for_days' => ['required', 'integer', 'min:7', 'max:365'],
            'audit.backup_enabled' => ['boolean'],
            'audit.backup_frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly'])],
        ];
    }

    protected function auditDefaults(): array
    {
        return [
            'retain_logs_for_days' => 90,
            'backup_enabled' => true,
            'backup_frequency' => 'daily',
        ];
    }

    protected function hotelDefaults(): array
    {
        return [
            'name' => '',
            'code' => '',
            'tax_id' => '',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'language' => 'en',
            'address_line1' => '',
            'address_line2' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country' => '',
            'phone' => '',
            'email' => '',
            'website' => '',
            'logo_path' => '',
            'default_check_in' => '15:00',
            'default_check_out' => '11:00',
            'default_max_guests' => 2,
            'default_deposit' => 0,
            'cancellation_policy' => '',
            'is_primary' => false,
            'is_active' => true,
        ];
    }
}
