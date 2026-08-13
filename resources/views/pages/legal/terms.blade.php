@extends('blog.layout')

@section('seo_title', 'Syarat & Ketentuan')
@section('seo_description', 'Syarat & Ketentuan penggunaan layanan Molife.')
@section('canonical', route('terms'))

@section('content')
<div class="post-wrap" style="padding-top:56px;padding-bottom:72px">
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('landing') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:700;color:#6b7280;text-decoration:none;margin-bottom:22px">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <h1 style="font-size:2rem;font-weight:800;letter-spacing:-.02em;color:#111827;margin:0 0 6px">Syarat &amp; Ketentuan</h1>
    <p style="font-size:13px;color:#9ca3af;margin:0 0 32px">Terakhir diperbarui: Agustus 2026</p>

    <div class="prose">
        <p class="lead">Dengan membuat akun dan menggunakan Molife, kamu setuju dengan Syarat &amp; Ketentuan berikut. Mohon dibaca dengan saksama.</p>

        <h2>1. Tentang Molife</h2>
        <p>Molife adalah aplikasi pencatatan dan perencanaan hidup , mencakup ibadah, olahraga, kesehatan mental, tugas, karier, bisnis, dan keuangan , yang dirangkum menjadi satu "Life Score". Molife adalah alat bantu pribadi, bukan pengganti nasihat profesional.</p>

        <h2>2. Akun</h2>
        <ul>
            <li>Kamu bertanggung jawab menjaga kerahasiaan kata sandi dan seluruh aktivitas pada akunmu.</li>
            <li>Data yang kamu masukkan harus benar dan menjadi tanggung jawabmu.</li>
            <li>Satu orang cukup satu akun. Jangan gunakan akun orang lain tanpa izin.</li>
            <li>Kamu bisa menghapus akunmu kapan saja; data terkait akan dihapus sesuai Kebijakan Privasi.</li>
        </ul>

        <h2>3. Langganan &amp; Pembayaran</h2>
        <ul>
            <li>Sebagian fitur memerlukan langganan berbayar. Kamu membayar <strong>satu kali untuk durasi yang dipilih</strong> (mis. 3 bulan, 6 bulan, 1 tahun).</li>
            <li>Pembayaran diproses melalui penyedia pembayaran pihak ketiga (Midtrans/QRIS). Akses aktif otomatis setelah pembayaran terkonfirmasi.</li>
            <li><strong>Tidak ada perpanjangan otomatis.</strong> Akses berakhir saat durasi habis, kecuali kamu memperpanjang sendiri.</li>
            <li>Karena akses digital diberikan langsung setelah pembayaran, pembayaran yang sudah masuk pada dasarnya tidak dapat dikembalikan, kecuali diwajibkan oleh hukum yang berlaku atau atas kebijakan kami untuk kasus tertentu (mis. gagal aktivasi karena kesalahan sistem).</li>
        </ul>

        <h2>4. Program Referral</h2>
        <ul>
            <li>Kamu bisa mengajak orang lain memakai kode/link referral. Temanmu mendapat diskon untuk pembayaran pertamanya, dan kamu mendapat komisi dari pembayaran pertama itu.</li>
            <li>Komisi dapat dicairkan setelah mencapai ambang minimum yang berlaku, melalui metode yang tersedia.</li>
            <li>Kami berhak membatalkan komisi/akun yang terindikasi melakukan kecurangan (mis. akun palsu, self-referral, atau manipulasi).</li>
        </ul>

        <h2>5. Penggunaan yang Dilarang</h2>
        <p>Kamu setuju untuk tidak menyalahgunakan layanan, termasuk namun tidak terbatas pada: meretas atau mengganggu sistem, mengakses akun orang lain tanpa izin, menyebarkan konten melanggar hukum, atau menggunakan Molife untuk aktivitas ilegal.</p>

        <h2>6. Kontenmu</h2>
        <p>Semua catatan yang kamu masukkan (jurnal, ide, script, data keuangan, dan lainnya) tetap menjadi milikmu. Kami hanya memproses data tersebut untuk menjalankan fitur aplikasi untukmu, sesuai Kebijakan Privasi.</p>

        <h2>7. Batasan Tanggung Jawab</h2>
        <p>Molife disediakan "sebagaimana adanya". Fitur seperti prediksi siklus, Life Score, insight, atau ringkasan keuangan bersifat perkiraan dan <strong>bukan nasihat medis, keuangan, hukum, atau keagamaan profesional</strong>. Untuk keputusan penting, konsultasikan dengan ahli yang berwenang. Sejauh diizinkan hukum, kami tidak bertanggung jawab atas kerugian yang timbul dari penggunaan aplikasi.</p>

        <h2>8. Perubahan</h2>
        <p>Kami dapat memperbarui fitur maupun Syarat &amp; Ketentuan ini dari waktu ke waktu. Perubahan penting akan kami informasikan melalui aplikasi. Dengan terus menggunakan Molife setelah perubahan, kamu dianggap menyetujui versi terbaru.</p>

        <h2>9. Kontak</h2>
        <p>Ada pertanyaan tentang Syarat &amp; Ketentuan ini? Hubungi kami di <a href="mailto:support@molife.space">support@molife.space</a>.</p>

        <p style="margin-top:2rem"><a href="{{ route('privacy') }}">Baca juga: Kebijakan Privasi →</a></p>
    </div>
</div>
@endsection
