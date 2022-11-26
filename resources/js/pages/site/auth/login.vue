<template>
<b-card class="auth-card" no-body>
    <div class="position-relative image-side">
        <p class="text-white h2" style="font-weight: 300">Welcome to</p>
        <p class="text-white h2">Vizzi.Live</p>
        <p class="white mb-0">
            Please use your credentials to login.
            <br />If you are not a member, please
            <router-link tag="a" to="/auth/register" class="white">register</router-link>.
        </p>
    </div>
    <div class="form-side text-center">

        <div class="d-inline-flex pb-5">
            <span class="logo-single ml-auto" />
            <div class="my-auto ml-4">
                <h2 style="letter-spacing: 8px; font-weight: bold; text-transform: uppercase">{{host}}</h2>
                <h3>Creative Studios</h3>
            </div>
        </div>
        <b-form @submit.prevent="formSubmit" class="av-tooltip tooltip-label-bottom">
            <b-alert v-model="error" variant="danger" v-if="error">Login Incorrect</b-alert>
            <input v-model="form.user_timezone_region" type="hidden" name="user_timezone_region" id="user_timezone_region">
            <input v-model="form.user_timezone" type="hidden" name="user_timezone" id="user_timezone">
            <b-form-group label="Email" class="has-float-label mb-4">
                <b-form-input type="text" v-model="$v.form.email.$model" :state="!$v.form.email.$error"/>
                <b-form-invalid-feedback v-if="!$v.form.email.required">Please enter your email address</b-form-invalid-feedback>
                <b-form-invalid-feedback v-else-if="!$v.form.email.email">Please enter a valid email address</b-form-invalid-feedback>
                <b-form-invalid-feedback v-else-if="!$v.form.email.minLength">Your email must be minimum 4 characters</b-form-invalid-feedback>
            </b-form-group>

            <b-form-group label="Password" class="has-float-label mb-4">
                <b-form-input type="password" v-model="$v.form.password.$model" :state="!$v.form.password.$error" />
                <b-form-invalid-feedback v-if="!$v.form.password.required">Please enter your password</b-form-invalid-feedback>
                <b-form-invalid-feedback v-else-if="!$v.form.password.minLength || !$v.form.password.maxLength">Your password must be between 6 and 16 characters</b-form-invalid-feedback>
            </b-form-group>
            <div class="d-flex justify-content-between align-items-center">
                <a href="/forgot-password">Forgot Password?</a>
                <b-button type="submit" variant="primary" size="lg" :class="{'btn-multiple-state btn-shadow': true}">
                    <span class="spinner d-inline-block">
                        <span class="bounce1"></span>
                        <span class="bounce2"></span>
                        <span class="bounce3"></span>
                    </span>
                    <span class="icon success">
                        <i class="simple-icon-check"></i>
                    </span>
                    <span class="icon fail">
                        <i class="simple-icon-exclamation"></i>
                    </span>
                    <span class="label">Login</span>
                </b-button>
            </div>
        </b-form>
    </div>
    <div id="user_edit_timezone"></div>
</b-card>
</template>

<script>
import Form from 'vform'
import { mapGetters } from "vuex"

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
        return { title: `Login` }
    },
    data: () => ({
        form: new Form({
            email: "",
            password: "",
            user_timezone_region: '',
            user_timezone: ''
        }),
        remember: false,
        error: false
    }),
    created () {
        let app = this;

    $.get('/timezones', function (tzData) { 

      $("#user_edit_timezone").timezoneWidget({
        data: tzData,
            onTimezoneSelect: function (region, timezone, auto) {
          app.form.user_timezone_region = region;
          if (timezone) {
              app.form.user_timezone = timezone;
            }
        },
            onRegionSelect: this.selectTz,
            guessUserTimezone: true
          });
        }, 'json');    
    },
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
        selectTz(region, timezone, auto) {
          this.form.user_timezone_region = region;
          if (timezone) {
              this.form.user_timezone = timezone;
            }
        },
        async formSubmit() {
            this.$v.$touch();
            this.$v.form.$touch();
            if (!this.$v.form.$anyError) {
                let app = this;
                this.form.post('/login').then(async (data)  => {
                    this.$store.dispatch('auth/saveToken', {
                        token: data.data.token,
                        remember: this.remember
                    })

                     await this.$store.dispatch('auth/fetchUser')

                    let user = this.user;
                     
                    await this.$store.dispatch('site/setSite', {
                        userId: user.id,
                        userParent: user.parent,
                        domainId: user.domain_id
                    })
 
                    await this.$store.dispatch('chat/getContacts', {
                        userId: user.id,
                        siteId: app.siteId
                    })

                    if (user.role < 3) {
                        app.$router.push({ name: 'app.home' })
                    } else if (user.role < 5) {
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
                        if (user.avatar == 'default.jpg') {
                            app.$router.push({ name: 'settings.profile' })
                        } else {
                            app.$router.push({ name: 'home' })
                        }
                    }


                }).catch(error => {
                    if (error.response.data == 'Already authenticated.') {
                        app.$router.push({ name: 'app.home' })
                    } else {
                        app.error = true
                    }
                })
            }
        }
    },  
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            user: "auth/user"
        }),
        host() {
            return window.location.host.split('.')[0]
        }
    }
};
</script>
<style>

#user_edit_timezone { display: none; }

</style>
