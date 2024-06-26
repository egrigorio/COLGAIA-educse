/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{html,js,php}"],
  theme: {
    extend: {      
      fontFamily: {
        'cralika': ['cralika', 'system-ui']
      }
    }
    
  },
  plugins: [require("daisyui")],
  daisyui: {
    themes: [
      {
      "black": {
        ...require("daisyui/src/theming/themes")["black"],
        "accent": "#95A472",
      }
      },      
      "cupcake",
      "bumblebee",
      "emerald",
      "corporate",
      "synthwave",
      "retro",
      "cyberpunk",
      "valentine",
      "halloween",
      "garden",
      "forest",
      "aqua",
      "lofi",
      "pastel",
      "fantasy",
      "wireframe",
      /* "black", */
      "luxury",
      "dracula",
      "cmyk",
      "autumn",
      "business",
      "acid",
      "lemonade",
      "night",
      "coffee",
      "winter",
      "dim",
      "nord",
      "sunset",
      {
        
        mytheme: {
          "primary": "#bfdbfe",
          "secondary": "#3b82f6",
          "warning": "red",
          "accent": "orange",
          "success": "#f5cb5c",
          "error": "green",
          "neutral": "#ffffff",
          "base-100": "#ffffff",
          "base-200": "#fafafa",
          "base-300": "#e2e8f0",
          

          fontFamily: 'Poppins, system-ui',

          "--rounded-box": "1rem", // border radius rounded-box utility class, used in card and other large boxes
          "--rounded-btn": "0.5rem", // border radius rounded-btn utility class, used in buttons and similar element
          "--rounded-badge": "1.9rem", // border radius rounded-badge utility class, used in badges and similar
          "--animation-btn": "0.25s", // duration of animation when you click on button
          "--animation-input": "0.2s", // duration of animation for inputs like checkbox, toggle, radio, etc
          "--btn-focus-scale": "0.95", // scale transform of button when you focus on it
          "--border-btn": "1px", // border width of buttons
          "--tab-border": "1px", // border width of tabs
          "--tab-radius": "0.5rem", // border radius of tabs
        },
        nocas: {
          "primary": "#ffcbdb",
          "secondary": "white",
          "warning": "#FF5733",
          "success": "green",
          "accent": "#F865B0",
          "error": "#87ceeb",          
          "neutral": "#ffffff",
          "base-100": "#f9fafb",
          "base-200": "#fafafa",
          "base-300": "#e2e8f0",
          "base-400": "black",

          fontFamily: 'cralika, system-ui',

          "--rounded-box": "1rem", // border radius rounded-box utility class, used in card and other large boxes
          "--rounded-btn": "0.5rem", // border radius rounded-btn utility class, used in buttons and similar element
          "--rounded-badge": "1.9rem", // border radius rounded-badge utility class, used in badges and similar
          "--animation-btn": "0.25s", // duration of animation when you click on button
          "--animation-input": "0.2s", // duration of animation for inputs like checkbox, toggle, radio, etc
          "--btn-focus-scale": "0.95", // scale transform of button when you focus on it
          "--border-btn": "1px", // border width of buttons
          "--tab-border": "1px", // border width of tabs
          "--tab-radius": "0.5rem", // border radius of tabs
        },
        
        
      }
    ],

  }
}