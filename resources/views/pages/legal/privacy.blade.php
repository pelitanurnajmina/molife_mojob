@extends('blog.layout')

@section('seo_title', 'Kebijakan Privasi')
@section('seo_description', 'Bagaimana Molife mengumpulkan, memakai, dan melindungi datamu.')
@section('canonical', route('privacy'))

@section('content')
<div class="post-wrap" style="padding-top:56px;padding-bottom:72px">
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('landing') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:700;color:#6b7280;text-decoration:none;margin-bottom:22px">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <h1 style="font-size:2rem;font-weight:800;letter-spacing:-.02em;color:#111827;margin:0 0 6px">Kebijakan Privasi</h1>
    <p style="font-size:13px;color:#9ca3af;margin:0 0 32px">Terakhir diperbarui: Agustus 2026</p>

    <div class="prose">
        <p class="lead">Privasimu penting. Halaman ini menjelaskan data apa yang kami kumpulkan, untuk apa, dan bagaimana kami menjaganya. Prinsip kami sederhana: datamu milikmu, kami hanya memakainya untuk menjalankan aplikasi ini untukmu.</p>

        <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:14px;padding:16px 18px;margin:0 0 1.7rem">
            <p style="margin:0 0 .35rem;font-weight:800;color:#166534;display:flex;align-items:center;gap:8px">
                <svg width="18" height="18" fill="none" stroke="#16A34A" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Catatan pribadimu tidak kami baca
            </p>
            <p style="margin:0;color:#15803d;font-size:.97rem;line-height:1.7">Semua yang kamu catat setiap hari , sholat, mood, jurnal, keuangan, tugas, ide/script, dan lainnya , bersifat pribadi. <strong>Tim Molife tidak membaca, memantau, atau memakai isi catatanmu.</strong> Data itu hanya diproses otomatis oleh sistem untuk menampilkan fitur kepadamu (misalnya menghitung Life Score dan statistik), bukan untuk dilihat oleh orang di tim kami.</p>
        </div>

        <h2>1. Data yang Kami Kumpulkan</h2>
        <ul>
            <li><strong>Data akun:</strong> email, username, dan kata sandi (disimpan dalam bentuk terenkripsi/hash , kami tidak pernah bisa melihat kata sandi aslimu).</li>
            <li><strong>Data profil:</strong> nama tampilan, jenis kelamin, dan preferensi fitur yang kamu atur.</li>
            <li><strong>Data aktivitas yang kamu masukkan sendiri:</strong> catatan sholat, olahraga, mood, tugas, jurnal, ide/script, target, transaksi keuangan, dan sejenisnya. Ini kamu isi secara sukarela untuk fitur pencatatan.</li>
            <li><strong>Data pembayaran:</strong> saat berlangganan, pembayaran diproses oleh penyedia pihak ketiga (Midtrans). <strong>Kami tidak menyimpan nomor kartu atau detail pembayaran sensitifmu</strong> , itu ditangani langsung oleh penyedia pembayaran.</li>
        </ul>

        <h2>2. Cara Kami Memakai Data</h2>
        <ul>
            <li>Menyediakan dan menjalankan fitur aplikasi (mencatat, menampilkan, menghitung Life Score, pengingat, statistik).</li>
            <li>Mengelola akun, langganan, dan program referral.</li>
            <li>Meningkatkan layanan dan memperbaiki masalah teknis.</li>
        </ul>
        <p>Kami <strong>tidak menjual datamu</strong>, dan <strong>tidak membagikannya ke pihak ketiga untuk iklan</strong>.</p>

        <h2>3. Catatan Pribadimu Tidak Kami Baca</h2>
        <p>Isi catatan harianmu (sholat, mood, jurnal, keuangan, tugas, ide/script, dan sejenisnya) diperlakukan sebagai data pribadi. Tim Molife <strong>tidak membaca, memantau, atau menganalisis isi catatanmu secara manual</strong>. Yang terjadi hanyalah pemrosesan otomatis oleh sistem untuk menjalankan fitur , misalnya menghitung Life Score, statistik, dan pengingat , dan hasilnya hanya ditampilkan kepadamu.</p>
        <p>Kami jujur soal ini: agar fitur seperti Life Score bisa berjalan, data disimpan di database kami (bukan terenkripsi end-to-end). Secara teknis, akses ke database hanya dibuka <strong>seperlunya untuk pemeliharaan teknis</strong> (misalnya memperbaiki bug atau menjaga server), oleh personel terbatas, dan <strong>tidak untuk melihat-lihat isi catatan penggunamu</strong>. Kami tidak akan pernah menjual, membagikan, atau menggunakan catatan pribadimu untuk tujuan lain.</p>

        <h2>4. Berbagi Data dengan Pihak Ketiga</h2>
        <p>Kami hanya membagikan data seperlunya kepada penyedia layanan yang membuat aplikasi ini berjalan:</p>
        <ul>
            <li><strong>Penyedia pembayaran (Midtrans):</strong> untuk memproses transaksi langganan.</li>
            <li><strong>Login Google (opsional):</strong> jika kamu memilih masuk dengan Google, kami hanya menerima info dasar akun (nama, email) untuk membuat/menautkan akunmu.</li>
        </ul>
        <p>Kami juga dapat mengungkap data bila diwajibkan oleh hukum yang berlaku.</p>

        <h2>5. Penyimpanan &amp; Keamanan</h2>
        <p>Data disimpan di server kami dengan langkah pengamanan yang wajar (kata sandi di-hash, akses dibatasi). Tidak ada sistem yang 100% aman, tetapi kami berupaya menjaga datamu sebaik mungkin.</p>

        <h2>6. Hak Kamu atas Data</h2>
        <ul>
            <li>Melihat dan mengubah data profil serta catatanmu kapan saja lewat aplikasi.</li>
            <li>Menghapus catatan tertentu, atau menghapus seluruh akun beserta datanya.</li>
            <li>Meminta bantuan terkait datamu melalui email dukungan kami.</li>
        </ul>

        <h2>7. Cookie &amp; Sesi</h2>
        <p>Kami memakai cookie/sesi seperlunya agar kamu tetap masuk (login) dan aplikasi berfungsi. Kami tidak memakai cookie pelacak untuk iklan.</p>

        <h2>8. Data Anak</h2>
        <p>Molife ditujukan untuk pengguna dewasa/remaja yang cukup umur. Kami tidak dengan sengaja mengumpulkan data anak di bawah umur tanpa persetujuan wali.</p>

        <h2>9. Perubahan Kebijakan</h2>
        <p>Kebijakan ini dapat kami perbarui sewaktu-waktu. Perubahan penting akan diinformasikan melalui aplikasi.</p>

        <h2>10. Kontak</h2>
        <p>Punya pertanyaan atau permintaan terkait data pribadimu? Hubungi kami di <a href="mailto:support@molife.space">support@molife.space</a>.</p>

        <p style="margin-top:2rem"><a href="{{ route('terms') }}">Baca juga: Syarat &amp; Ketentuan →</a></p>
    </div>
</div>
@endsection
