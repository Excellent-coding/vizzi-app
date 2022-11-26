<template>
<div class="text-center">
<span class="logo-single ml-auto" />
<b-card class="auth-card col-md-6 text-center mx-auto" no-body>
    <div class="form-side text-center">

        <div class="d-inline-flex pb-5">
            <div class="my-auto ml-4">
                <h3>Virtual Event Experience</h3>
            </div>
        </div>
        <b-form @submit.prevent="formSubmit" class="av-tooltip tooltip-label-bottom">
            <input v-model="form.user_timezone_region" type="hidden" name="user_timezone_region" id="user_timezone_region">
            <input v-model="form.user_timezone" type="hidden" name="user_timezone" id="user_timezone">
            <b-form-group label="Email" class="has-float-label mb-4">
                <b-form-input type="text" v-model="$v.form.email.$model" :state="!$v.form.email.$error" @input="whiteSpace"/>
                <b-form-invalid-feedback v-if="!$v.form.email.required">Please enter your email address</b-form-invalid-feedback>
                <b-form-invalid-feedback v-else-if="!$v.form.email.email">Please enter a valid email address</b-form-invalid-feedback>
                <b-form-invalid-feedback v-else-if="!$v.form.email.minLength">Your email must be minimum 4 characters</b-form-invalid-feedback>
            </b-form-group>

            <b-form-group label="Name" class="has-float-label mb-4" v-if="registerMode">
                <b-form-input type="text" v-model="$v.form.name.$model" :state="!$v.form.name.$error"/>
                <b-form-invalid-feedback v-if="!$v.form.name.required">Please enter your name </b-form-invalid-feedback>
            </b-form-group>

            <input type="hidden" v-model="$v.form.password.$model" name="password" value="medweek"/>
            
            <div class="d-flex justify-content-between align-items-center">
                <a variant="secondary" size="lg" @click="register()" id="register">Register</a>
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
</div>
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
            password: "medweek",
            user_timezone_region: '',
            user_timezone: ''
        }),
        remember: false,
        registerMode: false
    }),
      created () {
        let app = this;

        $("#forgot-password").click(function (e) {
            document.location.href = $(this).attr("href");
            e.preventDefault();
        })

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
            email: {
                required,
                email,
                minLength: minLength(4)
            },
            name: {
                required
            },
            password: {
                
            }
        }
    },
    methods: {
        whiteSpace (){
            var str = this.form.email;
            str.toLowerCase();
            str = str.replace(/[^\w@\.]/g, '');

            this.form.email = str;

            this.$v.$touch();
            return true;
        },
        selectTz(region, timezone, auto) {
          this.form.user_timezone_region = region;
          if (timezone) {
              this.form.user_timezone = timezone;
            }
        },
        async formSubmit() {
            this.$v.$touch();
            this.$v.form.$touch();

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

                    app.$router.push({ name: 'app.home' })

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
                            app.$router.push({ name: 'app.home' })
                        }
                    }


                }).catch(error => {
                    if (error.response.data == 'Already authenticated.') {
                        app.$router.push({ name: 'app.home' })
                    } else {
                        app.registerMode = true
                    }
                })
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

.auth-card .form-side {
    width: 100%;
}

.logo-single {
    width: 460px;
    height: 460px;
    background: url(/assets/img/medweek-logo-login.png) no-repeat;
    background-position: center center;
    display: fixed;
    top: 55px;
    background-size: fit;
    margin-bottom: 0;
}

</style>