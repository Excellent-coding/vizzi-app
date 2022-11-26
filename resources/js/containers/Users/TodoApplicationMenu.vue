<template>
  <application-menu>
    <vue-perfect-scrollbar :settings="{ suppressScrollX: true, wheelPropagation: false }">
      <div class="p-4">
        <p class="text-muted text-small mb-3">{{$t('status')}}</p>
        <ul class="list-unstyled mb-4">
          <li class="nav-item">
            <a href="#">
              <i class="simple-icon-reload" />
              <span class="d-inline-block">{{$t('users.all-admin-users')}}</span>
              <span class="float-right">{{items.length}}</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#">
              <i class="simple-icon-refresh" />
              <span class="d-inline-block">{{$t('users.active-admin-users')}}</span>
              <span class="float-right">{{items.filter(x => x.status === 1).length}}</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#">
              <i class="simple-icon-check" />
              <span class="d-inline-block">{{$t('users.pending-admin-users')}}</span>
              <span class="float-right">{{items.filter(x => x.status === 0).length}}</span>
            </a>
          </li>
        </ul>
        <p class="text-muted text-small mb-1">{{$t('categories')}}</p>
        <ul class="list-unstyled mb-4">
          <b-form-radio-group
            stacked
            class="pt-2"
            label="Categories"
          >
            <b-form-radio v-model="selected" name="category" v-for="category in categories" :key="category">
              {{category}}
            </b-form-radio>
          </b-form-radio-group>
        </ul>
        <p class="text-muted text-small mb-3">{{$t('labels')}}</p>
        <div>
          <p class="d-sm-inline-block mb-1" v-for="(l,index) in labels" :key="index">
            <a href="#">
              <b-badge pill :variant="`outline-${l.color}`" class="mb-1 mr-1">{{l.label}}</b-badge>
            </a>
          </p>
        </div>
      </div>
    </vue-perfect-scrollbar>
  </application-menu>
</template>
<script>
import ApplicationMenu from "../../components/Common/ApplicationMenu";

const TodoApplicationMenu = {
  props: ["categories", "labels", "selected", "items"],
  components: {
    "application-menu": ApplicationMenu
  },
  mounted() {
    document.body.classList.add("right-menu");
  },
  beforeDestroy() {
    document.body.classList.remove("right-menu");
  }
};
export default TodoApplicationMenu;
</script>
