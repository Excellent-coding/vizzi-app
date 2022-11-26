<template>
    <router-view />
</template>

<script>
export default {
    layout: 'app',
    middleware: 'any'
}
</script>

<style>
    nav {
        width: 100%
    }
    .backHall {
        top: 5%;
        left: 2%;
        color: red;
        font-weight: 900;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        z-index: 2;
        box-shadow: 0 0 4px;
    }
    .booth-header {
        font-size: 1.5rem;
    }
    @media (min-width: 992px) {
        .w-md-60 {
            width: 60% !important;
        }
        .w-md-50 {
            width: 50% !important;
        }
    }
    @media (max-width: 991px) {
        .booth-header {
            font-size: 1rem;
        }
    }
    .area {
        background: rgba(0, 0, 0, .4);
    }
    .img-area {
        background: transparent;
    }
    .img-area button {
        display: none;
    }
    .img-area:hover {
        background: rgba(0, 0, 0, .4);
    }
    .img-area:hover button {
        display: block;
    }
    .align-center {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .header {
        min-width: 40px;
    }
    .header-btn {
        font-weight: 400;
        min-width: 64px;
        transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,border 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        line-height: 1.75;
        letter-spacing: 0.02857em;
        text-transform: capitalize;
        justify-content: center;
    }
    .del-btn {
        font-size: 1rem;
        color: #922c88;
        cursor: pointer;
        font-weight: 900;
    }
    .nav-pills .nav-link {
        border-radius: 8px;
    }
    .arrow-btn {
        bottom: 30px;
        z-index: 1;
        background: rgba(146, 44, 136, .8);
        padding: 8px;
        transform: rotate(0);
        border-radius: 8px;
        color: white;
        font-weight: 900;
        cursor: pointer;
        width: 60px;
        text-align: center;
        position: fixed;
    }
    .arrow-btn:hover {
        background: gray;
    }
    .type-text {
        padding: 2px 9px;
        top: 19px;
        font-size: 12px;
        border-radius: 0 40px 40px 0;
        text-transform: uppercase;
        font-weight: bold;
        color: white
    }
</style>