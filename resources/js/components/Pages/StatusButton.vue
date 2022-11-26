<template>
    <b-button-group>
        <b-dropdown split right @click="selectAll(true)" class="check-button" variant="primary">
            <label
                class="custom-control custom-checkbox pl-4 mb-0 d-inline-block"
                slot="button-content"
            >
                <input
                    class="custom-control-input"
                    type="checkbox"
                    :checked="isSelectedAll"
                    v-shortkey="{select: ['ctrl','a'], undo: ['ctrl','d']}"
                    @shortkey="keymap"
                />
                <span
                    :class="{
                    'custom-control-label' :true,
                    'indeterminate' : isAnyItemSelected
                    }"
                >&nbsp;</span>
            </label>
            <b-dropdown-item @click="setStatus(1)">{{status[0]}}</b-dropdown-item>
            <b-dropdown-item @click="setStatus(0)">{{status[1]}}</b-dropdown-item>
            <b-dropdown-item @click="setStatus(2)">{{status[2]}}</b-dropdown-item>
            <b-dropdown-divider v-if="status.length > 3"/>
            <template v-for="(action, index) in status">
                <b-dropdown-item @click="setStatus(index)" v-if="index > 2" :key="index">{{action}}</b-dropdown-item>
            </template>
        </b-dropdown>
    </b-button-group>
</template>

<script>
export default {
    props: [
        'selectAll',
        'isSelectedAll',
        'keymap',
        'isAnyItemSelected',
        'setStatus',
        'status'
    ]
}
</script>