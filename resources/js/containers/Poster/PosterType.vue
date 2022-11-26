<template>
    <div class="rounded mt-5 bordered text-center fit-content mx-auto p-4" style="background: #f9fafc">
        <h2 class="font-weight-bold">Welcome to the Poster Presentation Wizard</h2>
        <h4 class="font-medium my-3">To Begin, Choose your Layout View & Upload your Poster Files</h4>
        <div class="my-4 d-flex justify-content-end align-items-center">
            <div class="ml-auto mr-2 text-center layout-item">
                <h5 class="font-medium">Single View</h5>
                <div class="bg-white px-3 py-4 bordered rounded-1 cursor-pointer" :style="isActive(1)" @click="setType(1)">
                    <div class="single-view-layout layout"></div>
                </div>
            </div>
            <div class="mx-2 text-center layout-item">
                <h5 class="font-medium">Multi-View</h5>
                <div class="bg-white px-3 py-4 bordered rounded-1 cursor-pointer" :style="isActive(2)" @click="setType(2)">
                    <div class="multi-view-layout d-flex justify-content-end align-items-center">
                        <div class="mx-auto w-25 single-view-layout layout"></div>
                        <div class="mx-auto w-25 single-view-layout layout"></div>
                        <div class="mx-auto w-25 single-view-layout layout"></div>
                    </div>
                </div>
            </div>
            <div class="ml-2 mr-auto text-center layout-item">
                <h5 class="font-medium">Custom View</h5>
                <div class="bg-white px-3 py-4 bordered rounded-1 cursor-pointer" :style="isActive(3)" @click="setType(3)">
                    <div class="layout d-flex justify-content-end">
                        <div class="w-25 single-view-layout layout mx-auto"></div>
                        <div class="w-65 mx-auto position-relative">
                            <div class="w-100 single-view-layout mb-2" style="height: 56px"></div>
                            <div class="w-100 single-view-layout mt-2" style="height: 56px"></div>
                            <div class="custom-view"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" @click="goNext()">Upload Poster File</button>
    </div>
</template>

<script>
export default {
    props: ['goNext', 'viewType'],
    data() {
        return {
            active: null
        }
    },
    methods: {
        isActive(val) {
            if (this.viewType == val) {
                return {
                    'border': 'solid white',
                    'box-shadow': '0 0 10px 0'
                }
            }
        },
        setType(val) {
            this.$emit('setType', val)
        }
    }
}
</script>

<style scoped>
    .font-medium {
        font-weight: 600;
    }
    .fit-content {
        width: fit-content;
    }
    .bordered {
        border: 2px solid lightgray
    }
    .layout-item {
        width: 200px;
    }
    .rounded-1 {
        border-radius: 1rem
    }
    .layout {
        height: 120px;
    }
    .single-view-layout {
        background: #922c88;
    }
    .w-65 {
        width: 65%;
    }
    .custom-view {
        position: absolute;
        width: 60px;
        height: 90px;
        border-left: 8px solid white;
        top: 0;
        border-bottom: 8px solid white;
        right: 0;
        background: #922c88;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>