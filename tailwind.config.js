/** Kompilasi statis Tailwind (menggantikan Play CDN yang compile ulang tiap load). */
module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './app/**/*.php',
  ],
  safelist: [
    // Kelas yang dirakit dinamis di PHP/Blade (mis. "bg-{{ $c }}-500")
    {
      pattern: /^(bg|text|border|from|to|ring)-(red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose|gray|slate)-(50|100|200|300|400|500|600|700|800|900)$/,
      variants: ['hover', 'focus'],
    },
    { pattern: /^(bg|text|border)-(red|orange|amber|green|emerald|teal|sky|blue|violet|pink|rose|gray)-(50|100)\/(40|50|60|70|80)$/ },
    { pattern: /^grid-cols-(1|2|3|4|5|6|7|8|9|10)$/ },
  ],
  theme: { extend: {} },
  plugins: [],
};
