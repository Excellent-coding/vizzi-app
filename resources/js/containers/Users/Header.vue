<template>
    <div class="mb-2">
        <h1>{{ title }}</h1>
        <div class="top-right-button-container d-flex">
            <b-button
                v-b-modal.userModal
                variant="primary"
                size="lg"
                class="top-right-button mr-1"
            >{{ $t('add-new') }}</b-button>
            <b-button-group>
                <b-dropdown
                    split
                    right
                    @click="selectAll(true)"
                    class="check-button"
                    variant="primary"
                >
                    <label
                        class="custom-control custom-checkbox pl-4 mb-0 d-inline-block"
                        slot="button-content"
                    >
                    <input
                        class="custom-control-input"
                        type="checkbox"
                        :checked="isSelectedAll()"
                        v-shortkey="{select: ['ctrl','a'], undo: ['ctrl','d']}"
                        @shortkey="keymap"
                    />
                    <span
                        :class="{
                            'custom-control-label' :true,
                            'indeterminate' : isAnyItemSelected()
                        }"
                    >&nbsp;</span>
                    </label>
                    <b-dropdown-item v-for="action in actions" :key="action.value" @click="setStatus(action.value)">{{action.text}}</b-dropdown-item>
                </b-dropdown>
            </b-button-group>
        </div>
        <breadcrumb />
    </div>
</template>

<script>
export default {
    props: ['selectAll', 'isSelectedAll', 'isAnyItemSelected', 'actions', 'setStatus', 'keymap', 'title']
}
</script>