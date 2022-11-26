<template>
  <b-card class="auth-container mx-auto" no-body>
    <b-container>
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
        <p class="form-label mb-5">Enter Virtual Experience</p>
        <b-alert v-model="error" variant="danger" v-if="error"
          >Login Incorrect</b-alert
        >
        <input
          v-model="form.user_timezone_region"
          type="hidden"
          name="user_timezone_region"
          id="user_timezone_region"
        />
        <input
          v-model="form.user_timezone"
          type="hidden"
          name="user_timezone"
          id="user_timezone"
        />
        <b-form-group class="mb-3">
          <b-form-input
            type="text"
            class="form-input"
            placeholder="Enter Email..."
            v-model="$v.form.email.$model"
            :state="!$v.form.email.$error"
          />
          <b-form-invalid-feedback v-if="!$v.form.email.required"
            >Please enter your email address</b-form-invalid-feedback
          >
          <b-form-invalid-feedback v-else-if="!$v.form.email.email"
            >Please enter a valid email address</b-form-invalid-feedback
          >
          <b-form-invalid-feedback v-else-if="!$v.form.email.minLength"
            >Your email must be minimum 4 characters</b-form-invalid-feedback
          >
        </b-form-group>

        <b-form-group class="mb-3">
          <b-form-input
            type="password"
            placeholder="Enter Password"
            class="form-input"
            v-model="$v.form.password.$model"
            :state="!$v.form.password.$error"
          />
          <b-form-invalid-feedback v-if="!$v.form.password.required"
            >Please enter your password</b-form-invalid-feedback
          >
          <b-form-invalid-feedback
            v-else-if="
              !$v.form.password.minLength || !$v.form.password.maxLength
            "
            >Your password must be between 6 and 16
            characters</b-form-invalid-feedback
          >
        </b-form-group>
        <b-row>
          <b-col class="pb-xs-2">
            <b-button
              block
              type="button"
              href="/forgot-password"
              variant="primary"
              size="lg"
              :class="{ 'btn-multiple-state btn-shadow': true }"
            >
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
              <span class="label">Reset</span>
            </b-button>
          </b-col>
          <b-col class="pb-xs-2">
            <b-button
              block
              type="submit"
              variant="primary"
              size="lg"
              :class="{ 'btn-multiple-state btn-shadow': true }"
            >
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
          </b-col>
        </b-row>
      </b-form>
      <div id="user_edit_timezone"></div>
    </b-container>
  </b-card>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";

import { validationMixin } from "vuelidate";

const {
  required,
  maxLength,
  minLength,
  email,
} = require("vuelidate/lib/validators");

export default {
  layout: "auth",
  middleware: "guest",

  metaInfo() {
    return { title: `Login` };
  },

  data: () => ({
    form: new Form({
      email: "",
      password: "",
      user_timezone_region: "",
      user_timezone: "",
    }),
    remember: false,
    error: false,
  }),

  mounted() {
    $.get(
      "/timezones",
      (tzData) => {
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const [region] = timezone.split("/");
        tzData.forEach((element) => {
          const tz = element.timezones.find((elementTz) => {
            return elementTz.labels.find((item) => {
              return item == timezone;
            });
          });

          if (tz) {
            this.form.user_timezone_region = element.id;
            this.form.user_timezone = element.label;
          }
        });
      },
      "json"
    );
  },

  mixins: [validationMixin],

  validations: {
    form: {
      password: {
        required,
        maxLength: maxLength(16),
        minLength: minLength(6),
      },
      email: {
        required,
        email,
        minLength: minLength(4),
      },
    },
  },

  methods: {
    async formSubmit() {
      this.$v.$touch();
      this.$v.form.$touch();
      if (!this.$v.form.$anyError) {
        let app = this;
        this.form
          .post("/login")
          .then(async (data) => {
            this.$store.dispatch("auth/saveToken", {
              token: data.data.token,
              remember: this.remember,
            });

            await this.$store.dispatch("auth/fetchUser");

            let user = this.user;

            await this.$store.dispatch("site/setSite", {
              userId: user.id,
              userParent: user.parent,
              domainId: user.domain_id,
            });

            await this.$store.dispatch("chat/getContacts", {
              userId: user.id,
              siteId: app.siteId,
            });

            if (user.role < 3) {
              app.$router.push({ name: "app.home" });
            } else if (user.role < 5) {
              if (!this.user.booth_id) {
                if (this.user.type == 0) {
                  this.$router.push({ name: "wizard.booth.main" });
                } else if (this.user.type == 1) {
                  this.$router.push({ name: "wizard.sponsor.main" });
                } else {
                  this.$router.push({ name: "wizard.poster.main" });
                }
              } else {
                if (this.user.type == 0) {
                  this.$router.push("/wizard/booth/edit/" + this.user.booth_id);
                } else if (this.user.type == 1) {
                  this.$router.push(
                    "/wizard/sponsor/edit/" + this.user.booth_id
                  );
                } else {
                  this.$router.push(
                    "/wizard/poster/edit/" + this.user.booth_id
                  );
                }
              }
            } else {
              if (user.avatar == "default.jpg") {
                app.$router.push({ name: "settings.profile" });
              } else {
                app.$router.push({ name: "home" });
              }
            }
          })
          .catch(function (e) {
            app.error = true;
          });
      }
    },
  },

  computed: {
    ...mapGetters({
      siteId: "site/getSiteId",
      user: "auth/user",
    }),
    host() {
      return window.location.host.split(".")[0];
    },
  },
};
</script>
<style lang="scss">
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

  #user_edit_timezone {
    display: none;
  }
}
</style>
