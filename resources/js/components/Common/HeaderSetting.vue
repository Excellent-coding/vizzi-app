<template>
    <div>
        <b-row>
            <b-colxx xxs="12" class="header text-center">
                <div class="d-inline-flex py-2">
                    <div class="header-btn mx-1" v-for="(item, index) in items" :key="index">{{item.title}}</div>
                </div>
            </b-colxx>
        </b-row>

        <b-row>
            <b-colxx xs="6" md="12">
                <b-form-group label="Header Color">
                    <b-form-input type="color" name="" />
                </b-form-group>
            </b-colxx>
            <b-colxx xs="6" md="12">
            </b-colxx>
        </b-row>
    </div>
</template>

<script>
export default {
    name: "HeaderSetting",
    props: [
        'submit',
        'items',
        'style'
    ],
    data() {
        return {
        }
    }
}
</script>