[Give a bit of context. What the app is used for, by who.]

# Tech Stack

Open Web Purge is a stateful single-page application.

Symfony is used to authenticate users with the OpenID Connect (OIDC) protocol, and as a JSON API that forwards incoming requests to the backend.

- Symfony
  - drenso/symfony-oidc-bundle
- Vue
  - Vue Router
  - Pinia
- Tailwind
- Docker

# Conventions

- PHP
  - [PER Coding Style](https://www.php-fig.org/per/coding-style/)
- Vue
  - Single-file components
    - `<template>` block before `<script>`
    - `<script setup>` syntax
    - [`<style module>` syntax](https://vuejs.org/api/sfc-css-features.html#css-modules) (CSS Modules) to avoid [leaks](https://github.com/vuejs/vue-loader/issues/957)
      - [camelCase for class names](https://github.com/css-modules/css-modules/blob/master/docs/naming.md)
    - Root element selector named after the component, e.g.  
      `MyComponent` ↓  
      ```vue
      <template>
        <div :class="$style.myComponent">
          <slot></slot>
        </div>
      </template>
      ```
  - Composition API
  - Vue [style guide](https://vuejs.org/style-guide)