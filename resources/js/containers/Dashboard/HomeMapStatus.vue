<template>
<b-row>
    <b-colxx sm="12">
        <map-chart
            :countryData="{'US': 4, 'CA': 7, 'GB': 8, 'IE': 14, 'ES': 21}"
            highColor="#ff0000"
            lowColor="#aaaaaa"
            countryStrokeColor="#909090"
            defaultCountryFillColor="#dadada"
        />
    </b-colxx>
</b-row>

</template>

<script>
import MapChart from 'vue-map-chart'
export default {
    components: {
        MapChart
    }
}
</script>