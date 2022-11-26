<template>
  <div class="container">
    <div class="row justify-content-center vh-100">
      <div class="col-6 mx-auto my-auto">
        <div class="card card-default">
          <div class="card-header">New Password</div>
          <div class="card-body">
            <ul v-if="errors">
              <li v-for="error in errors" v-bind:key="error">
                
              </li>
            </ul>
            <form autocomplete="off" @submit.prevent="resetPassword" method="post">
              <alert-error :form="form" :message="status" />
              <div class="form-group">
                  <label for="email">E-mail</label>
                  <input type="email" id="email" class="form-control" placeholder="user@example.com" v-model="form.email" required>
                  <has-error :form="form" field="email" />
              </div>
              <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" id="password" class="form-control" placeholder="" v-model="form.password" required>
                  <has-error :form="form" field="password" />
              </div>
              <div class="form-group">
                  <label for="password_confirmation">Confirm Password</label>
                  <input type="password" id="password_confirmation" class="form-control" placeholder="" v-model="form.password_confirmation" required>
                  <has-error :form="form" field="password_confirmation" />
              </div>
              <button type="submit" class="btn btn-primary">Update</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Form from 'vform'
import axios from 'axios'

export default {
  layout: 'auth',
  middleware: 'guest',

  data: () => ({
    errors: {},
    status: '',
    form: new Form({
        token: null,
        email: null,
        password: null,
        password_confirmation: null,
    })
  }),
  created() {
    this.form.email = this.$route.query.email;
  },
  
  methods: {
    async resetPassword() {
      this.form.token = this.$route.params.token;
      let self = this;
      
      this.form.post("/password/reset", {
          token: self.form.token,
          email: self.form.email,
          password: self.form.password,
          password_confirmation: self.form.password_confirmation
      })
      .then(async (result) => {
          console.log(result.data);
          window.location = '/auth/login'
      }).catch(function(error) {
          
          if (error.response.data.email != null) {
            self.status = error.response.data.email;
          } else if (error.response.data.errors.password != null) {
            self.status = error.response.data.errors.password[0];
          }

      });
      }
  }
}
</script>