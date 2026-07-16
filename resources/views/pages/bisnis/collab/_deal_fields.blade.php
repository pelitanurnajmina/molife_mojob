{{-- Field proposal untuk workspace kolaborasi (proyek sudah fix, tanpa select proyek) --}}
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
            {{-- Form tambah: default hari ini (tetap bisa diganti mundur); form edit diisi via JS. --}}
            <div class="relative">
                <input type="date" name="proposal_date" max="{{ date('Y-m-d') }}" value="{{ !empty($edit) ? '' : date('Y-m-d') }}"
                    class="w-full pl-3 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
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
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Awal Komunikasi') }}</label>
            <select name="channel" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                <option value="">{{ __('— Pilih channel —') }}</option>
                @foreach(\App\Services\BusinessService::CHANNELS as $ck => $cl)
                <option value="{{ $ck }}">{{ $cl }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Alamat Klien') }}</label>
        <input type="text" name="address" maxlength="255"
            placeholder="{{ __('cth: Jakarta') }}"
            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
    </div>

    <div class="mb-4">
        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Respon Klien / Catatan') }}</label>
        <textarea name="notes" rows="3"
            placeholder="{{ __('cth: Tertarik, minta revisi harga...') }}"
            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm leading-relaxed outline-none focus:border-black focus:bg-white resize-none transition-all"></textarea>
    </div>
</div>
