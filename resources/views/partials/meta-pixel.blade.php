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
<script>window.mlFbTrack(@json($mlFbEvent), {}, @json('srv_' . $mlFbEvent . '_' . substr(md5(session()->getId()), 0, 12)));</script>
@endif
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=979836518073745&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
