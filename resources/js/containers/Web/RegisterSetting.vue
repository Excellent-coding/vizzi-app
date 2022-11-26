<template>
    <b-row>
        <b-colxx md="4" xs="12" class="mb-3">
            <b-card class="mb-4" title="Default">
                <b-form-group label="Email*">
                    <b-form-input type="email" />
                </b-form-group>
                <b-form-group label="Password*">
                    <b-form-input type="password" />
                </b-form-group>
            </b-card>
        </b-colxx>
        <b-colxx md="4" xs="12" class="mb-3">
            <b-card class="mb-4" title="Template">
                <b-form-group :label="getLabel(setting)" v-for="(setting, index) in settings" :key="index">
                    <b-form-input />
                </b-form-group>
            </b-card>
        </b-colxx>
        <b-colxx md="4" xs="12" class="mb-3">

        </b-colxx>
        <b-colxx xxs="12">
            <b-button variant="primary" @click="setRegister">Save</b-button>
        </b-colxx>
    </b-row>
</template>

<script>
export default {
    props: ['setRegister'],
    data() {
        return {
            settings: [
                {
                    label: 'Name',
                    required: true,
                    disabled: false
                },
                {
                    label: 'Phone',
                    required: true,
                    disabled: false
                },
                {
                    label: 'Address',
                    required: true,
                    disabled: false
                },
                {
                    label: 'Company',
                    required: true,
                    disabled: false
                },
                {
                    label: 'Zipcode',
                    required: true,
                    disabled: false
                },
            ],
            customSettings: []
        }
    },
    methods: {
        getLabel(setting) {
            var label = setting.label
            if (setting.required) {
                label += '*'
            }
            return label
        }
    }
}
</script>