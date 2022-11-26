<template>
    <router-view />
</template>

<script>
export default {
    middleware: 'site-auth'
}
</script>

<style lang="css">
    img {
        width: 100%;
    }
    .border-rounded {
        border-radius: 1rem;
    }
    .cursor-pointer {
        cursor: pointer
    }
    .prevBooth {
        position: absolute;
        font-size: 15px;
        color: red;
        width: fit-content;
        top: 5%;
        background: white;
        padding: 0 12px;
        border-radius: 4px;
        font-weight: 900;
        cursor: pointer;
        left: 2%;
    }
    .nextBooth {
        position: absolute;
        font-size: 15px;
        color: red;
        width: fit-content;
        top: 5%;
        right: 2%;
        background: white;
        padding: 0 12px;
        border-radius: 4px;
        font-weight: 900;
        cursor: pointer;
    }
    .exhibit-header .header-btn {
        padding: 0 4px;
        font-weight: 400;
        transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,border 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        line-height: 1.75;
        letter-spacing: 0.02857em;
        text-transform: capitalize;
        box-shadow: 0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12);
    }
    .model {
        height: 24vh;
        object-fit: cover;
    }
</style>