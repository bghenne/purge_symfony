All these components render HTML. Renderless components, which have stateful logic but no visual layout, are replaced by composables for performance reasons.

# Atomic

> If atoms are the basic building blocks of matter, then the **atoms of our interfaces serve as the foundational building blocks that comprise all our user interfaces**. These atoms include basic HTML elements like form labels, inputs, buttons, and others that can’t be broken down any further without ceasing to be functional.  ([Atomic Design Methodology](https://atomicdesign.bradfrost.com/chapter-2/))

Besides native HTML elements, atomic components include wrappers to library components, as long as the above definition holds true. (The purpose of wrapper components is to apply our own design system and to constrain the library components, i.e. make them opinionated.)

All atomic components are both _presentational_ (aka _dumb_) and _injector_ components: their responsability is to display data and emit changes over the component tree (through the [provide/inject](https://vuejs.org/guide/components/provide-inject.html) mechanism), so that an ancestor —the _provider_ component— can mutate the state accordingly.

# Compound

Compound components are components that are neither atoms or routes. They can be deeply nested in the component tree.

# Route

Route components match a URL path. The mapping is defined in _router.ts_.
