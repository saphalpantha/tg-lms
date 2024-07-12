/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.php',
      './js/**/*.js',
      './**/*.php',
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  theme: {
    extend: {},
  },
  plugins: [require('daisyui')],
}