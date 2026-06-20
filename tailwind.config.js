/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.jsx',
    './app/Models/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#0D530E',
          deep: '#083A09',
          soft: '#DDF5DF',
        },
        accent: {
          DEFAULT: '#116B13',
          deep: '#0D530E',
        },
        canvas: {
          DEFAULT: '#ffffff',
          soft: '#f9fafb',
          'soft-2': '#f3f4f6',
        },
        hairline: {
          DEFAULT: '#e5e7eb',
          strong: '#9ca3af',
        },
        ink: '#111827',
        body: '#374151',
        mute: '#6b7280',
        success: {
          DEFAULT: '#0D530E',
          soft: '#DDF5DF',
        },
        warning: {
          DEFAULT: '#d97706',
          soft: '#fef3c7',
        },
        error: {
          DEFAULT: '#dc2626',
          soft: '#fee2e2',
        },
        info: {
          DEFAULT: '#2563eb',
          soft: '#dbeafe',
        },
      },
      fontFamily: {
        jakarta: ['"Plus Jakarta Sans"', 'sans-serif'],
        delight: ['"Outfit"', 'sans-serif'],
      },
      borderRadius: {
        'pill-sm': '64px',
        'pill': '100px',
      },
      boxShadow: {
        'card-inset': 'inset 0 0 0 1px rgba(0,0,0,0.08)',
        'card-sm': '0px 1px 1px rgba(0,0,0,0.02), 0px 2px 2px rgba(0,0,0,0.04), inset 0 0 0 1px rgba(0,0,0,0.08)',
        'card-md': '0px 2px 2px rgba(0,0,0,0.04), 0px 8px 8px -8px rgba(0,0,0,0.04), inset 0 0 0 1px rgba(0,0,0,0.08)',
        'card-lg': '0px 2px 2px rgba(0,0,0,0.04), 0px 8px 16px -4px rgba(0,0,0,0.04), inset 0 0 0 1px rgba(0,0,0,0.08)',
        'modal': '0px 1px 1px rgba(0,0,0,0.02), 0px 8px 16px -4px rgba(0,0,0,0.04), 0px 24px 32px -8px rgba(0,0,0,0.06), inset 0 0 0 1px rgba(0,0,0,0.08)',
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
      },
    },
  },
  plugins: [],
}
