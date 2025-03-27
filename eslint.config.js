import eslint from "@eslint/js";
import eslintConfigPrettier from "eslint-config-prettier";
import eslintPluginVue from "eslint-plugin-vue";
import globals from "globals";
import typescriptEslint from "typescript-eslint";

// https://eslint.vuejs.org/user-guide/#example-configuration-with-typescript-eslint-and-prettier
export default typescriptEslint.config(
  {
    ignores: ["**/*.d.ts"],
  },
  {
    extends: [
      eslint.configs.recommended,
      ...typescriptEslint.configs.recommended,
      // Includes "flat/essential" and "flat/strongly-recommended".
      ...eslintPluginVue.configs["flat/recommended"],
    ],
    files: ["**/*.{ts,vue}"],
    languageOptions: {
      ecmaVersion: "latest",
      sourceType: "module",
      globals: globals.browser,
      parserOptions: {
        parser: typescriptEslint.parser,
      },
    },
    rules: {
      "vue/block-order": [
        "error",
        {
          order: ["template", "script", "style"],
        },
      ],
    },
  },
  // Turns off all ESLint rules that are unnecessary or might conflict with Prettier.
  eslintConfigPrettier,
);
