<template>
<b-row class="vh-100">
    <b-colxx xxs="12" md="10" class="mx-auto my-auto">
        <b-card class="auth-card" no-body>
            <div class="position-relative image-side">
                <p class="text-white h2" style="font-weight: 300">Welcome to </p><p class="text-white h2"> Vizzi.Live</p>
                <p class="white mb-0">{{text}}</p>
            </div>
            <div class="form-side text-center">
                <div class="d-inline-flex pb-5">
                    <span class="logo-single ml-auto" />
                    <div class="my-auto ml-4">
                        <h2 style="letter-spacing: 8px; font-weight: bold">VIRTUAL</h2>
                        <h3>Creative Studios</h3>
                    </div>
                </div>
                <b-form-group label="Email" class="has-float-label mb-4" v-if="!next">
            <b-form-input type="text" v-model="$v.form.code.$model"/>
                    <b-form-input type="text" v-model="$v.form.email.$model" :state="!$v.form.email.$error"/>
                    <b-form-invalid-feedback v-if="!$v.form.email.required">Please enter your email address</b-form-invalid-feedback>
                    <b-form-invalid-feedback v-else-if="!$v.form.email.email">Please enter a valid email address</b-form-invalid-feedback>
                    <b-form-invalid-feedback v-else-if="!$v.form.email.minLength">Your email must be minimum 4 characters</b-form-invalid-feedback>
                </b-form-group>
                <b-form-group label="Password" class="has-float-label mb-4" v-if="next">
                    <b-form-input type="password" v-model="form.password" @keyup="match = true"/>
                    <b-form-invalid-feedback v-if="!match" style="display:block">Password doesnt match</b-form-invalid-feedback>
                </b-form-group>
                <label class="form-group has-float-label mb-4" v-if="next">
                    <input type="password" class="form-control" v-model="form.password_confirmation" @keyup="match = true"/>
                    <span>Confirm Password</span>
                </label>
                <div class="d-flex justify-content-end align-items-center" v-if="!next">
                    <b-button type="button" variant="primary" size="lg" class="btn-shadow" @click="isNext()">xxxContinue</b-button>
                </div>
                <div class="d-flex justify-content-end align-items-center" v-else>
                    <b-button type="submit" variant="primary" size="lg" class="btn-shadow" @click="formSubmit">Register</b-button>
                </div>
            </div>
        </b-card>
    </b-colxx>
</b-row>
</template>

<script>
import Form from 'vform'
import axios from 'axios'
import { mapGetters } from 'vuex'

import {
    validationMixin
} from "vuelidate";

const {
    required,
    maxLength,
    minLength,
    email
} = require("vuelidate/lib/validators");

export default {
    layout: 'auth',
    middleware: 'guest',
    metaInfo () {
        return { title: `Register` }
    },
    data: () => ({
        form: new Form({
            id: '',
            code: '',
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        }),
        match: true,
        mustVerifyEmail: false,
        code: '',
        next: false,
        user: {},
        text: 'Please enter your email.'
    }),
    mixins: [validationMixin],
    validations: {
        form: {
            password: {
            required,
            maxLength: maxLength(16),
            minLength: minLength(6)
            },
            email: {
            required,
            email,
            minLength: minLength(4)
            }
        }
    },
    methods: {
        init() {
            axios.post('/getAdmin', {code: this.code}).then(res => {
                this.users = res.data
            })
        },
        async formSubmit () {
            this.form.id = this.user.id
            if (this.form.password_confirmation ===  this.form.password) {
                const { data } = await this.form.post('/code-register')

                const { data: { token } } = await this.form.post('/login')

                this.$store.dispatch('auth/saveToken', { token })

                await this.$store.dispatch('auth/updateUser', { user: data })

                if (this.currentUser.role == 1) {
                    this.$router.push({ name: 'app.home' })
                } else if (this.currentUser.role == 2) {
                    this.$router.push({ name: 'web.domain' })
                }
            }
        },
        isNext() {
            this.form.post('/code-check').then(async (data)  => {

            }).catch(function (e) {
                app.error = true
            })
        }
    },
    computed: {
        host() {
            return window.location.host.split('.')[0]
        },
        ...mapGetters({
            currentUser: "auth/user"
        })
    },
    mounted() {
        this.form.code = this.$route.params.code
        this.init()
    }
}
</script>
