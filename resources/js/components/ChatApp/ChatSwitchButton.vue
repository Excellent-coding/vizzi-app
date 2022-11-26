<template>
    <div>
        <span :id="id"> <slot></slot> </span>
        <button
                role="switch"
                :aria-checked="isChecked.toString()"
                :aria-labelledby="id"
                @click="toggle"
        >
            <span title="Individual Session Chat" >{{ onLabel }}</span> <span title="Networking Event Chat" >{{ offLabel }}</span>
        </button>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                id: null,
                isChecked: this.value
            };
        },
        props: {
            value: {
                type: Boolean,
                default: true
            },
            onLabel: {
                type: String,
                default: "on"
            },
            offLabel: {
                type: String,
                default: "off"
            }
        },
        methods: {
            toggle() {
                this.$emit("click", !this.isChecked);
                this.isChecked = !this.isChecked;
            }
        },
        mounted() {
            this.id = Math.random()
                .toString(36)
                .substring(2, 15);
        }
    };
</script>

<style scoped>
    button {
        padding: 0.2rem 0.4rem;
        border: 0.1rem solid;
        border-radius: 1.9rem;
        font: inherit;
    }

    [aria-checked] span {
        font-size: 0.9rem;
        font-weight: bold;
        display: inline-block;
        border-radius: 0.2rem;
        padding: 0.02rem 1.2rem;
    }

    [aria-checked="true"] :first-child,
    [aria-checked="false"] :last-child {
        background-color: #fff;
        border: 2px solid transparent;
        color: #000;
        border-radius: 0.9rem;
        box-shadow: 2px 2px 2px #aaaaaa;
    }
</style>
