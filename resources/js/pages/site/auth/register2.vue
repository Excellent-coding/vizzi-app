<template>
  <b-card class="auth-container mx-auto" no-body v-if="isShow">
    <b-container>
      <template v-if="fields.length">
        <b-row class="row-event-logo p-5">
          <b-col class="event-logo">
            <img
              class="logo"
              src="https://iaphs.vizzi.live/data/230537137.png"
              alt="EVENT LOGO PLACEHOLDER"
            />
          </b-col>
        </b-row>

        <b-form
          @submit.prevent="formSubmit"
          class="form-container av-tooltip tooltip-label-bottom p-5"
        >
          <p class="form-label mb-2">Event Registration</p>
          <p class="form-label mb-5" v-if="domain.title">{{ domain.title }}</p>

          <b-row>
            <template v-for="(field, index) in fields">
              <b-colxx md="6" sm="12" :key="field.id">
                <b-form-group class="mb-3">
                  <b-form-input
                    type="text"
                    class="form-input"
                    :placeholder="'Enter ' + getLabel(field) + '...'"
                    v-model="form[field.label]"
                    v-if="field.label != 'password'"
                  />
                  <template v-else>
                    <b-form-input
                      type="password"
                      class="form-input"
                      placeholder="Enter Password..."
                      v-model="form.password"
                    />
                    <b-form-invalid-feedback v-if="!match" style="display:block"
                      >Password doesnt match</b-form-invalid-feedback
                    >
                  </template>
                </b-form-group>
              </b-colxx>
              <b-colxx
                md="6"
                sm="12"
                v-if="field.label == 'password'"
                :key="index"
              >
                <b-form-input
                  type="password"
                  class="form-input"
                  placeholder="Confirm Password..."
                  v-model="form.password_confirmation"
                  @keyup="match = true"
                />
              </b-colxx>
            </template>
          </b-row>
          <div class="d-flex justify-content-end align-items-center">
            <b-button
              type="submit"
              variant="primary"
              size="lg"
              class="btn-shadow"
              >Register</b-button
            >
          </div>
        </b-form>
      </template>
      <template v-else>
        <b-row class="row-event-logo p-5">
          <b-col class="event-logo">
            <router-link tag="a" to="/">
              <img
                class="logo"
                src="https://iaphs.vizzi.live/data/230537137.png"
                alt="EVENT LOGO PLACEHOLDER"
              />
            </router-link>
          </b-col>
        </b-row>
        <b-form
          @submit.prevent="formSubmit"
          class="form-container av-tooltip tooltip-label-bottom p-5"
        >
          <p class="form-label mb-2">Event Registration</p>
          <p class="form-label mb-5" v-if="domain.title">{{ domain.title }}</p>

			<input type="hidden" name="code" v-model="this.$route.params.code" />
          <b-form-group class="mb-3">
            <b-form-input
              type="password"
              class="form-input"
              placeholder="Enter Password..."
              v-model="form.password"
              @keyup="match = true"
            />
            <b-form-invalid-feedback v-if="!match" style="display:block"
              >Password doesnt match</b-form-invalid-feedback
            >
          </b-form-group>
          <b-form-group class="mb-3">
            <b-form-input
              type="password"
              class="form-input"
              placeholder="Confirm Password..."
              v-model="form.password_confirmation"
              @keyup="match = true"
            />
          </b-form-group>
          <div class="d-flex justify-content-center align-items-center">
            <b-button
              type="submit"
              variant="primary"
              size="lg"
              class="btn-shadow"
              >Set Password</b-button
            >
          </div>
        </b-form>
      </template>
    </b-container>
  </b-card>
</template>
<script>
import Form from 'vform';
import Axios from 'axios';
import { mapGetters } from 'vuex';
import timezoneList from '../../../constants/timezone';

import { validationMixin } from 'vuelidate';

const {
  required,
  maxLength,
  minLength,
  email,
} = require('vuelidate/lib/validators');

export default {
  layout: 'auth',
  middleware: 'guest',
  metaInfo() {
    return { title: `Register` };
  },
  data: () => ({
    form: null,
    match: true,
    isShow: false,
    fields: [],
    timezoneList,
  }),
  mixins: [validationMixin],
  validations: {
    form: {
      password: {
        required,
        maxLength: maxLength(16),
        minLength: minLength(6),
      }
    },
  },
  computed: {
    host() {
      return window.location.host.split('.')[0];
    },
  },
  mounted() {
    Axios.post('site/host', { domain: this.host }).then(res => {
      this.domain = res.data.domain;
      this.id = this.domain.id;
      this.fields = res.data.fields;
      if (this.fields.length) {
        var formData = {
          id: '',
          password_confirmation: '',
        };
        this.fields.forEach(data => {
          formData[data.label] = '';
        });
        this.form = formData;
      } else {
        this.form = new Form({
			code: '',
          password: '',
          password_confirmation: '',
          id: 0,
        });
      }
      this.isShow = true;
    });
  },
  methods: {
    getLabel(setting) {
      var label = setting.label;
      if (setting.required) {
        label += '*';
      }
      return label.charAt(0).toUpperCase() + label.slice(1);
    },
    async formSubmit() {
      if (this.form.password_confirmation === this.form.password) {
        this.form.id = this.id;
        if (this.fields.length) {
          var flag = false;
          this.fields.forEach(field => {
            if (field.required && this.form[field.label] == '') {
              flag = true;
            }
          });
          if (flag) {
            this.$notify('primary filled', '', 'Please Fill the Forms!', {
              duration: 3000,
              permanent: false,
            });
          } else {
            const res = await Axios.post('/register', this.form);
            this.remember = true;
            this.$store.dispatch('auth/saveToken', {
              token: res.data.user.token,
              remember: this.remember,
            });
            await this.$store.dispatch('site/saveSite', res.data.site);
            await this.$store.dispatch('auth/setUser', res.data.user);
            this.$router.push('/');
          }
        } else {
          let {data} = await Axios.post('/register', {
			  code: this.$route.params.code,
			  password: this.form.password
		  });
			let user = data.user

		  if (data.status !== 200) {
            this.mustVerifyEmail = true;
          } else if(data.status === 200) {
			let { data } = await Axios.post('/login', {
				email: user.email,
				password: this.form.password
			});

            this.$store.dispatch('auth/saveToken', {
              token: data.token,
              remember: this.remember,
            });

            await this.$store.dispatch('auth/fetchUser');

            var diff = 0;
            if (this.user.user_timezone) {
              this.timezoneList.forEach(listItem => {
                listItem.utc.forEach(item => {
                  if (item == this.user.user_timezone) {
                    diff = 1 - listItem.offset;
                  }
                });
              });
            }

            this.$store.dispatch('auth/setZone', {
              diffZone: diff,
            });
            this.$router.push('/settings/profile');
          }
        }
      } else {
        this.match = false;
      }
    },
  },
  computed: {
    ...mapGetters({
      siteId: 'site/getSiteId',
      user: 'auth/user',
    }),
    host() {
      return window.location.host.split('.')[0];
    },
  }
};
</script>

<style lang="scss">
// input[type='file'] {
//   width: 0.1px;
//   height: 0.1px;
//   opacity: 0;
//   position: absolute;
// }
// .logo {
//   width: 110px;
//   height: 100px;
//   background-position: center center;
//   display: inline-block;
//   background-size: cover;
//   margin-bottom: 0;
// }
// .col-form-label {
//   text-transform: capitalize;
// }
.auth-container {
  max-width: 30rem;
  
  .logo {
		width: 250px !important;
	}
  
  .row-event-logo {
    .event-logo {
      background-color: #f1f2f3;
      padding: 2em 0px;
    }
  }
  .form-container {
	  padding-top: 0px !important;
    .form-label {
      font-size: 1.25rem;
      color: #afb6bd;
    }
    .form-input {
      height: calc(1.5em + 1rem + 2px);
      padding: 0.5rem 1rem;
      font-size: 1rem;
      line-height: 1.5;
      border-radius: 0.3rem;
    }

    .btn-primary {
      background-color: #353d47;
      border-color: #353d47;
    }
  }
}
</style>
