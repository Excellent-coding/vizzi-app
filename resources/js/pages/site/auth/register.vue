<template>
<b-card class="auth-card" no-body>
    <div class="position-relative image-side">
        <p class="text-white h2">Welcome to VCS Portal</p>
        <p class="white mb-0">
            Please use your credentials to login.
        </p>
    </div>
    <div class="form-side text-center">
        <div class="d-inline-flex pb-5">
            <span class="logo-single ml-auto" />
            <div class="my-auto ml-4">
                <h2 style="letter-spacing: 8px; font-weight: bold">{{host}}</h2>
            </div>
        </div>
            <b-alert v-model="error" variant="danger" v-if="error">Email Incorrect</b-alert>
            <input type="hidden" v-model="$v.form.code.$model"/>
            <input type="hidden" v-model="$v.form.id.$model"/>
        <b-form-group label="Email" class="has-float-label mb-4" v-if="!next">
            <b-form-input type="text" v-model="$v.form.email.$model" :state="!$v.form.email.$error"/>
            <b-form-invalid-feedback v-if="!$v.form.email.required">Please enter your email address</b-form-invalid-feedback>
            <b-form-invalid-feedback v-else-if="!$v.form.email.email">Please enter a valid email address</b-form-invalid-feedback>
            <b-form-invalid-feedback v-else-if="!$v.form.email.minLength">Your email must be minimum 4 characters</b-form-invalid-feedback>
        </b-form-group>
        <b-form-group label="Password" class="has-float-label mb-4" v-else>
            <b-form-input type="password" v-model="form.password" @keyup="match = true"/>
            <b-form-invalid-feedback v-if="!match" style="display:block">Password doesnt match</b-form-invalid-feedback>
        </b-form-group>
        <label class="form-group has-float-label mb-4" v-if="next">
            <input type="password" class="form-control" v-model="form.password_confirmation" @keyup="match = true"/>
            <span>Confirm Password</span>
        </label>
        <div class="d-flex justify-content-end align-items-center" v-if="!next">
            <b-button type="button" variant="primary" size="lg" class="btn-shadow" @click="isNext()">Continue</b-button>
        </div>
        <div class="d-flex justify-content-end align-items-center" v-else>
            <b-button type="submit" variant="primary" size="lg" class="btn-shadow" @click="formSubmit">Register</b-button>
        </div>
    </div>
</b-card>
</template>

<script>
import Form from 'vform'
import axios from 'axios'
import { mapGetters } from 'vuex'
import timezoneList from '../../../constants/timezone'

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
        next: false,
        users: [],
        timezoneList,
        error: false
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
            },
            code: {
                required,
                minLength: minLength(4)
            },
            id: {
            }
        }
    },
    methods: {
        init() {
        },
        async formSubmit () {
            if (this.form.password_confirmation ===  this.form.password) {
                await this.form.post('/code-register')

                const { data } = await this.form.post('/login')

                this.$store.dispatch('auth/saveToken', {
                    token: data.token,
                    remember: this.remember
                })

                await this.$store.dispatch('auth/fetchUser')

                await this.$store.dispatch('site/setSite', {
                    userId: this.user.id,
                    userParent: this.user.parent,
                    domainId: this.user.domain_id
                })

                await this.$store.dispatch('chat/getContacts', {
                    userId: this.user.id,
                    siteId: this.siteId
                })

                var diff = 0
                if (this.user.user_timezone) {
                    this.timezoneList.forEach(listItem => {
                        listItem.utc.forEach(item => {
                            if (item == this.user.user_timezone) {
                                diff = 1 - listItem.offset
                            }
                        })
                    })
                }
                
                this.$store.dispatch('auth/setZone', {
                    diffZone: diff
                })

                if (this.user.role < 3) {
                    this.$router.push({ name: 'app.home' })
                } else if (this.user.role < 5) {
                    if (!this.user.booth_id) {
                        if (this.user.type == 0) {
                            this.$router.push({ name: 'wizard.booth.main' })
                        } else if (this.user.type == 1) {
                            this.$router.push({ name: 'wizard.sponsor.main' })
                        } else {
                            this.$router.push({ name: 'wizard.poster.main' })
                        }
                    } else {
                        if (this.user.type == 0) {
                            this.$router.push('/wizard/booth/edit/' + this.user.booth_id)
                        } else if (this.user.type == 1) {
                            this.$router.push('/wizard/sponsor/edit/' + this.user.booth_id)
                        } else {
                            this.$router.push('/wizard/poster/edit/' + this.user.booth_id)
                        }
                    }
                } else {
                    if (this.user.avatar == 'default.jpg') {
                        this.$router.push({ name: 'settings.profile' })
                    } else {
                        this.$router.push({ name: 'home' })
                    }
                }
            }
        },
        isNext() {
            let app = this;
            this.form.post('/code-check').then(async (data)  => {

                if (data.data.check == 0) {
                    app.error = true
                } else {
                    app.form.id = data.data.id;
                    app.next = true
                }
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
            siteId: 'site/getSiteId',
            user: 'auth/user'
        })
    },
    mounted() {
        this.form.code = this.$route.params.code
        this.init()
    }
}
</script>
