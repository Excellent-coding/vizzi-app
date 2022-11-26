<template>
    <router-view />
</template>

<style lang="css">
    .header {
        min-width: 40px;
    }
    .header .header-btn {
        padding: 13px 10px;
        font-weight: 400;
        min-width: 64px;
        transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,border 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        line-height: 1.75;
        letter-spacing: 0.02857em;
        text-transform: capitalize;
        box-shadow: 0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12);
    }
    .view-header {
        font-size: 1.4vw;
        background: rgba(255, 255, 255, .8);
        width: 36vw;
        border-radius: 1rem;
    }
    .single-view {
        width: 48vw;
        height: 24vw;
        border-radius: 2rem;
    }
    .item {
        width: 24vw;
        height: 24vw;
        border-radius: 1rem;
    }
    .position-center {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .object-fit {
        object-fit: contain;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .arrow-btn {
        bottom: 20px;
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
    .nav-pills .nav-link {
        border-radius: 8px;
    }
    .modal-body {
        overflow: visible;
    }
</style>