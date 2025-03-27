import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import vuePlugin from "@vitejs/plugin-vue";

export default defineConfig({
  plugins: [symfonyPlugin(), vuePlugin()],
  build: {
    rollupOptions: {
      input: {
        app: "./assets/app.ts",
      },
    },
  },
});
