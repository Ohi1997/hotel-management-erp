<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">System Configuration</h1>
            <p class="text-sm text-slate-500">Maintain hotel profile, financial preferences, operational rules, and security settings.</p>
        </div>
        <div class="flex items-center gap-3">
            <label class="text-xs font-semibold uppercase text-slate-500">Active Hotel</label>
            <select
                wire:model.live="hotelId"
                class="rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
            >
                @foreach ($hotels as $hotelId => $hotelName)
                    <option value="{{ $hotelId }}">{{ $hotelName }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="space-y-6">
        <section class="rounded-lg bg-white shadow-sm ring-1 ring-slate-200/70">
            <form wire:submit.prevent="saveHotelProfile" class="space-y-4">
                <header class="border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-800">Hotel Profile</h2>
                    <p class="text-xs text-slate-500">Set the identity and contact details for this hotel.</p>
                </header>

                <div class="px-6 pb-6 pt-2 space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <x-admin-settings.input label="Hotel Name" model="hotelForm.name" required />
                        <x-admin-settings.input label="Code" model="hotelForm.code" required />
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <x-admin-settings.input label="Tax ID" model="hotelForm.tax_id" />
                        <x-admin-settings.input label="Timezone" model="hotelForm.timezone" />
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <x-admin-settings.input label="Currency" model="hotelForm.currency" />
                        <x-admin-settings.input label="Language" model="hotelForm.language" />
                        <x-admin-settings.input label="Website" model="hotelForm.website" type="url" />
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <x-admin-settings.input label="Phone" model="hotelForm.phone" />
                        <x-admin-settings.input label="Email" model="hotelForm.email" type="email" />
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <x-admin-settings.input label="Address Line 1" model="hotelForm.address_line1" />
                        <x-admin-settings.input label="Address Line 2" model="hotelForm.address_line2" />
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <x-admin-settings.input label="City" model="hotelForm.city" />
                        <x-admin-settings.input label="State" model="hotelForm.state" />
                        <x-admin-settings.input label="Postal Code" model="hotelForm.postal_code" />
                        <x-admin-settings.input label="Country" model="hotelForm.country" />
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <x-admin-settings.input label="Default Check-in" model="hotelForm.default_check_in" type="time" />
                        <x-admin-settings.input label="Default Check-out" model="hotelForm.default_check_out" type="time" />
                        <x-admin-settings.input label="Max Guests" model="hotelForm.default_max_guests" type="number" min="1" max="10" />
                        <x-admin-settings.input label="Default Deposit" model="hotelForm.default_deposit" type="number" min="0" step="0.01" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500">Cancellation Policy</label>
                        <textarea
                            rows="3"
                            wire:model.live="hotelForm.cancellation_policy"
                            class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        ></textarea>
                        @error('hotelForm.cancellation_policy')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-wrap gap-6">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" wire:model="hotelForm.is_primary" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Primary hotel
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" wire:model="hotelForm.is_active" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Active
                        </label>
                    </div>
                </div>

                <footer class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                    <button type="submit" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Save Hotel Profile
                    </button>
                </footer>
            </form>
        </section>

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-admin-settings.card title="Booking Settings" description="Control booking behaviour and occupancy rules.">
                <form wire:submit.prevent="saveBooking" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <x-admin-settings.input label="Check-in" model="booking.check_in_time" type="time" />
                        <x-admin-settings.input label="Check-out" model="booking.check_out_time" type="time" />
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <x-admin-settings.input label="Max Guests" model="booking.max_guests" type="number" min="1" max="10" />
                        <x-admin-settings.input label="Default Deposit" model="booking.default_deposit" type="number" min="0" step="0.01" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500">Cancellation Policy</label>
                        <textarea
                            rows="3"
                            wire:model.live="booking.cancellation_policy"
                            class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        ></textarea>
                        @error('booking.cancellation_policy')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save Booking Settings
                        </button>
                    </div>
                </form>
            </x-admin-settings.card>

            <x-admin-settings.card title="Invoice Settings" description="Configure invoice numbering, taxes, and footer copy.">
                <form wire:submit.prevent="saveInvoice" class="space-y-4">
                    <x-admin-settings.input label="Invoice Prefix" model="invoice.prefix" />
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <x-admin-settings.input label="Tax %" model="invoice.tax_percentage" type="number" min="0" max="100" step="0.01" />
                        <x-admin-settings.input label="Service Charge %" model="invoice.service_charge" type="number" min="0" max="100" step="0.01" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500">Invoice Footer</label>
                        <textarea
                            rows="3"
                            wire:model.live="invoice.footer_text"
                            class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        ></textarea>
                        @error('invoice.footer_text')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save Invoice Settings
                        </button>
                    </div>
                </form>
            </x-admin-settings.card>
        </section>

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-admin-settings.card title="Notification Templates" description="Customise the email/SMS templates sent to guests.">
                <form wire:submit.prevent="saveNotifications" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500">Booking Confirmation</label>
                        <textarea rows="3" wire:model.live="notifications.booking_confirmation" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"></textarea>
                        @error('notifications.booking_confirmation')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500">Cancellation</label>
                        <textarea rows="3" wire:model.live="notifications.cancellation" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"></textarea>
                        @error('notifications.cancellation')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500">Check-out</label>
                        <textarea rows="3" wire:model.live="notifications.check_out" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"></textarea>
                        @error('notifications.check_out')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save Notification Templates
                        </button>
                    </div>
                </form>
            </x-admin-settings.card>

            <x-admin-settings.card title="Financial Settings" description="Define tax rates, accepted payments, and gateway credentials.">
                <form wire:submit.prevent="saveFinancial" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <x-admin-settings.input label="VAT %" model="financial.vat_rate" type="number" min="0" max="100" step="0.01" />
                        <x-admin-settings.input label="Service Charge %" model="financial.service_charge_rate" type="number" min="0" max="100" step="0.01" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500">Payment Modes</label>
                        <input
                            type="text"
                            wire:model.live="financial.payment_modes"
                            placeholder="Comma separated (e.g. cash, card, bank transfer)"
                            class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        >
                        @error('financial.payment_modes')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <x-admin-settings.input label="Gateway Provider" model="financial.gateway_provider" />
                        <x-admin-settings.input label="Merchant ID" model="financial.gateway_merchant_id" />
                        <x-admin-settings.input label="API Key" model="financial.gateway_api_key" />
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save Financial Settings
                        </button>
                    </div>
                </form>
            </x-admin-settings.card>
        </section>

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-admin-settings.card title="Operational Rules" description="Control housekeeping and maintenance procedures.">
                <form wire:submit.prevent="saveOperational" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <x-admin-settings.input label="Housekeeping Start" model="operational.housekeeping_start" type="time" />
                        <x-admin-settings.input label="Housekeeping End" model="operational.housekeeping_end" type="time" />
                    </div>
                    <x-admin-settings.input label="Maintenance Window" model="operational.maintenance_window" />
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-500">Housekeeping Notes</label>
                        <textarea rows="3" wire:model.live="operational.housekeeping_notes" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"></textarea>
                        @error('operational.housekeeping_notes')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save Operational Rules
                        </button>
                    </div>
                </form>
            </x-admin-settings.card>

            <x-admin-settings.card title="Access Control" description="Toggle optional modules and UI permissions.">
                <form wire:submit.prevent="saveAccess" class="space-y-4">
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <input type="checkbox" wire:model="access.feature_wake_up_calls" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Enable wake-up calls module
                        </label>
                        <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <input type="checkbox" wire:model="access.feature_loyalty_program" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Enable loyalty system
                        </label>
                        <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <input type="checkbox" wire:model="access.feature_maintenance" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Enable maintenance module
                        </label>
                        <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <input type="checkbox" wire:model="access.role_permissions_ui" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Allow role permission UI access
                        </label>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save Access Control
                        </button>
                    </div>
                </form>
            </x-admin-settings.card>
        </section>

        <section>
            <x-admin-settings.card title="Audit &amp; Security" description="Manage audit retention, backups, and security automation.">
                <form wire:submit.prevent="saveAudit" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <x-admin-settings.input label="Retain Logs (days)" model="audit.retain_logs_for_days" type="number" min="7" max="365" />
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-500">Backup Frequency</label>
                            <select
                                wire:model.live="audit.backup_frequency"
                                class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                            >
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                            @error('audit.backup_frequency')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                                <input type="checkbox" wire:model="audit.backup_enabled" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                Backups enabled
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save Audit Settings
                        </button>
                    </div>
                </form>
            </x-admin-settings.card>
        </section>
    </div>
</div>
