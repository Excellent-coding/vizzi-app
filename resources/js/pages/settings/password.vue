<template>
  <div>
    <b-row>
      <b-colxx xxs="12">
        <breadcrumb heading="Password" />
        <div class="separator mb-5"></div>
      </b-colxx>
    </b-row>
    <b-row>
      <b-colxx xxs="12">
        <b-card :title="$t('your_password')">
          <form @submit.prevent="update" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="$t('password_updated')" />

            <!-- Password -->
            <div class="form-group row">
              <label class="col-md-3 col-form-label text-md-right">{{ $t('new_password') }}</label>
              <div class="col-md-7">
                <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" type="password" name="password">
                <has-error :form="form" field="password" />
              </div>
            </div>

            <!-- Password Confirmation -->
            <div class="form-group row">
              <label class="col-md-3 col-form-label text-md-right">{{ $t('confirm_password') }}</label>
              <div class="col-md-7">
                <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" class="form-control" type="password" name="password_confirmation">
                <has-error :form="form" field="password_confirmation" />
              </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group row">
              <div class="col-md-9 ml-md-auto">
                <v-button :loading="form.busy" type="success">
                  {{ $t('update') }}
                </v-button>
              </div>
            </div>
          </form>
        </b-card>
      </b-colxx>
    </b-row>
  </div>
</template>

<script>
import Form from 'vform'

export default {
  scrollToTop: false,
  metaInfo () {
      return { title: `Password Setting` }
  },
  data: () => ({
    form: new Form({
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async update () {
      await this.form.patch('settings/password')

      this.form.reset()
    }
  }
}
</script>
