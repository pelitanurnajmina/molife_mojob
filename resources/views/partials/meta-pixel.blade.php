{{--
    Meta Pixel — satu sumber untuk seluruh halaman publik (landing, blog,
    dan alur daftar/masuk/langganan). Cukup @include partial ini di <head>.

    Dataset ID: 979836518073745

    CATATAN: sengaja TIDAK dipasang di layout aplikasi (layouts/app) dan
    panel admin. Halaman di dalam aplikasi memakai URL sensitif seperti
    /sholat, /haid, /intimasi, /mental dan /journal. Mengirimkan URL itu ke
    Meta berarti mengirim data keyakinan agama, kesehatan, dan perilaku
    seksual pengguna, yang dilarang Ketentuan Meta Business Tools sekaligus
    bertentangan dengan janji di Kebijakan Privasi Molife.
--}}
@php
    /**
     * Pengaman URL.
     *
     * Meta Pixel SELALU mengirim URL halaman lengkap (parameter dl) beserta
     * referrer (rl) pada setiap event. Jadi memasang pixel di halaman yang
     * URL-nya memuat token atau email sama saja dengan mengirimkan rahasia
     * itu ke Meta.
     *
     * Partial ini menolak memuat pixel bila jalur atau query terlihat
     * membawa data rahasia, supaya halaman baru yang dibuat kemudian tidak
     * bisa membocorkannya tanpa sengaja. Kode referral (?ref=) sengaja
     * TIDAK diblokir karena bukan data pribadi.
     */
    $mlPath = '/' . trim(request()->path(), '/') . '/';

    /**
     * Pengaman lingkungan.
     *
     * Pixel memakai ID dataset yang sama di mana pun ia dimuat, jadi membuka
     * halaman di XAMPP saat mengembangkan fitur akan terkirim ke Meta sebagai
     * kunjungan sungguhan. Event palsu itu ikut dipakai Meta untuk belajar
     * mengenali calon pelanggan, padahal tidak ada orang di baliknya.
     *
     * Sengaja memeriksa NAMA HOST, bukan APP_ENV. Kalau suatu saat APP_ENV di
     * server salah terisi, pemeriksaan APP_ENV akan mematikan pixel di situs
     * sungguhan tanpa ada tanda apa pun, dan iklan berjalan tanpa data. Dengan
     * daftar host pengembangan, kesalahan paling buruk hanya membuat pixel
     * tetap menyala di tempat yang memang seharusnya menyala.
     */
    $mlHost    = strtolower(request()->getHost());
    $mlIsLocal = in_array($mlHost, ['localhost', '127.0.0.1', '::1', '0.0.0.0'], true)
        || str_ends_with($mlHost, '.localhost')
        || str_ends_with($mlHost, '.test')
        || str_ends_with($mlHost, '.local');

    $mlPixelAllowed = ! $mlIsLocal
        && ! preg_match('#/(reset-password|forgot-password/[^/]|verify|verifikasi|terima|invite|undangan)/#i', $mlPath)
        && ! request()->hasAny(['token', 'email', 'password', 'signature', 'api_token', 'remember_token']);
@endphp
@if($mlPixelAllowed)
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '979836518073745');
fbq('track', 'PageView');

/* ──────────────────────────────────────────────────────────────
   Helper event funnel.

   - Tidak pernah mengirim data pribadi: hanya nilai transaksi & mata uang.
     Advanced matching (email/telepon) sengaja TIDAK diaktifkan.
   - Anti-duplikasi: tiap event penting dikirim sekali saja per kunci
     (mis. per nomor pesanan), ditandai di localStorage. Jadi reload,
     tombol diklik dua kali, atau pindah halaman tidak menggandakan event.
   - Kunci yang sama juga dikirim sebagai eventID, supaya kalau nanti
     dipasang Conversions API dari server, Meta bisa menyatukan keduanya.
   ────────────────────────────────────────────────────────────── */
window.mlFbTrack = function (name, params, dedupKey) {
    if (typeof fbq !== 'function') return false;
    if (dedupKey) {
        try {
            if (localStorage.getItem('mlfb:' + dedupKey)) return false;
            localStorage.setItem('mlfb:' + dedupKey, '1');
        } catch (e) { /* localStorage diblokir: biarkan event tetap terkirim */ }
    }
    fbq('track', name, params || {}, dedupKey ? { eventID: dedupKey } : undefined);
    return true;
};
</script>

{{-- Event yang dipicu dari server (mis. registrasi berhasil).
     Dipakai `pull` supaya terambil sekaligus terhapus: dikirim tepat sekali,
     dan tetap selamat walau sempat melewati beberapa redirect. --}}
@php($mlFbEvent = session()->pull('fb_event'))
@if($mlFbEvent)
<script>window.mlFbTrack && window.mlFbTrack(@json($mlFbEvent), {}, @json('srv_' . $mlFbEvent . '_' . substr(md5(session()->getId()), 0, 12)));</script>
@endif
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=979836518073745&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
@endif
