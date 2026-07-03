{{-- Field proposal untuk workspace kolaborasi (produk sudah fix, tanpa select produk) --}}
<div class="space-y-3">
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Nama Klien / Perusahaan') }} <span class="text-red-400">*</span></label>
        <input type="text" name="client_name" maxlength="255" required
            placeholder="{{ __('cth: PT Maju Jaya') }}"
            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Bidang Klien') }}</label>
            <input type="text" name="industry" maxlength="120"
                placeholder="{{ __('cth: Retail, F&B, Tech') }}"
                class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Status') }}</label>
            <select name="status" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                @foreach($statuses as $key => $meta)
                <option value="{{ $key }}">{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Nilai Proposal (Rp)') }}</label>
            <input type="number" name="value" min="0" placeholder="0"
                class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Tanggal Proposal') }}</label>
            <input type="date" name="proposal_date" max="{{ date('Y-m-d') }}"
                class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Narahubung') }}</label>
            <input type="text" name="contact" maxlength="255"
                placeholder="{{ __('Nama / telp / email') }}"
                class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Alamat Klien') }}</label>
            <input type="text" name="address" maxlength="255"
                placeholder="{{ __('cth: Jakarta') }}"
                class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
        </div>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Respon Klien / Catatan') }}</label>
        <textarea name="notes" rows="3"
            placeholder="{{ __('cth: Tertarik, minta revisi harga...') }}"
            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm leading-relaxed outline-none focus:border-black focus:bg-white resize-none transition-all"></textarea>
    </div>
</div>
