import pluginVue from 'eslint-plugin-vue';

export default [
    // This includes "flat/essential" and "flat/strongly-recommended".
    ...pluginVue.configs['flat/recommended'],
    {
        rules: {
            "vue/block-order": [
                "error",
                {
                    "order": [
                        "template",
                        "script",
                        "style",
                    ],
                },
            ],
        },
    },
];
