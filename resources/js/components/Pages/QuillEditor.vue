<template>
    <b-card class="mb-2" title="Text Editor">
        <quill-editor
            ref="editor"
            :options="editorOption"
            @change="onEditorChange($event)"
        >
        </quill-editor>
    </b-card>
</template>

<script>
import { quillEditor } from 'vue-quill-editor'

import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

export default {
    components: {
        "quill-editor" : quillEditor
    },
    name: "QuillEditor",
    props: [
        'onEditorChange'
    ],
    data() {
        return {
            editorOption: {
                placeholder: '',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike', 'blockquote'],
                        [
                            { list: 'ordered' },
                            { list: 'bullet' },
                            { indent: '-1' },
                            { indent: '+1' }
                        ],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            },
        }
    },
    methods: {

    }
}
</script>