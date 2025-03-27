> In the context of Vue applications, a "composable" is a function that leverages Vue Composition API to encapsulate and reuse stateful logic. ([docs](https://vuejs.org/guide/reusability/composables.html#what-is-a-composable))

For performance reasons, [composables replace renderless components](https://vuejs.org/guide/reusability/composables.html#vs-renderless-components).

- **/shared**  
  Reusable, granular stateful logic.

- **/scoped**  
  Component-scoped stateful logic.  
  Subdirectories are named after the route.
