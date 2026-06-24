{{-- Shared add/edit fields for an important link --}}
<div>
    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Link Apa') }} *</label>
    <input type="text" name="title" maxlength="255" required placeholder="{{ __('cth: Dashboard Analytics, Drive Kerja...') }}"
        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
</div>
<div>
    <label class="block text-xs font-bold text-gray-500 mb-1.5">URL *</label>
    <input type="url" name="url" maxlength="1000" required placeholder="https://..."
        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
</div>
<div>
    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Fungsi') }}</label>
    <input type="text" name="notes" maxlength="255" placeholder="{{ __('cth: Untuk cek laporan harian') }}"
        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
</div>
