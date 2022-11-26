<template>
  <b-row class="vh-100">
    <b-colxx xxs="12" md=10  class="mx-auto my-auto">
      <b-card class="auth-card" no-body>
          <div class="position-relative image-side ">
            <p class=" text-white h2">Welcome to VCS Portal</p>
            <p class="white mb-0">  Please use this form to register. <br />If you are a member, please
              <router-link tag="a" to="/login" class="white">login</router-link>.
            </p>
          </div>
          <div class="form-side text-center">
            <div class="d-inline-flex pb-5">
                <router-link tag="a" to="/">
                    <span class="logo-single" />
                </router-link>
                <div class="my-auto ml-4">
                    <h2 style="letter-spacing: 8px; font-weight: bold">VIRTUAL</h2>
                    <h5>Creative Studios</h5>
                </div>
            </div>
            <b-form @submit.prevent="formSubmit">
              <b-form-group label="Password" class="has-float-label mb-4">
                <b-form-input type="password" v-model="form.password" @keyup="match = true"/>
                <b-form-invalid-feedback v-if="!match" style="display:block">Password doesnt match</b-form-invalid-feedback>
              </b-form-group>
              <label class="form-group has-float-label mb-4">
                <input type="password" class="form-control" v-model="form.password_confirmation" @keyup="match = true"/>
                <span>Confirm Password</span>
              </label>
              <div class="d-flex justify-content-end align-items-center">
                  <b-button type="submit" variant="primary" size="lg" class="btn-shadow">Set Password</b-button>
              </div>
            </b-form>
          </div>
      </b-card>
    </b-colxx>
  </b-row>
</template>
<script>
import Form from 'vform'
import axios from 'axios'

import {
    validationMixin
} from "vuelidate";
import Axios from 'axios';

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
      password: '',
      password_confirmation: ''
    }),
    match: true,
  }),
  mixins: [validationMixin],
  validations: {
    form: {
        password: {
          required,
          maxLength: maxLength(16),
          minLength: minLength(6)
        },
    }
  },
  methods: {
    async formSubmit () {
      if (this.form.password_confirmation ===  this.form.password) {
          const { data } = await this.form.post('/register')

          if (data.status) {
            this.mustVerifyEmail = true
          } else {
            const { data: { token } } = await this.form.post('/login')

            this.$store.dispatch('auth/saveToken', { token })

            await this.$store.dispatch('auth/updateUser', { user: data })

            // this.$router.push({ name: 'app.home' })
          }
      } else {
        this.match = false
      }
    },
    onFileChange(e) {
        var files = e.target.files || e.dataTransfer.files;
        if (!files.length) return;
        this.createInput(files[0]);
    },
    createInput(file) {
        var reader = new FileReader();
        reader.onload = (e) => {
          var data = new Uint8Array(e.target.result);
          var workbook = XLSX.read(data, {type: 'array'});
          let sheetName = workbook.SheetNames[0]
          let worksheet = workbook.Sheets[sheetName];
          this.submit(worksheet);
        }
        reader.readAsArrayBuffer(file);
    },
    submit(file) {
      let no = 1
      let adminData = {}
      while(file['B' + no]) {
        if (file['D' + no]) {
          adminData[file['B' + no].v.toLowerCase()] = file['D' + no].v
        }
        no++
      }

      let userData = []
      no = no + 1
      const field_1 = file['C' + no].v.toLowerCase()
      const field_2 = file['D' + no].v.toLowerCase()
      const field_3 = file['E' + no].v.toLowerCase()
      no = no + 1
      while(file['C' + no]) {
        let data = {}
        data[field_1] = file['C' + no].v
        data[field_2] = file['D' + no].v
        data[field_3] = file['E' + no].v
        userData.push(data)
        no++
      }
      axios.post('/csv-register', {adminData: adminData, userData: userData}).then(res => {
        if (res.statusText == 'OK') {
          this.$notify('primary filled', 'System is reviewing your data!', ' Once approved, the server will send to you success email', { duration: 10000, permanent: false })
        } else {
          this.$notify('primary filled', 'Sorry!', 'Something went wrong!, please try again', { duration: 3000, permanent: false })
        }
      })
    }
  }
}
</script>

<style lang="css">
  input[type="file"] {
    width: .1px;
    height: .1px;
    opacity: 0;
    position: absolute;
  }
</style>
