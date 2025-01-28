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
    - `<script setup>` syntax
    - [`<style module>` syntax](https://vuejs.org/api/sfc-css-features.html#css-modules) (CSS Modules) to avoid [leaks](https://github.com/vuejs/vue-loader/issues/957)
  - Composition API
  - Vue [style guide](https://vuejs.org/style-guide)