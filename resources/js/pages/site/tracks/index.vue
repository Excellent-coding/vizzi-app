<template>
    <router-view />
</template>

<script>
export default {
    layout: 'app',
    middleware: 'should'
}
</script>

<style>
    .manage-btn {
        font-size: 1rem;
        font-weight: bold;
    }
    .select-btn {
        background: #f4f6f8;
        border: 1px solid #61b9f9;
        font-size: 1rem;
        font-weight: 900;
        border-radius: 4px;
        padding: 8px 20px;
    }
    .select-class {
        background: #f4f6f8;
        border: 1px solid #61b9f9;
        font-size: 1rem;
        border-radius: 4px;
        margin-top: 16px;
        height: 42px;
        width: 360px;
    }
    .vue__time-picker input.display-time {
        background: #f4f6f8;
        border: 1px solid #61b9f9;
        font-size: 1rem;
        border-radius: 4px;
        height: 42px!important;
    }
    .vue__time-picker .dropdown, .time-picker, .vue__time-picker .dropdown .select-list, .vue__time-picker input.display-time {
        width: 160px!important;
    }
    .minutes::-webkit-scrollbar, .hours::-webkit-scrollbar {
        width: 8px;
    }
    .minutes::-webkit-scrollbar-track, .hours::-webkit-scrollbar-track {
        box-shadow: inset 0 0 5px grey; 
        border-radius: 4px;
    }
    .minutes::-webkit-scrollbar-thumb, .hours::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, .2); 
        border-radius: 4px;
    }
    .minutes::-webkit-scrollbar-thumb:hover, .hours::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, .4); 
    }
    .select-btn i {
        font-weight: 900;
        color: #ced0da;
    }
    .select-btn:hover {
        background: #f2f2f2;
    }
    .modal .modal-footer {
        border: none!important
    }
    .modal-dialog #agenda_modal___BV_modal_content_.modal-content {
        border-radius: 36px;
        border: 12px solid #823480
    }
    textarea.form-control {
        height: 70px!important;
    }
</style>